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
	 * Action to register the plugin's settings.
	 *
	 * @since 1.0.0
	 */
	public function action_init() {
		$registry = $this->plugin->services()->get( 'setting_registry' );
		$factory  = $registry->factory();

		$owner_data = $factory->create_aggregate_setting( 'owner_data', array(
			'title'        => __( 'Owner Data', 'wp-site-identity' ),
			'description'  => __( 'Data about the owner of the website.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		$owner_data->factory()->create_setting( 'type', array(
			'title'        => __( 'Type', 'wp-site-identity' ),
			'description'  => __( 'Whether the owner is an organization or an individual.', 'wp-site-identity' ),
			'type'         => 'string',
			'default'      => 'individual',
			'show_in_rest' => true,
			'choices'      => array(
				'individual'   => __( 'Individual', 'wp-site-identity' ),
				'organization' => __( 'Organization', 'wp-site-identity' ),
			),
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
			'title'        => __( 'Address Format (Single Line)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'  => sprintf( __( 'The address format as a single line. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'         => 'string',
			'default'      => $address_single_default,
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'address_format_multi', array(
			'title'        => __( 'Address Format (Multiple Lines)', 'wp-site-identity' ),
			/* translators: %s: comma-separated list of placeholders */
			'description'  => sprintf( __( 'The address format as multiple lines. Allowed placeholders are: %s', 'wp_site-identity' ), $address_placeholders_string ),
			'type'         => 'string',
			'default'      => $address_multi_default,
			'show_in_rest' => true,
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

		$appearance = $factory->create_aggregate_setting( 'appearance', array(
			'title'        => __( 'Appearance', 'wp-site-identity' ),
			'description'  => __( 'Appearance information representing the brand.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		// TODO: Register appearance sub settings.
		$appearance->register();

		/**
		 * Fires when additional settings for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Setting_Registry $registry Setting registry instance.
		 */
		do_action( 'wp_site_identity_register_settings', $registry );
	}
}
