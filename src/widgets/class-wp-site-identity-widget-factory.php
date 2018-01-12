<?php
/**
 * WP_Site_Identity_Widget_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating widget objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Widget_Factory {

	/**
	 * Registry to use for instantiating widgets.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Widget_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Widget_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Widget_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Widget_Registry();
		}
	}

	/**
	 * Instantiates a new widget object for the given ID base and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $id_base         Base ID for the widget, lowercase and unique. If left empty,
	 *                                  a portion of the widget's class name will be used. Has to be unique.
	 * @param string   $name            Name for the widget displayed on the configuration page.
	 * @param string   $description     Widget description.
	 * @param callable $render_callback Widget rendering callback. Must return its content.
	 * @param array    $fields          Optional. Fields for the shortcode as `$attr => $args` pairs. Default empty array.
	 * @return WP_Site_Identity_Widget New widget instance.
	 */
	public function create_widget( $id_base, $name, $description, $render_callback, array $fields = array() ) {
		return new WP_Site_Identity_Widget( $this->registry->prefix( $id_base ), $name, $description, $render_callback, $fields, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new widgets.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Widget_Registry Registry for new widgets.
	 */
	public function registry() {
		return $this->registry;
	}
}
