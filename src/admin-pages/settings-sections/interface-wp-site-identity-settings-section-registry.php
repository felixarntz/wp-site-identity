<?php
/**
 * WP_Site_Identity_Settings_Section_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering settings sections.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Settings_Section_Registry {

	/**
	 * Gets all registered settings sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_sections();

	/**
	 * Gets a registered settings section instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings section.
	 * @return WP_Site_Identity_Settings_Section Registered settings section.
	 *
	 * @throws WP_Site_Identity_Settings_Section_Not_Found_Exception Thrown when a settings section cannot be found.
	 */
	public function get_section( $slug );

	/**
	 * Checks whether a settings section is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings section.
	 * @return bool True if the settings section is registered, false otherwise.
	 */
	public function has_section( $slug );

	/**
	 * Registers a new settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Section $section Settings section to register.
	 */
	public function register_section( WP_Site_Identity_Settings_Section $section );

	/**
	 * Gets the factory to create settings section objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Section_Factory Factory to create settings section objects.
	 */
	public function factory();
}
