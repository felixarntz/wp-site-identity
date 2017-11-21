<?php
/**
 * WP_Site_Identity_Settings_Section_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating settings section objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Section_Factory {

	/**
	 * Registry to use for instantiating setting sections.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Section_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Section_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Settings_Section_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Settings_Section_Registry();
		}
	}

	/**
	 * Instantiates a new settings section object for the given name and setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Settings section name.
	 * @param array  $args Optional. Arguments for the settings section. See {@see WP_Site_Identity_Settings_Section::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Settings_Section New settings section instance.
	 */
	public function create_section( $name, array $args = array() ) {
		return new WP_Site_Identity_Settings_Section( $name, $args, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new settings sections.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Section_Registry Registry for new settings sections.
	 */
	public function registry() {
		return $this->registry;
	}
}
