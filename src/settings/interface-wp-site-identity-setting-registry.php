<?php
/**
 * WP_Site_Identity_Setting_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering settings.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Setting_Registry {

	/**
	 * Gets the current value for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return mixed Current setting value.
	 *
	 * @throws WP_Site_Identity_Setting_Not_Found_Exception Thrown when a setting cannot be found.
	 */
	public function get_value( $name );

	/**
	 * Gets all registered settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$name => $instance` pairs.
	 */
	public function get_all_settings();

	/**
	 * Gets a registered setting instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return WP_Site_Identity_Setting Registered setting.
	 *
	 * @throws WP_Site_Identity_Setting_Not_Found_Exception Thrown when a setting cannot be found.
	 */
	public function get_setting( $name );

	/**
	 * Checks whether a setting is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Name of the setting.
	 * @return bool True if the setting is registered, false otherwise.
	 */
	public function has_setting( $name );

	/**
	 * Registers a new setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to register.
	 */
	public function register_setting( WP_Site_Identity_Setting $setting );

	/**
	 * Unregisters an existing setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to unregister.
	 */
	public function unregister_setting( WP_Site_Identity_Setting $setting );

	/**
	 * Gets the factory to create setting objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Factory Factory to create setting objects.
	 */
	public function factory();

	/**
	 * Gets the feedback handler for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Feedback_Handler Feedback handler to use for registered settings.
	 */
	public function feedback_handler();

	/**
	 * Gets the validator for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Validator Validator to use for registered settings.
	 */
	public function validator();

	/**
	 * Gets the sanitizer for the setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Sanitizer Sanitizer to use for registered settings.
	 */
	public function sanitizer();

	/**
	 * Prefixes a setting name.
	 *
	 * If no name is given, the prefix is simply returned.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting name to prefix.
	 * @return string Prefixed setting name.
	 */
	public function prefix( $name = '' );

	/**
	 * Gets the group to use for registered settings.
	 *
	 * @since 1.0.0
	 *
	 * @return string Group identifier.
	 */
	public function group();
}
