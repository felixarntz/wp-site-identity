<?php
/**
 * WP_Site_Identity_Settings_Form_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering settings forms.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Settings_Form_Registry {

	/**
	 * Gets all registered settings forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_forms();

	/**
	 * Gets a registered settings form instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings form.
	 * @return WP_Site_Identity_Settings_Form Registered settings form.
	 *
	 * @throws WP_Site_Identity_Settings_Form_Not_Found_Exception Thrown when a settings form cannot be found.
	 */
	public function get_form( $slug );

	/**
	 * Checks whether a settings form is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings form.
	 * @return bool True if the settings form is registered, false otherwise.
	 */
	public function has_form( $slug );

	/**
	 * Registers a new settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form $form Settings form to register.
	 */
	public function register_form( WP_Site_Identity_Settings_Form $form );

	/**
	 * Unregisters an existing settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form $form Settings form to unregister.
	 */
	public function unregister_form( WP_Site_Identity_Settings_Form $form );

	/**
	 * Gets the factory to create settings form objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Form_Factory Factory to create settings form objects.
	 */
	public function factory();
}
