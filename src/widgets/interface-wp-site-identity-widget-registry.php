<?php
/**
 * WP_Site_Identity_Widget_Registry interface
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Interface for a structure that allows registering widgets.
 *
 * @since 1.0.0
 */
interface WP_Site_Identity_Widget_Registry {

	/**
	 * Gets all registered widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$id_base => $instance` pairs.
	 */
	public function get_all_widgets();

	/**
	 * Gets a registered widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id_base Widget ID base.
	 * @return WP_Site_Identity_Widget Registered widget.
	 *
	 * @throws WP_Site_Identity_Widget_Not_Found_Exception Thrown when a widget cannot be found.
	 */
	public function get_widget( $id_base );

	/**
	 * Checks whether a widget is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id_base Widget ID base.
	 * @return bool True if the widget is registered, false otherwise.
	 */
	public function has_widget( $id_base );

	/**
	 * Registers a new widget.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Widget $widget Widget to register.
	 */
	public function register_widget( WP_Site_Identity_Widget $widget );

	/**
	 * Unregisters an existing widget.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Widget $widget Widget to unregister.
	 */
	public function unregister_widget( WP_Site_Identity_Widget $widget );

	/**
	 * Gets the factory to create widget objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Widget_Factory Factory to create widget objects.
	 */
	public function factory();
}
