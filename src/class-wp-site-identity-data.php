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
	 * @param WP_Site_Identity_Setting_Registry $setting_registry Setting registry to retrieve the data from.
	 */
	public function __construct( WP_Site_Identity_Setting_Registry $setting_registry ) {
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
}
