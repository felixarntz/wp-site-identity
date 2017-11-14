<?php
/**
 * WP_Site_Identity_Settings_Form_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating settings form objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Form_Factory {

	/**
	 * Registry to use for instantiating setting forms.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Form_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Settings_Form_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Settings_Form_Registry();
		}
	}

	/**
	 * Instantiates a new settings form object for the given name and setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @param string                            $name             Settings form name.
	 * @param WP_Site_Identity_Setting_Registry $setting_registry Setting registry the settings form should display fields for.
	 * @return WP_Site_Identity_Settings_Form New settings form instance.
	 */
	public function create_form( $name, WP_Site_Identity_Setting_Registry $setting_registry ) {
		return new WP_Site_Identity_Settings_Form( $name, $setting_registry, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new settings.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Form_Registry Registry for new settings.
	 */
	public function registry() {
		return $this->registry;
	}
}
