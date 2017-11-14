<?php
/**
 * WP_Site_Identity_Setting_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering settings.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Setting_Registry implements WP_Site_Identity_Setting_Registry {

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
	 * All registered settings as `$name => $instance` pairs.
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
	 * Feedback handler to use for registered settings.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Feedback_Handler
	 */
	protected $feedback_handler;

	/**
	 * Validator to use for registered settings.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Validator
	 */
	protected $validator;

	/**
	 * Sanitizer to use for registered settings.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Sanitizer
	 */
	protected $sanitizer;

	/**
	 * Constructor.
	 *
	 * Sets the feedback handler to use for registered settings.
	 *
	 * @since 1.0.0
	 *
	 * @param string                                    $prefix           Prefix to use for all setting names within WordPress.
	 * @param string                                    $group            Group to use for all settings within WordPress.
	 * @param WP_Site_Identity_Setting_Feedback_Handler $feedback_handler Optional. Feedback handler to use.
	 * @param WP_Site_Identity_Setting_Validator        $validator        Optional. Validator to use.
	 * @param WP_Site_Identity_Setting_Sanitizer        $sanitizer        Optional. Sanitizer to use.
	 */
	public function __construct( $prefix, $group, WP_Site_Identity_Setting_Feedback_Handler $feedback_handler = null, WP_Site_Identity_Setting_Validator $validator = null, WP_Site_Identity_Setting_Sanitizer $sanitizer = null ) {
		$this->prefix = $prefix;
		$this->group  = $group;

		$this->factory = new WP_Site_Identity_Setting_Factory( $this );

		if ( $feedback_handler ) {
			$this->feedback_handler = $feedback_handler;
		} else {
			$this->feedback_handler = new WP_Site_Identity_Setting_Feedback_Handler();
		}

		if ( $validator ) {
			$this->validator = $validator;
		} else {
			$this->validator = new WP_Site_Identity_Setting_Validator();
		}

		if ( $sanitizer ) {
			$this->sanitizer = $sanitizer;
		} else {
			$this->sanitizer = new WP_Site_Identity_Setting_Sanitizer();
		}

		$this->feedback_handler->set_prefix( $this->prefix );
	}

	/**
	 * Gets the current value for a setting.
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

		return $this->get_value_from_wp( $this->settings[ $name ] );
	}

	/**
	 * Gets a registered setting instance.
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
	 * Checks whether a setting is registered.
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
	 * Registers a new setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to register.
	 */
	public function register_setting( WP_Site_Identity_Setting $setting ) {
		$name = $setting->get_name();

		$this->settings[ $name ] = $setting;

		$this->register_in_wp( $setting );
	}

	/**
	 * Unregisters an existing setting.
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

		$this->unregister_in_wp( $this->settings[ $name ] );

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
		return $this->feedback_handler;
	}

	/**
	 * Gets the validator for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Validator Validator to use for registered settings.
	 */
	public function validator() {
		return $this->validator;
	}

	/**
	 * Gets the sanitizer for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Sanitizer Sanitizer to use for registered settings.
	 */
	public function sanitizer() {
		return $this->sanitizer;
	}

	/**
	 * Prefixes a setting name.
	 *
	 * If no name is given, the prefix is simply returned.
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
	 * Sanitizes a value for a setting in WordPress.
	 *
	 * Due to WordPress using the respective hook for validation and sanitization,
	 * this method validates the value as well before sanitizing it. In case of a
	 * validation error it adds the error in WordPress to make sure it shows in the UI.
	 *
	 * @since 1.0.0
	 * @internal
	 *
	 * @param mixed  $value Value to sanitize.
	 * @param string $name  Name of the setting.
	 * @return mixed Sanitized value, or old value if an error occurred.
	 */
	public function sanitize_value_in_wp( $value, $name ) {
		$setting = $this->get( $name );

		try {
			$validated_value = $this->validator->validate( $value, $setting );
		} catch ( WP_Site_Identity_Setting_Validation_Error_Exception $e ) {
			$this->feedback_handler->add_error( $setting, $e->getMessage() );

			return $this->get_value_from_wp( $setting );
		}

		return $this->sanitizer->sanitize( $validated_value, $setting );
	}

	/**
	 * Gets the current value for a setting from WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to get the value for.
	 * @return mixed Current setting value.
	 */
	protected function get_value_from_wp( WP_Site_Identity_Setting $setting ) {
		$name = $this->prefix( $setting->get_name() );

		return get_option( $name );
	}

	/**
	 * Registers a new setting in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to register.
	 */
	protected function register_in_wp( WP_Site_Identity_Setting $setting ) {
		$name = $this->prefix( $setting->get_name() );

		$args = array(
			'type'              => $setting->get_type(),
			'group'             => $this->group,
			'description'       => $setting->get_description(),
			'sanitize_callback' => null,
			'show_in_rest'      => false,
		);

		if ( $setting->show_in_rest() ) {
			$args['show_in_rest'] = array(
				'schema' => $this->build_rest_schema_for_wp( $setting ),
			);
		}

		register_setting( $this->group, $name, $args );

		add_filter( "sanitize_option_{$name}", array( $this, 'sanitize_value_in_wp' ), 10, 2 );
	}

	/**
	 * Unregisters an existing setting in WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to unregister.
	 */
	protected function unregister_in_wp( WP_Site_Identity_Setting $setting ) {
		$name = $this->prefix( $setting->get_name() );

		remove_filter( "sanitize_option_{$name}", array( $this, 'sanitize_value_in_wp' ), 10 );

		unregister_setting( $this->group, $name );
	}

	/**
	 * Builds the REST schema array to pass to WordPress.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to build the REST schema for.
	 * @return array REST schema.
	 */
	protected function build_rest_schema_for_wp( WP_Site_Identity_Setting $setting ) {
		$default_schema = array(
			'type'        => $setting->get_type(),
			'description' => $setting->get_description(),
			'default'     => $setting->get_default(),
		);

		switch ( $default_schema['type'] ) {
			case 'string':
				$choices = $setting->get_choices();
				$format  = $setting->get_format();
				if ( ! empty( $choices ) ) {
					$default_schema['enum'] = array_keys( $choices );
				}
				if ( ! empty( $format ) ) {
					$default_schema['format'] = $format;
				}
				break;
			case 'integer':
			case 'number':
				$min = $setting->get_min();
				$max = $setting->get_max();
				if ( is_float( $min ) || is_int( $min ) ) {
					$default_schema['minimum'] = $min;
				}
				if ( is_float( $max ) || is_int( $max ) ) {
					$default_schema['maximum'] = $max;
				}
				break;
		}

		return array_merge( $default_schema, $setting->get_rest_schema() );
	}
}
