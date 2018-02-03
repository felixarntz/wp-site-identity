<?php
/**
 * WP_Site_Identity_Bootstrap_Settings class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class to register the plugin's settings.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap_Settings {

	/**
	 * Plugin bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap
	 */
	private $bootstrap;

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
	 * @param WP_Site_Identity_Bootstrap $bootstrap Plugin bootstrap instance.
	 * @param WP_Site_Identity           $plugin    Plugin instance.
	 */
	public function __construct( WP_Site_Identity_Bootstrap $bootstrap, WP_Site_Identity $plugin ) {
		$this->bootstrap = $bootstrap;
		$this->plugin    = $plugin;
	}

	/**
	 * Action to register the plugin's settings.
	 *
	 * @since 1.0.0
	 */
	public function action_init() {
		$registry = $this->plugin->services()->get( 'setting_registry' );
		$factory  = $registry->factory();

		$type_choices = $this->bootstrap->get_type_choices();

		$owner_data = $factory->create_aggregate_setting( 'owner_data', array(
			'title'        => __( 'Owner Data', 'wp-site-identity' ),
			'description'  => __( 'Data about the owner of the website.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		$owner_data->factory()->create_setting( 'type', array(
			'title'        => __( 'Type', 'wp-site-identity' ),
			'description'  => __( 'Select the type of entity that is the owner of the site.', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => key( $type_choices ),
			'show_in_rest' => true,
			'choices'      => $type_choices,
		) )->register();

		$owner_data->factory()->create_setting( 'first_name', array(
			'title'        => __( 'First Name', 'wp-site-identity' ),
			'description'  => __( 'The owner&#8217;s first name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'last_name', array(
			'title'        => __( 'Last Name', 'wp-site-identity' ),
			'description'  => __( 'The owner&#8217;s last name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'organization_name', array(
			'title'        => __( 'Organization Name', 'wp-site-identity' ),
			'description'  => __( 'The organization&#8217;s name.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'organization_legal_name', array(
			'title'        => __( 'Organization Legal Name', 'wp-site-identity' ),
			'description'  => __( 'The organization&#8217;s full legal name as registered.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_line_1', array(
			'title'        => __( 'Address Line 1', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_line_2', array(
			'title'        => __( 'Address Line 2 (optional)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_city', array(
			'title'        => __( 'City', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_zip', array(
			'title'        => __( 'Zip / Postal Code', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_state', array(
			'title'        => __( 'State', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_state_abbrev', array(
			'title'        => __( 'State (Abbrev.)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_country', array(
			'title'        => __( 'Country', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_country_abbrev', array(
			'title'        => __( 'Country (Abbrev.)', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$address_placeholders_string = implode( ', ', array(
			'{line_1}',
			'{line_2}',
			'{city}',
			'{zip}',
			'{state}',
			'{state_abbrev}',
			'{country}',
			'{country_abbrev}',
		) );

		$address_single_default = _x( '{line_1} {line_2}, {city}, {state_abbrev} {zip}', 'single line address template', 'wp-site-identity' );
		$address_multi_default  = str_replace( '<br />', PHP_EOL, _x( '{line_1}<br />{line_2}<br />{city}, {state_abbrev} {zip}', 'multiple lines address template', 'wp-site-identity' ) );

		$owner_data->factory()->create_setting( 'address_format_single', array(
			'title'             => __( 'Address Format (Single Line)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'       => sprintf( __( 'The address format as a single line. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'              => 'string',
			'default'           => $address_single_default,
			'validate_callback' => array( $this, 'validate_address_format' ),
			'show_in_rest'      => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_format_multi', array(
			'title'             => __( 'Address Format (Multiple Lines)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'       => sprintf( __( 'The address format as multiple lines. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'              => 'string',
			'default'           => $address_multi_default,
			'validate_callback' => array( $this, 'validate_address_format' ),
			'show_in_rest'      => true,
		) )->register();

		$owner_data->factory()->create_setting( 'email', array(
			'title'        => __( 'Email Address', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => get_option( 'admin_email' ),
			'show_in_rest' => true,
			'format'       => 'email',
		) )->register();

		$owner_data->factory()->create_setting( 'website', array(
			'title'        => __( 'Website URL', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => home_url(),
			'show_in_rest' => true,
			'format'       => 'uri',
		) )->register();

		$owner_data->factory()->create_setting( 'phone', array(
			'title'        => __( 'Phone Number (Machine Readable)', 'wp-site-identity' ),
			'description'  => __( 'The contact phone number, in machine readable format (for example <code>+1555123456</code>).', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'phone_human', array(
			'title'        => __( 'Phone Number (Human Readable)', 'wp-site-identity' ),
			'description'  => __( 'The contact phone number, in human readable format (for example <code>(555) 123 456</code>).', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->register();

		$brand_data = $factory->create_aggregate_setting( 'brand_data', array(
			'title'        => __( 'Brand', 'wp-site-identity' ),
			'description'  => __( 'Appearance information representing the brand_data.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		$logo_description = __( 'Upload the logo.', 'wp-site-identity' );

		$custom_logo_args = get_theme_support( 'custom-logo' );
		if ( $custom_logo_args ) {
			/* translators: 1: image width, 2: image height */
			$logo_description .= ' ' . sprintf( __( 'The image should have a resolution of %1$sx%2$s pixels, or at least have a similar aspect ratio.', 'wp-site-identity' ), $custom_logo_args[0]['width'], $custom_logo_args[0]['height'] );
		}

		$brand_data->factory()->create_setting( 'logo', array(
			'title'             => __( 'Logo', 'wp-site-identity' ),
			'description'       => $logo_description,
			'type'              => 'integer',
			'validate_callback' => array( $this, 'validate_image' ),
			'show_in_rest'      => true,
		) )->register();

		$brand_data->factory()->create_setting( 'icon', array(
			'title'             => __( 'Icon', 'wp-site-identity' ),
			/* translators: 1: image width, 2: image height */
			'description'       => sprintf( __( 'Upload the icon. The image should be in square format and have a resolution of at least %1$sx%2$s pixels.', 'wp-site-identity' ), 512, 512 ),
			'type'              => 'integer',
			'validate_callback' => array( $this, 'validate_image' ),
			'show_in_rest'      => true,
		) )->register();

		$brand_data->factory()->create_setting( 'primary_color', array(
			'title'             => __( 'Primary Color', 'wp-site-identity' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_color' ),
			'show_in_rest'      => true,
		) )->register();

		$brand_data->factory()->create_setting( 'secondary_color', array(
			'title'             => __( 'Secondary Color', 'wp-site-identity' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_color' ),
			'show_in_rest'      => true,
		) )->register();

		$brand_data->factory()->create_setting( 'tertiary_color', array(
			'title'             => __( 'Tertiary Color', 'wp-site-identity' ),
			'type'              => 'string',
			'validate_callback' => array( $this, 'validate_color' ),
			'show_in_rest'      => true,
		) )->register();

		$brand_data->register();

		/**
		 * Fires when additional settings for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Setting_Registry $registry Setting registry instance.
		 */
		do_action( 'wp_site_identity_register_settings', $registry );
	}

	/**
	 * Validates an address format setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @return string Address format string.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate_address_format( $value ) {
		$value = trim( (string) $value );

		if ( empty( $value ) ) {
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( __( 'The address format must not be empty.', 'wp-site-identity' ) );
		}

		if ( false === strpos( $value, '{' ) || false === strpos( $value, '}' ) ) {
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( __( 'The address format must contain at least one placeholder.', 'wp-site-identity' ) );
		}

		return $value;
	}

	/**
	 * Validates an image setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @return int Attachment ID.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate_image( $value ) {
		$value = (int) $value;

		$attachment = get_post( $value );
		if ( ! $attachment ) {
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( __( 'The specified attachment does not exist.', 'wp-site-identity' ) );
		}

		if ( 'attachment' !== $attachment->post_type || 'image/' !== substr( $attachment->post_mime_type, 0, 6 ) ) {
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( __( 'The specified attachment is not an image.', 'wp-site-identity' ) );
		}

		return $value;
	}

	/**
	 * Validates a color setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @return string Hex color string.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate_color( $value ) {
		$value = (string) $value;

		if ( 0 !== strpos( $value, '#' ) ) {
			$value = '#' . $value;
		}

		if ( ! preg_match( '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/i', $value ) ) {
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( __( 'The color must be specified in hexadecimal format.', 'wp-site-identity' ) );
		}

		return $value;
	}
}
