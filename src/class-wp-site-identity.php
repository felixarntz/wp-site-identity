<?php
/**
 * WP_Site_Identity class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Plugin main class.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity {

	/**
	 * Plugin main file.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $main_file;

	/**
	 * Plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $version;

	/**
	 * Service container.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Service_Container
	 */
	private $services;

	/**
	 * Constructor.
	 *
	 * Sets the plugin file and version and instantiates the service container.
	 *
	 * @since 1.0.0
	 *
	 * @param string $main_file Plugin main file.
	 * @param string $version   Plugin version.
	 */
	public function __construct( $main_file, $version ) {
		$this->main_file = $main_file;
		$this->version   = $version;
		$this->services  = new WP_Site_Identity_Service_Container();

		$this->register_services();
	}

	/**
	 * Gets the full path for a relative path within the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path Relative path to a plugin file or directory.
	 * @return string Full path.
	 */
	public function path( $relative_path ) {
		return path_join( plugin_dir_path( $this->main_file ), $relative_path );
	}

	/**
	 * Gets the full URL for a relative path within the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @param string $relative_path Relative path to a plugin file or directory.
	 * @return string Full URL.
	 */
	public function url( $relative_path ) {
		return path_join( plugin_dir_url( $this->main_file ), $relative_path );
	}

	/**
	 * Gets the plugin version number.
	 *
	 * @since 1.0.0
	 *
	 * @return string Plugin version number.
	 */
	public function version() {
		return $this->version;
	}

	/**
	 * Gets the plugin's service container.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Service_Container Service container instance.
	 */
	public function services() {
		return $this->services;
	}

	/**
	 * Adds all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		add_action( 'init', array( $this, 'action_register_settings' ), 10, 0 );
		add_action( 'admin_menu', array( $this, 'action_register_settings_page' ), 10, 0 );
	}

	/**
	 * Removes all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {
		remove_action( 'init', array( $this, 'action_register_settings' ), 10 );
		remove_action( 'admin_menu', array( $this, 'action_register_settings_page' ), 10 );
	}

	/**
	 * Action to register the plugin's settings.
	 *
	 * @since 1.0.0
	 */
	public function action_register_settings() {
		$factory = $this->services->get( 'setting_registry' )->factory();

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
		$address_multi_default  = _x( '{line_1}
{line_2}
{city}, {state_abbrev} {zip}', 'multiple lines address template', 'wp-site-identity' );

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
			'description'  => __( 'The contact phone number, in machine readable format.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->factory()->create_setting( 'phone_human', array(
			'title'        => __( 'Phone Number (Human Readable)', 'wp-site-identity' ),
			'description'  => __( 'The contact phone number, in human readable format.', 'wp-site-identity' ),
			'type'         => 'string',
			'show_in_rest' => true,
		) )->register();

		$owner_data->register();

		$appearance = $factory->create_aggregate_setting( 'appearance', array(
			'title'        => __( 'Appearance', 'wp-site-identity' ),
			'description'  => __( 'Apperance information representing the brand.', 'wp-site-identity' ),
			'show_in_rest' => true,
		) );

		// TODO: Register appearance sub settings.
		$appearance->register();
	}

	/**
	 * Action to register the plugin's settings page in the admin.
	 *
	 * @since 1.0.0
	 */
	public function action_register_settings_page() {
		$factory = $this->services->get( 'admin_page_registry' )->factory();

		$factory->create_admin_submenu_page( 'settings', array(
			'title'            => __( 'Site Identity Settings', 'wp-site-identity' ),
			'capability'       => 'manage_options',
			'render_callback'  => array( $this, 'action_render_settings_page' ),
			'handle_callback'  => array( $this, 'action_handle_settings_page' ),
			'enqueue_callback' => null,
			'menu_title'       => __( 'Site Identity', 'wp-site-identity' ),
			'parent_slug'      => 'options-general.php',
		) )->register();
	}

	/**
	 * Action to handle a request to the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	public function action_handle_settings_page() {
		$setting_registry = $this->services->get( 'setting_registry' );

		$factory = $this->services->get( 'settings_form_registry' )->factory();

		$owner_data_form = $factory->create_form( $setting_registry->get_setting( 'owner_data' ) );
		$owner_data_form->set_defaults();

		// TODO: Add owner data settings sections and fields.
		$owner_data_form->register();

		$appearance_form = $factory->create_form( $setting_registry->get_setting( 'appearance' ) );
		$appearance_form->set_defaults();

		// TODO: Add appearance settings sections and fields.
		$appearance_form->register();
	}

	/**
	 * Action to render the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page object.
	 */
	public function action_render_settings_page( $admin_page ) {
		if ( ! is_a( $admin_page, 'WP_Site_Identity_Admin_Submenu_Page' ) || 'options-general.php' !== $admin_page->get_parent_slug() ) {
			require ABSPATH . 'wp-admin/options-head.php';
		}

		$settings_forms = $this->services->get( 'settings_form_registry' )->get_all_forms();

		$current_slug = null;

		if ( ! empty( $settings_forms ) ) {
			// @codingStandardsIgnoreStart
			if ( isset( $_GET['tab'] ) && isset( $settings_forms[ $_GET['tab'] ] ) ) {
				$current_slug = $_GET['tab'];
			} else {
				$current_slug = key( $settings_forms );
			}
			// @codingStandardsIgnoreEnd
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( $admin_page->get_title() ); ?></h1>

			<?php if ( ! empty( $settings_forms ) ) : ?>
				<?php if ( count( $settings_forms ) > 1 ) : ?>
					<h2 class="nav-tab-wrapper" style="margin-bottom:1em;">
						<?php foreach ( $settings_forms as $slug => $settings_form ) : ?>
							<a class="<?php echo esc_attr( $slug === $current_slug ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>" href="<?php echo esc_url( add_query_arg( 'tab', $slug, $admin_page->get_url() ) ); ?>">
								<?php echo esc_html( $settings_form->get_setting_registry()->get_title() ); ?>
							</a>
						<?php endforeach; ?>
					</h2>
				<?php else : ?>
					<h2 class="screen-reader-text">
						<?php echo esc_html( $settings_forms[ $current_slug ]->get_setting_registry()->get_title() ); ?>
					</h2>
				<?php endif; ?>

				<?php $settings_forms[ $current_slug ]->render(); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Registers all services used by the plugin.
	 *
	 * @since 1.0.0
	 */
	private function register_services() {

		// Settings.
		$this->services->register( 'setting_feedback_handler', 'WP_Site_Identity_Setting_Feedback_Handler' );
		$this->services->register( 'setting_validator', 'WP_Site_Identity_Setting_Validator' );
		$this->services->register( 'setting_sanitizer', 'WP_Site_Identity_Setting_Sanitizer' );
		$this->services->register( 'setting_registry', 'WP_Site_Identity_Standard_Setting_Registry', array(
			'wpsi_',
			'site_identity',
			new WP_Site_Identity_Service_Reference( 'setting_feedback_handler' ),
			new WP_Site_Identity_Service_Reference( 'setting_validator' ),
			new WP_Site_Identity_Service_Reference( 'setting_sanitizer' ),
		) );

		// Admin pages.
		$this->services->register( 'admin_page_registry', 'WP_Site_Identity_Standard_Admin_Page_Registry', array(
			'wpsi_',
		) );

		// Settings forms.
		$this->services->register( 'settings_form_registry', 'WP_Site_Identity_Standard_Settings_Form_Registry' );
	}
}
