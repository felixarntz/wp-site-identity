<?php
/**
 * WP_Site_Identity_Admin_Submenu_Page class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing an admin page that appears as a submenu item.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Admin_Submenu_Page extends WP_Site_Identity_Admin_Page {

	/**
	 * Menu title of the admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $menu_title = '';

	/**
	 * Parent slug of the admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $parent_slug = '';

	/**
	 * Constructor.
	 *
	 * Sets the admin page properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                               $slug     Admin page slug.
	 * @param array                                $args     {
	 *     Optional. Arguments for the admin page.
	 *
	 *     @type string   $title            Title for the admin page. Default will be generated from $slug.
	 *     @type string   $capability       Capability required to access the admin page. Default 'manage_options'.
	 *     @type callable $render_callback  Render callback for the admin page. Should print the content it
	 *                                      generates. Default null.
	 *     @type callable $handle_callback  Handle callback for the admin page. Should handle incoming requests and
	 *                                      set help content as necessary. Default null.
	 *     @type callable $enqueue_callback Enqueue callback for the admin page. Should enqueue scripts and styles
	 *                                      for the admin page as necessary. Default null.
	 *     @type string   $menu_title       Title to display in the menu for the admin page. Default is the value
	 *                                      of $title.
	 *     @type string   $parent_slug      Slug for the parent admin menu, or file name to a standard WordPress
	 *                                      admin page. Default 'options-general.php'.
	 * }
	 * @param WP_Site_Identity_Admin_Page_Registry $registry Optional. Parent registry for the admin page.
	 */
	public function __construct( $slug, array $args = array(), WP_Site_Identity_Admin_Page_Registry $registry = null ) {
		parent::__construct( $slug, $args, $registry );

		if ( ! empty( $args['menu_title'] ) ) {
			$this->menu_title = $args['menu_title'];
		} else {
			$this->menu_title = $this->title;
		}

		if ( ! empty( $args['parent_slug'] ) ) {
			$this->parent_slug = $args['parent_slug'];
		} else {
			$this->parent_slug = 'options-general.php';
		}
	}

	/**
	 * Gets the menu title of the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Menu title of the admin page.
	 */
	public function get_menu_title() {
		return $this->menu_title;
	}

	/**
	 * Gets the parent slug of the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Parent slug of the admin page.
	 */
	public function get_parent_slug() {
		return $this->parent_slug;
	}
}
