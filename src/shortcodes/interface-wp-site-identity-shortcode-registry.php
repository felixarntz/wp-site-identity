<?php
/**
 * WP_Site_Identity_Shortcode_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering shortcodes.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Shortcode_Registry {

	/**
	 * Gets all registered shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$tag => $instance` pairs.
	 */
	public function get_all_shortcodes();

	/**
	 * Gets a registered shortcode instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Tag of the shortcode.
	 * @return WP_Site_Identity_Shortcode Registered shortcode.
	 *
	 * @throws WP_Site_Identity_Shortcode_Not_Found_Exception Thrown when a shortcode cannot be found.
	 */
	public function get_shortcode( $tag );

	/**
	 * Checks whether a shortcode is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Tag of the shortcode.
	 * @return bool True if the shortcode is registered, false otherwise.
	 */
	public function has_shortcode( $tag );

	/**
	 * Registers a new shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Shortcode $shortcode Shortcode to register.
	 */
	public function register_shortcode( WP_Site_Identity_Shortcode $shortcode );

	/**
	 * Unregisters an existing shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Shortcode $shortcode Shortcode to unregister.
	 */
	public function unregister_shortcode( WP_Site_Identity_Shortcode $shortcode );

	/**
	 * Gets the factory to create shortcode objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Shortcode_Factory Factory to create shortcode objects.
	 */
	public function factory();

	/**
	 * Prefixes a shortcode tag.
	 *
	 * If no tag is given, the prefix is simply returned.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Shortcode tag to prefix.
	 * @return string Prefixed shortcode tag.
	 */
	public function prefix( $tag = '' );
}
