<?php
/**
 * WP_Site_Identity_Admin_Page_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating admin page objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Admin_Page_Factory {

	/**
	 * Registry to use for instantiating admin pages.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Admin_Page_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Admin_Page_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Admin_Page_Registry();
		}
	}

	/**
	 * Instantiates a new admin page object for the given slug and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Admin page slug.
	 * @param array  $args Optional. Arguments for the admin page. See {@see WP_Site_Identity_Admin_Page::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Admin_Page New admin page instance.
	 */
	public function create_admin_page( $slug, $args = array() ) {
		return new WP_Site_Identity_Admin_Page( $slug, $args, $this->registry );
	}

	/**
	 * Instantiates a new admin menu page object for the given slug and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Admin page slug.
	 * @param array  $args Optional. Arguments for the admin page. See {@see WP_Site_Identity_Admin_Menu_Page::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Admin_Menu_Page New admin page instance.
	 */
	public function create_admin_menu_page( $slug, $args = array() ) {
		return new WP_Site_Identity_Admin_Menu_Page( $slug, $args, $this->registry );
	}

	/**
	 * Instantiates a new admin submenu page object for the given slug and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Admin page slug.
	 * @param array  $args Optional. Arguments for the admin page. See {@see WP_Site_Identity_Admin_Submenu_Page::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Admin_Submenu_Page New admin page instance.
	 */
	public function create_admin_submenu_page( $slug, $args = array() ) {
		return new WP_Site_Identity_Admin_Submenu_Page( $slug, $args, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new admin pages.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Admin_Page_Registry Registry for new admin pages.
	 */
	public function registry() {
		return $this->registry;
	}
}
