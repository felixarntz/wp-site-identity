<?php
/**
 * WP_Site_Identity_Settings_Field_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering settings fields.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Settings_Field_Registry {

	/**
	 * Gets all registered settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_fields();

	/**
	 * Gets a registered settings field instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings field.
	 * @return WP_Site_Identity_Settings_Field Registered settings field.
	 *
	 * @throws WP_Site_Identity_Settings_Field_Not_Found_Exception Thrown when a settings field cannot be found.
	 */
	public function get_field( $slug );

	/**
	 * Checks whether a settings field is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings field.
	 * @return bool True if the settings field is registered, false otherwise.
	 */
	public function has_field( $slug );

	/**
	 * Registers a new settings field.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field $field Settings field to register.
	 */
	public function register_field( WP_Site_Identity_Settings_Field $field );

	/**
	 * Gets the factory to create settings field objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Field_Factory Factory to create settings field objects.
	 */
	public function factory();
}
