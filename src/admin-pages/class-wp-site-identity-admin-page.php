<?php
/**
 * WP_Site_Identity_Admin_Page class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a basic admin page.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Admin_Page {

	/**
	 * Slug of the admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Title of the admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Required capability to access the admin page.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $capability = '';

	/**
	 * Render callback for the admin page.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $render_callback = null;

	/**
	 * Handle callback for the admin page.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $handle_callback = null;

	/**
	 * Enqueue callback for the admin page.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $enqueue_callback = null;

	/**
	 * Parent registry for the admin page.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Admin_Page_Registry
	 */
	protected $registry;

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
	 * }
	 * @param WP_Site_Identity_Admin_Page_Registry $registry Optional. Parent registry for the admin page.
	 */
	public function __construct( $slug, array $args = array(), WP_Site_Identity_Admin_Page_Registry $registry = null ) {
		$this->slug = $slug;

		if ( ! empty( $args['title'] ) ) {
			$this->title = $args['title'];
		} else {
			$this->title = ucwords( str_replace( array( '-', '_' ), ' ', $this->slug ) );
		}

		if ( ! empty( $args['capability'] ) ) {
			$this->capability = $args['capability'];
		} else {
			$this->capability = 'manage_options';
		}

		$callbacks = array( 'render_callback', 'handle_callback', 'enqueue_callback' );

		foreach ( $callbacks as $callback ) {
			if ( isset( $args[ $callback ] ) ) {
				$this->$callback = $args[ $callback ];
			}
		}

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Admin_Page_Registry();
		}
	}

	/**
	 * Checks whether the admin page is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the admin page is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_admin_page( $this->name );
	}

	/**
	 * Registers the admin page.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_admin_page( $this );
	}

	/**
	 * Unregisters the admin page.
	 *
	 * @since 1.0.0
	 */
	public function unregister() {
		$this->registry->unregister_admin_page( $this );
	}

	/**
	 * Gets the slug of the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin page slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets the title of the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin page title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Gets the capability required to access the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin page capability.
	 */
	public function get_capability() {
		return $this->capability;
	}

	/**
	 * Gets the render callback for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Render callback, or null if none set.
	 */
	public function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Gets the handle callback for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Handle callback, or null if none set.
	 */
	public function get_handle_callback() {
		return $this->handle_callback;
	}

	/**
	 * Gets the enqueue callback for the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Enqueue callback, or null if none set.
	 */
	public function get_enqueue_callback() {
		return $this->enqueue_callback;
	}

	/**
	 * Gets the URL to the admin page.
	 *
	 * @since 1.0.0
	 *
	 * @return string URL to the admin page.
	 */
	public function get_url() {
		return $this->registry->get_url_to_admin_page( $this );
	}
}
