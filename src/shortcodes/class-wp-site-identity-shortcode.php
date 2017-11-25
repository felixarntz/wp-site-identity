<?php
/**
 * WP_Site_Identity_Shortcode class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a basic shortcode.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Shortcode {

	/**
	 * Tag of the shortcode.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $tag = '';

	/**
	 * Render callback for the shortcode.
	 *
	 * @since 1.0.0
	 * @var callable
	 */
	protected $render_callback;

	/**
	 * Shortcode UI arguments.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $args = array();

	/**
	 * Parent registry for the shortcode.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Shortcode_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * Sets the shortcode properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                              $tag             Shortcode tag.
	 * @param callable                            $render_callback Callback for the shortcode. Will already receive parsed attributes, so
	 *                                                             including that logic is not necessary.
	 * @param array                               $args            Optional. Shortcode UI arguments. May contain a 'default' for each field.
	 *                                                             Default empty.
	 * @param WP_Site_Identity_Shortcode_Registry $registry        Optional. Parent registry for the shortcode.
	 */
	public function __construct( $tag, $render_callback, array $args = array(), WP_Site_Identity_Shortcode_Registry $registry = null ) {
		$this->tag             = $tag;
		$this->render_callback = $render_callback;
		$this->args            = $args;

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Shortcode_Registry();
		}
	}

	/**
	 * Checks whether the shortcode is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the shortcode is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_shortcode( $this->tag );
	}

	/**
	 * Registers the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_shortcode( $this );
	}

	/**
	 * Unregisters the shortcode.
	 *
	 * @since 1.0.0
	 */
	public function unregister() {
		$this->registry->unregister_shortcode( $this );
	}

	/**
	 * Renders the shortcode output for given attributes and content.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Optional. Shortcode content, or null if self-contained. Default null.
	 * @return string Shortcode output.
	 */
	public function render( $atts, $content = null ) {
		$atts = shortcode_atts( $this->get_defaults(), $atts, $this->registry->prefix( $this->tag ) );

		return call_user_func( $this->render_callback, $atts, $content );
	}

	/**
	 * Gets the tag of the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode tag.
	 */
	public function get_tag() {
		return $this->tag;
	}

	/**
	 * Gets the render callback for the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return callable Shortcode render callback.
	 */
	public function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Gets the UI arguments for the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return array Shortcode UI arguments.
	 */
	public function get_args() {
		return $this->args;
	}

	/**
	 * Action hook method for registering the shortcode UI.
	 *
	 * @since 1.0.0
	 * @internal
	 */
	public function action_register_ui() {
		if ( ! function_exists( 'shortcode_ui_register_for_shortcode' ) ) {
			return;
		}

		shortcode_ui_register_for_shortcode( $this->registry->prefix( $this->tag ), $this->args );
	}

	/**
	 * Gets default attribute values for the shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @return Shortcode defaults.
	 */
	protected function get_defaults() {
		$defaults = array();

		if ( ! empty( $this->args['attrs'] ) ) {
			foreach ( $this->args['attrs'] as $attr ) {
				$defaults[ $attr['attr'] ] = isset( $attr['default'] ) ? $attr['default'] : false;
			}
		}

		return $defaults;
	}
}
