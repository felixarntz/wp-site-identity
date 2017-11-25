<?php
/**
 * WP_Site_Identity_Standard_Shortcode_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering shortcodes.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Shortcode_Registry implements WP_Site_Identity_Shortcode_Registry {

	/**
	 * Prefix to use for all shortcode tags within WordPress.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * All registered shortcodes as `$tag => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $shortcodes = array();

	/**
	 * Factory to create shortcode objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Shortcode_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 *
	 * Sets the prefix to use for registered shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @param string $prefix Prefix to use for all shortcode tags within WordPress.
	 */
	public function __construct( $prefix ) {
		$this->prefix = $prefix;

		$this->factory = new WP_Site_Identity_Shortcode_Factory( $this );
	}

	/**
	 * Gets all registered shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$tag => $instance` pairs.
	 */
	public function get_all_shortcodes() {
		return $this->shortcodes;
	}

	/**
	 * Gets a registered shortcode instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Tag of the shortcode.
	 * @return WP_Site_Identity_Shortcode Registered shortcode.
	 *
	 * @throws WP_Site_Identity_Shortcode_Not_Found_Exception Thrown when a shortcode cannot be found.
	 */
	public function get_shortcode( $tag ) {
		if ( ! isset( $this->shortcodes[ $tag ] ) ) {
			/* translators: %s: shortcode tag */
			throw new WP_Site_Identity_Shortcode_Not_Found_Exception( sprintf( __( 'The shortcode with the tag %s could not be found.', 'wp-site-identity' ), $tag ) );
		}

		return $this->shortcodes[ $tag ];
	}

	/**
	 * Checks whether a shortcode is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Tag of the shortcode.
	 * @return bool True if the shortcode is registered, false otherwise.
	 */
	public function has_shortcode( $tag ) {
		return isset( $this->shortcodes[ $tag ] );
	}

	/**
	 * Registers a new shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Shortcode $shortcode Shortcode to register.
	 */
	public function register_shortcode( WP_Site_Identity_Shortcode $shortcode ) {
		$tag = $shortcode->get_tag();

		$this->shortcodes[ $tag ] = $shortcode;

		add_shortcode( $this->prefix( $tag ), array( $shortcode, 'render' ) );
		add_action( 'register_shortcode_ui', array( $shortcode, 'action_register_ui' ) );
	}

	/**
	 * Unregisters an existing shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Shortcode $shortcode Shortcode to unregister.
	 */
	public function unregister_shortcode( WP_Site_Identity_Shortcode $shortcode ) {
		$tag = $shortcode->get_tag();

		if ( ! isset( $this->shortcodes[ $tag ] ) ) {
			return;
		}

		remove_shortcode( $this->prefix( $tag ) );
		remove_action( 'register_shortcode_ui', array( $shortcode, 'action_register_ui' ) );

		unset( $this->shortcodes[ $tag ] );
	}

	/**
	 * Gets the factory to create shortcode objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Shortcode_Factory Factory to create shortcode objects.
	 */
	public function factory() {
		return $this->factory;
	}

	/**
	 * Prefixes a shortcode tag.
	 *
	 * If no tag is given, the prefix is simply returned.
	 *
	 * @since 1.0.0
	 *
	 * @param string $tag Shortcode tag to prefix.
	 * @return string Prefixed shortcode tag.
	 */
	public function prefix( $tag = '' ) {
		return $this->prefix . $tag;
	}
}
