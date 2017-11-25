<?php
/**
 * WP_Site_Identity_Shortcode_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating shortcode objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Shortcode_Factory {

	/**
	 * Registry to use for instantiating shortcodes.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Shortcode_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Shortcode_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Shortcode_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Shortcode_Registry();
		}
	}

	/**
	 * Instantiates a new shortcode object for the given tag and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string   $tag             Shortcode tag.
	 * @param callable $render_callback Callback for the shortcode. Will already receive parsed attributes, so
	 *                                  including that logic is not necessary.
	 * @param array    $args            Optional. Arguments for the shortcode. See {@see WP_Site_Identity_Shortcode::__construct}
	 *                                  for a list of supported arguments.
	 * @return WP_Site_Identity_Shortcode New shortcode instance.
	 */
	public function create_shortcode( $tag, $render_callback, $args = array() ) {
		return new WP_Site_Identity_Shortcode( $tag, $render_callback, $args, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new shortcodes.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Shortcode_Registry Registry for new shortcodes.
	 */
	public function registry() {
		return $this->registry;
	}
}
