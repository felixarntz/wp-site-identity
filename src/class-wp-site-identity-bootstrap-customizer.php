<?php
/**
 * WP_Site_Identity_Bootstrap_Customizer class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class to register the plugin's Customizer content.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap_Customizer {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity $plugin Plugin instance.
	 */
	public function __construct( WP_Site_Identity $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Action to register the plugin's Customizer content.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Customize_Manager $wp_customize Customize manager instance.
	 */
	public function action_customize_register( $wp_customize ) {
		$setting_registry    = $this->plugin->services()->get( 'setting_registry' );
		$owner_data_registry = $setting_registry->get_setting( 'owner_data' );
		$owner_data          = $this->plugin->owner_data();

		$site_identity_panel = 'site_identity';
		$owner_data_section  = 'owner_data';

		$wp_customize->add_panel( $setting_registry->prefix( $site_identity_panel ), array(
			'title'    => __( 'Site Identity', 'wp-site-identity' ),
			'priority' => 20,
		) );

		$title_tagline_section = $wp_customize->get_section( 'title_tagline' );
		if ( $title_tagline_section ) {
			if ( current_theme_supports( 'custom-logo' ) ) {
				$title_tagline_section->title = __( 'Title and Logo', 'wp-site-identity' );
			} else {
				$title_tagline_section->title = __( 'Title and Tagline', 'wp-site-identity' );
			}

			$title_tagline_section->panel = $setting_registry->prefix( $site_identity_panel );
		}

		$wp_customize->add_section( $setting_registry->prefix( $owner_data_section ), array(
			'title'      => __( 'Owner Data', 'wp-site-identity' ),
			'capability' => 'manage_options',
			'panel'      => $setting_registry->prefix( $site_identity_panel ),
			'priority'   => 30,
		) );

		$address_fields = array(
			'address_line_1'         => '',
			'address_line_2'         => '',
			'address_city'           => '',
			'address_zip'            => '',
			'address_state'          => '',
			'address_state_abbrev'   => '',
			'address_country'        => '',
			'address_country_abbrev' => '',
			'address_format_single'  => '',
			'address_format_multi'   => '',
		);

		foreach ( $owner_data_registry->get_all_settings() as $setting ) {
			$setting_basename = $setting->get_name();

			$setting_name  = $owner_data_registry->prefix( $owner_data_registry->get_name() ) . '[' . $setting_basename . ']';

			$validate_callback = 'validate_callback_' . $owner_data_registry->get_name() . '_setting_' . $setting_basename;
			$sanitize_callback = 'sanitize_callback_' . $owner_data_registry->get_name() . '_setting_' . $setting_basename;
			$partial_callback  = 'partial_callback_' . $owner_data_registry->get_name() . '_setting_' . $setting_basename;

			if ( isset( $address_fields[ $setting_basename ] ) ) {
				$address_fields[ $setting_basename ] = $setting_name;
			}

			$setting_args = array(
				'type'              => 'option',
				'capability'        => 'manage_options',
				'default'           => $setting->get_default(),
				'transport'         => 'postMessage',
				'validate_callback' => array( $this, $validate_callback ),
				'sanitize_callback' => array( $this, $sanitize_callback ),
			);

			$control_args = array(
				'label'   => $setting->get_title(),
				'section' => $setting_registry->prefix( $owner_data_section ),
			);

			$setting_description = $setting->get_description();
			if ( ! empty( $setting_description ) ) {
				$control_args['description'] = $setting_description;
			}

			$control_args = $this->set_default_control_args_for_setting( $control_args, $setting );

			$wp_customize->add_setting( $setting_name, $setting_args );
			$wp_customize->add_control( $setting_name, $control_args );

			$wp_customize->selective_refresh->add_partial( $owner_data_registry->prefix( $setting_basename ), array(
				'settings'            => array( $setting_name ),
				'selector'            => '.' . $owner_data->get_css_class( $setting_basename ),
				'render_callback'     => array( $this, $partial_callback ),
				'container_inclusive' => true,
			) );
		}

		$address_single_settings = $address_fields;
		$address_multi_settings  = $address_fields;

		unset( $address_single_settings['address_format_multi'] );
		unset( $address_multi_settings['address_format_single'] );

		$address_single_settings = array_values( $address_single_settings );
		$address_multi_settings  = array_values( $address_multi_settings );

		$wp_customize->selective_refresh->add_partial( $owner_data_registry->prefix( 'address_single' ), array(
			'settings'            => $address_single_settings,
			'selector'            => '.' . $owner_data->get_css_class( 'address_single' ),
			'render_callback'     => array( $this, 'partial_callback_' . $owner_data_registry->get_name() . '_setting_address_single' ),
			'container_inclusive' => true,
		) );
		$wp_customize->selective_refresh->add_partial( $owner_data_registry->prefix( 'address_multi' ), array(
			'settings'            => $address_multi_settings,
			'selector'            => '.' . $owner_data->get_css_class( 'address_multi' ),
			'render_callback'     => array( $this, 'partial_callback_' . $owner_data_registry->get_name() . '_setting_address_multi' ),
			'container_inclusive' => true,
		) );

		/**
		 * Fires when additional Customizer content for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Customize_Manager $wp_customize Customize manager instance.
		 */
		do_action( 'wp_site_identity_register_customizer_content', $wp_customize );
	}

	/**
	 * Action to enqueue the plugin's Customizer preview script.
	 *
	 * @since 1.0.0
	 */
	public function action_customize_preview_init() {

	}

	/**
	 * Magic call method.
	 *
	 * Acts as a dynamic shortcode render callback.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Method name.
	 * @param array  $args   Method arguments.
	 * @return mixed Method results.
	 */
	public function __call( $method, $args ) {
		$is_callback = preg_match( '/^(validate|sanitize|partial)_callback_([a-z_]+)_setting_([a-z_]+)$/', $method, $matches );

		if ( ! $is_callback ) {
			return;
		}

		$callback_type          = $matches[1];
		$aggregate_setting_name = $matches[2];
		$setting_name           = $matches[3];

		switch ( $callback_type ) {
			case 'validate':
				return $this->validate_setting( $args[0], $args[1], $aggregate_setting_name, $setting_name );
			case 'sanitize':
				return $this->sanitize_setting( $args[0], $aggregate_setting_name, $setting_name );
			case 'partial':
				$this->print_partial( $aggregate_setting_name, $setting_name );
				break;
		}
	}

	/**
	 * Validates a Customizer setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Error $validity               Error object to add validation errors to as necessary.
	 * @param mixed    $value                  Value to sanitize.
	 * @param string   $aggregate_setting_name Name of the aggregate setting or setting registry.
	 * @param string   $setting_name           Name of the setting.
	 * @return mixed Sanitized value.
	 */
	private function validate_setting( $validity, $value, $aggregate_setting_name, $setting_name ) {
		$setting_registry  = $this->plugin->services()->get( 'setting_registry' );
		$aggregate_setting = $setting_registry->get_setting( $aggregate_setting_name );
		$setting           = $aggregate_setting->get_setting( $setting_name );

		try {
			$validated_value = $setting_registry->validator()->validate( $value, $setting );
		} catch ( WP_Site_Identity_Setting_Validation_Error_Exception $e ) {
			$prefixed = $setting_registry->prefix( $setting_name );

			$validity->add( "valid_{$prefixed}", $e->getMessage() );
		}

		return $validity;
	}

	/**
	 * Sanitizes a Customizer setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $value                  Value to sanitize.
	 * @param string $aggregate_setting_name Name of the aggregate setting or setting registry.
	 * @param string $setting_name           Name of the setting.
	 * @return mixed Sanitized value.
	 */
	private function sanitize_setting( $value, $aggregate_setting_name, $setting_name ) {
		$setting_registry  = $this->plugin->services()->get( 'setting_registry' );
		$aggregate_setting = $setting_registry->get_setting( $aggregate_setting_name );
		$setting           = $aggregate_setting->get_setting( $setting_name );

		try {
			$validated_value = $setting_registry->validator()->validate( $value, $setting );
		} catch ( WP_Site_Identity_Setting_Validation_Error_Exception $e ) {
			$validated_value = $setting_registry->get_value_from_wp( $setting );
		}

		return $setting_registry->sanitizer()->sanitize( $validated_value, $setting );
	}

	/**
	 * Prints a partial for a Customizer setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $aggregate_setting_name Name of the aggregate setting or setting registry.
	 * @param string $setting_name           Name of the setting.
	 */
	private function print_partial( $aggregate_setting_name, $setting_name ) {
		$data = call_user_func( array( $this->plugin, $aggregate_setting_name ) );

		echo $data->get_as_html( $setting_name ); // WPCS: XSS OK.
	}

	/**
	 * Gets the default Customizer control arguments depending on settings field arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array                    $control_args Arguments for the Customizer control. See {@see WP_Customize_Control::__construct}
	 *                                               for a list of supported arguments.
	 * @param WP_Site_Identity_Setting $setting      Setting the Customizer control is for.
	 * @return array Arguments including a type and further details.
	 */
	private function set_default_control_args_for_setting( array $control_args, WP_Site_Identity_Setting $setting ) {
		$type = $setting->get_type();

		switch ( $type ) {
			case 'boolean':
				$control_args['type'] = 'checkbox';
				break;
			case 'number':
			case 'integer':
				$control_args['type']        = 'number';
				$control_args['input_attrs'] = array();

				$min = $setting->get_min();
				$max = $setting->get_max();

				if ( is_float( $min ) || is_int( $min ) ) {
					if ( 'integer' === $type ) {
						$control_args['input_attrs']['min'] = (int) $min;
					} else {
						$control_args['input_attrs']['min'] = $min;
					}
				}

				if ( is_float( $max ) || is_int( $max ) ) {
					if ( 'integer' === $type ) {
						$control_args['input_attrs']['max'] = (int) $max;
					} else {
						$control_args['input_attrs']['max'] = $max;
					}
				}

				if ( 'integer' === $type ) {
					$control_args['input_attrs']['step'] = 1;
				} else {
					$control_args['input_attrs']['step'] = 0.01;
				}
				break;
			case 'string':
				$choices = $setting->get_choices();
				if ( ! empty( $choices ) ) {
					if ( count( $choices ) <= 5 ) {
						$control_args['type'] = 'radio';
					} else {
						$control_args['type'] = 'select';
					}
					$control_args['choices'] = $choices;
				} else {
					$default = $setting->get_default();
					$format  = $setting->get_format();

					if ( 'email' === $format ) {
						$control_args['type'] = 'email';
					} elseif ( 'uri' === $format ) {
						$control_args['type'] = 'url';
					} else {
						if ( is_string( $default ) && false !== strpos( $default, "\n" ) ) {
							$control_args['type'] = 'textarea';
						} else {
							$control_args['type'] = 'text';
						}
					}
				}
				break;
		}

		return $control_args;
	}
}
