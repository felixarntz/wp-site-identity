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

	}

	/**
	 * Removes all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {

	}

	/**
	 * Registers all services used by the plugin.
	 *
	 * @since 1.0.0
	 */
	private function register_services() {
		$this->services->register( 'setting_feedback_handler', 'WP_Site_Identity_Setting_Feedback_Handler' );
		$this->services->register( 'setting_registry', 'WP_Site_Identity_Setting_Registry', array( new WP_Site_Identity_Service_Reference( 'setting_feedback_handler' ) ) );
	}
}
