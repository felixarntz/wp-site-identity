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

		// TODO: Register owner data sub settings.
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
			'wp_site_identity',
			new WP_Site_Identity_Service_Reference( 'setting_feedback_handler' ),
			new WP_Site_Identity_Service_Reference( 'setting_validator' ),
			new WP_Site_Identity_Service_Reference( 'setting_sanitizer' ),
		) );
	}
}
