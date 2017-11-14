<?php
/**
 * WP_Site_Identity_Admin_Page_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering admin pages.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Admin_Page_Registry {

	/**
	 * Gets all registered admin pages.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_admin_pages();

	/**
	 * Gets a registered admin page instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the admin page.
	 * @return WP_Site_Identity_Admin_Page Registered admin page.
	 *
	 * @throws WP_Site_Identity_Admin_Page_Not_Found_Exception Thrown when a admin page cannot be found.
	 */
	public function get_admin_page( $slug );

	/**
	 * Checks whether a admin page is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the admin page.
	 * @return bool True if the admin page is registered, false otherwise.
	 */
	public function has_admin_page( $slug );

	/**
	 * Registers a new admin page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page to register.
	 */
	public function register_admin_page( WP_Site_Identity_Admin_Page $admin_page );

	/**
	 * Unregisters an existing admin page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page to unregister.
	 */
	public function unregister_admin_page( WP_Site_Identity_Admin_Page $admin_page );

	/**
	 * Gets the URL to a registered admin page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page to get the URL for.
	 * @return string URL to the admin page.
	 */
	public function get_url_to_admin_page( WP_Site_Identity_Admin_Page $admin_page );

	/**
	 * Gets the factory to create admin page objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Admin_Page_Factory Factory to create admin page objects.
	 */
	public function factory();

	/**
	 * Prefixes an admin page slug.
	 *
	 * If no slug is given, the prefix is simply returned.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Admin page slug to prefix.
	 * @return string Prefixed admin page slug.
	 */
	public function prefix( $slug = '' );
}
