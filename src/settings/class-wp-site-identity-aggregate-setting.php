<?php
/**
 * WP_Site_Identity_Aggregate_Setting class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing an aggregate setting consisting of other settings.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Aggregate_Setting extends WP_Site_Identity_Setting implements WP_Site_Identity_Setting_Registry {

	/**
	 * Prefix to use for all setting names within WordPress.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Group to use for all settings within WordPress.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $group = '';

	/**
	 * Registered sub settings for the aggregate setting.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $settings = array();

	/**
	 * Factory to create setting objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 *
	 * Sets the setting properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                            $name     Setting name.
	 * @param array                             $args     {
	 *     Optional. Arguments for the setting.
	 *
	 *     @type string    $title        Title for the setting. Default empty string.
	 *     @type string    $description  Description for the setting. Default empty string.
	 *     @type bool      $show_in_rest Whether to show the setting in the REST API. Default false.
	 * }
	 * @param WP_Site_Identity_Setting_Registry $registry Optional. Parent registry for the setting.
	 */
	public function __construct( $name, array $args = array(), WP_Site_Identity_Setting_Registry $registry = null ) {
		$this->factory = new WP_Site_Identity_Setting_Factory( $this );

		parent::__construct( $name, $args, $registry );

		$this->prefix            = $this->registry->prefix( '' );
		$this->group             = $this->name;
		$this->type              = 'object';
		$this->validate_callback = array( $this, 'validate' );
		$this->sanitize_callback = array( $this, 'sanitize' );
		$this->default           = array();
		$this->rest_schema       = array(
			'properties' => array(),
		);
		$this->choices           = array();
		$this->format            = '';
		$this->min               = false;
		$this->max               = false;
	}

	/**
	 * Gets the current value for a sub setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return mixed Current setting value.
	 *
	 * @throws WP_Site_Identity_Setting_Not_Found_Exception Thrown when a setting cannot be found.
	 */
	public function get_value( $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			/* translators: %s: setting name */
			throw new WP_Site_Identity_Setting_Not_Found_Exception( sprintf( __( 'The setting with the name %s could not be found.', 'wp-site-identity' ), $name ) );
		}

		$data = $this->registry->get_value( $this->name );

		if ( ! isset( $data[ $name ] ) ) {
			return $this->settings[ $name ]->get_default();
		}

		return $data[ $name ];
	}

	/**
	 * Gets all registered sub settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$name => $instance` pairs.
	 */
	public function get_all_settings() {
		return $this->settings;
	}

	/**
	 * Gets a registered sub setting instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return WP_Site_Identity_Setting Registered setting.
	 *
	 * @throws WP_Site_Identity_Setting_Not_Found_Exception Thrown when a setting cannot be found.
	 */
	public function get_setting( $name ) {
		if ( ! isset( $this->settings[ $name ] ) ) {
			/* translators: %s: setting name */
			throw new WP_Site_Identity_Setting_Not_Found_Exception( sprintf( __( 'The setting with the name %s could not be found.', 'wp-site-identity' ), $name ) );
		}

		return $this->settings[ $name ];
	}

	/**
	 * Checks whether a sub setting is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return bool True if the setting is registered, false otherwise.
	 */
	public function has_setting( $name ) {
		return isset( $this->settings[ $name ] );
	}

	/**
	 * Registers a new sub setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to register.
	 */
	public function register_setting( WP_Site_Identity_Setting $setting ) {
		$name = $setting->get_name();

		$this->settings[ $name ] = $setting;

		// TODO: Make sure the REST Schema and default get updated in WordPress.
		$this->default[ $name ] = $setting->get_default();
		$this->rest_schema['properties'][ $name ] = $this->generate_schema_for_setting( $setting );
	}

	/**
	 * Unregisters an existing sub setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to unregister.
	 */
	public function unregister_setting( WP_Site_Identity_Setting $setting ) {
		$name = $setting->get_name();

		if ( ! isset( $this->settings[ $name ] ) ) {
			return;
		}

		// TODO: Make sure the REST Schema and default get updated in WordPress.
		unset( $this->default[ $name ] );
		unset( $this->rest_schema['properties'][ $name ] );

		unset( $this->settings[ $name ] );
	}

	/**
	 * Gets the factory to create setting objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Factory Factory to create setting objects.
	 */
	public function factory() {
		return $this->factory;
	}

	/**
	 * Gets the feedback handler for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Feedback_Handler Feedback handler to use for registered settings.
	 */
	public function feedback_handler() {
		return $this->registry->feedback_handler();
	}

	/**
	 * Gets the validator for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Validator Validator to use for registered settings.
	 */
	public function validator() {
		return $this->registry->validator();
	}

	/**
	 * Gets the sanitizer for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Sanitizer Sanitizer to use for registered settings.
	 */
	public function sanitizer() {
		return $this->registry->sanitizer();
	}

	/**
	 * Prefixes a setting name.
	 *
	 * Sub settings don't need to be prefixed, so this method just passes through the name.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting name to prefix.
	 * @return string Prefixed setting name.
	 */
	public function prefix( $name = '' ) {
		return $this->prefix . $name;
	}

	/**
	 * Gets the group to use for registered sub settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string Group identifier.
	 */
	public function group() {
		return $this->group;
	}

	/**
	 * Standard validation callback for an aggregate setting.
	 *
	 * It iterates through the sub-settings and validates them individually.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data to validate.
	 * @return array Validated data.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate( $data ) {
		if ( $data instanceof stdClass ) {
			$data = (array) $data;
		}

		if ( ! is_array( $data ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $data, 'object' ) );
		}

		$validated_data = array();
		$original_data = $this->registry->get_value( $this->name );

		$has_errors = false;
		$has_valid  = false;

		foreach ( $this->settings as $setting ) {
			$name  = $setting->get_name();
			$value = isset( $data[ $name ] ) ? $data[ $name ] : null;

			try {
				$validated_value = $this->registry->validator()->validate( $value, $setting );
				$has_valid = true;
			} catch ( WP_Site_Identity_Setting_Validation_Error_Exception $e ) {
				$this->registry->feedback_handler()->add_error( $setting, $e->getMessage() );

				if ( isset( $original_data[ $name ] ) ) {
					$validated_value = $original_data[ $name ];
				} else {
					$validated_value = $setting->get_default();
				}

				$has_error = true;
			}

			$validated_data[ $name ] = $validated_value;
		}

		if ( $has_error && $has_valid ) {
			$this->registry->feedback_handler()->add_success( $this, __( 'All remaining settings were successfully saved.', 'wp-site-identity' ) );
		}

		return $validated_data;
	}

	/**
	 * Standard sanitization callback for an aggregate setting.
	 *
	 * It iterates through the sub-settings and sanitizes them individually.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $data Data to sanitize.
	 * @return array Sanitized data.
	 */
	public function sanitize( $data ) {
		if ( ! is_array( $data ) ) {
			$data = (array) $data;
		}

		$sanitized_data = array();

		foreach ( $this->settings as $setting ) {
			$name  = $setting->get_name();
			$value = isset( $data[ $name ] ) ? $data[ $name ] : null;

			$sanitized_data[ $name ] = $this->registry->sanitizer()->sanitize( $value, $setting );
		}

		return $sanitized_data;
	}

	/**
	 * Generates the REST schema array for a sub setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Sub setting to generate the schema for.
	 * @return array Complete REST schema for the sub setting.
	 */
	protected function generate_schema_for_setting( $setting ) {
		$base_schema = array(
			'type'        => $setting->get_type(),
			'description' => $setting->get_description(),
			'default'     => $setting->get_default(),
		);

		$choices = $setting->get_choices();
		if ( ! empty( $choices ) ) {
			$base_schema['enum'] = array_keys( $choices );
		}

		$format = $setting->get_format();
		if ( ! empty( $format ) ) {
			$base_schema['format'] = $format;
		}

		if ( in_array( $base_schema['type'], array( 'integer', 'number' ), true ) ) {
			$min = $setting->get_min();
			$max = $setting->get_max();

			if ( is_float( $min ) || is_int( $min ) ) {
				$base_schema['minimum'] = $min;
			}

			if ( is_float( $max ) || is_int( $max ) ) {
				$base_schema['maximum'] = $max;
			}
		}

		return array_merge( $base_schema, $setting->get_rest_schema() );
	}
}
