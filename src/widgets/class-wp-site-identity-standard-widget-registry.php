<?php
/**
 * WP_Site_Identity_Standard_Widget_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering widgets.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Widget_Registry implements WP_Site_Identity_Widget_Registry {

	/**
	 * All registered widgets as `$id_base => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $widgets = array();

	/**
	 * Factory to create widget objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Widget_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->factory = new WP_Site_Identity_Widget_Factory( $this );
	}

	/**
	 * Gets all registered widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$id_base => $instance` pairs.
	 */
	public function get_all_widgets() {
		return $this->widgets;
	}

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
	public function get_widget( $id_base ) {
		if ( ! isset( $this->widgets[ $id_base ] ) ) {
			/* translators: %s: widget ID base */
			throw new WP_Site_Identity_Widget_Not_Found_Exception( sprintf( __( 'The widget with the ID base %s could not be found.', 'wp-site-identity' ), $id_base ) );
		}

		return $this->widgets[ $id_base ];
	}

	/**
	 * Checks whether a widget is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id_base Widget ID base.
	 * @return bool True if the widget is registered, false otherwise.
	 */
	public function has_widget( $id_base ) {
		return isset( $this->widgets[ $id_base ] );
	}

	/**
	 * Registers a new widget.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Widget $widget Widget to register.
	 */
	public function register_widget( WP_Site_Identity_Widget $widget ) {
		$id_base = $widget->get_id_base();

		$this->widgets[ $id_base ] = $widget;

		register_widget( $widget );
	}

	/**
	 * Unregisters an existing widget.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Widget $widget Widget to unregister.
	 */
	public function unregister_widget( WP_Site_Identity_Widget $widget ) {
		$id_base = $widget->get_id_base();

		if ( ! isset( $this->widgets[ $id_base ] ) ) {
			return;
		}

		unregister_widget( $widget );

		unset( $this->widgets[ $id_base ] );
	}

	/**
	 * Gets the factory to create widget objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Widget_Factory Factory to create widget objects.
	 */
	public function factory() {
		return $this->factory;
	}
}
