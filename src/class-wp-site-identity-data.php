<?php
/**
 * WP_Site_Identity_Data class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class for an easy-to-use access point for plugin data.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Data {

	/**
	 * Prefix to use for all CSS classes.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Setting registry containing the data.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Registry
	 */
	protected $setting_registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string                            $prefix           Prefix to use for all CSS classes.
	 * @param WP_Site_Identity_Setting_Registry $setting_registry Setting registry to retrieve the data from.
	 */
	public function __construct( $prefix, WP_Site_Identity_Setting_Registry $setting_registry ) {
		$this->prefix           = $prefix;
		$this->setting_registry = $setting_registry;
	}

	/**
	 * Retrieves the value for a specific identifier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting identifier.
	 * @return mixed Current value.
	 */
	public function get( $name ) {
		return $this->setting_registry->get_value( $name );
	}

	/**
	 * Retrieves the value for a specific identifier as HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting identifier.
	 * @return string Current value as HTML.
	 */
	public function get_as_html( $name ) {
		$value = $this->get( $name );
		$class = $this->get_css_class( $name );

		return '<span class="' . esc_attr( $class ) . '">' . $value . '</span>';
	}

	/**
	 * Gets the CSS class for a specific identifier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting identifier.
	 * @return string CSS class.
	 */
	public function get_css_class( $name ) {
		return str_replace( '_', '-', $this->prefix . $name );
	}
}
