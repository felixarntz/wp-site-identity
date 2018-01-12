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
	 * Instantiates a new widget object for the given tag and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $tag             Widget tag.
	 * @param callable $render_callback Callback for the widget. Will already receive parsed attributes, so
	 *                                  including that logic is not necessary.
	 * @param array    $args            Optional. Arguments for the widget. See {@see WP_Site_Identity_Widget::__construct}
	 *                                  for a list of supported arguments.
	 * @return WP_Site_Identity_Widget New widget instance.
	 */
	public function create_widget( $tag, $render_callback, $args = array() ) {
		return new WP_Site_Identity_Widget( $tag, $render_callback, $args, $this->registry );
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
