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
	 * Plugin bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap
	 */
	private $bootstrap;

	/**
	 * Owner data access point.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Owner_Data
	 */
	private $owner_data;

	/**
	 * Brand data access point.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Data
	 */
	private $brand_data;

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
		$this->bootstrap = new WP_Site_Identity_Bootstrap( $this );

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
	 * Gets the owner data access point.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Owner_Data Owner data access point.
	 */
	public function owner_data() {
		if ( ! isset( $this->owner_data ) ) {
			$aggregate_setting = $this->services->get( 'setting_registry' )->get_setting( 'owner_data' );

			$this->owner_data = new WP_Site_Identity_Owner_Data( 'wpsi_', $aggregate_setting );
		}
		return $this->owner_data;
	}

	/**
	 * Gets the brand data access point.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Data Appearance access point.
	 */
	public function brand_data() {
		if ( ! isset( $this->brand_data ) ) {
			$aggregate_setting = $this->services->get( 'setting_registry' )->get_setting( 'brand_data' );

			$this->brand_data = new WP_Site_Identity_Brand_Data( 'wpsi_', $aggregate_setting, $this );
		}
		return $this->brand_data;
	}

	/**
	 * Gets the theme support value for a given plugin feature.
	 *
	 * @since 1.0.0
	 *
	 * @param string $feature Plugin feature to check.
	 * @return mixed Theme support value for $feature, or false if not supported.
	 */
	public function get_theme_support( $feature ) {
		$support = get_theme_support( 'wp_site_identity' );
		if ( ! $support ) {
			return false;
		}

		if ( ! array_key_exists( $feature, $support[0] ) ) {
			return false;
		}

		if ( 'css_properties' === $feature && $support[0][ $feature ] ) {
			$css_properties = $support[0][ $feature ];

			if ( ! is_array( $css_properties ) ) {
				$css_properties = array();
			}

			$defaults = array();
			foreach ( array( 'primary', 'secondary', 'tertiary' ) as $color_slug ) {
				$defaults[ $color_slug ]               = $color_slug . '-color';
				$defaults[ $color_slug . '_shade' ]    = $color_slug . '-shade-color';
				$defaults[ $color_slug . '_contrast' ] = $color_slug . '-contrast-color';
			}

			return wp_parse_args( $css_properties, $defaults );
		}

		return $support[0][ $feature ];
	}

	/**
	 * Adds all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		$this->bootstrap->add_hooks();
	}

	/**
	 * Removes all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {
		$this->bootstrap->remove_hooks();
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

		// Shortcodes.
		$this->services->register( 'shortcode_registry', 'WP_Site_Identity_Standard_Shortcode_Registry', array(
			'wpsi_',
		) );

		// Widgets.
		$this->services->register( 'widget_registry', 'WP_Site_Identity_Standard_Widget_Registry', array(
			'wpsi_',
		) );
	}
}
