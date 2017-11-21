<?php
/**
 * WP_Site_Identity_Settings_Field_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating settings field objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Field_Factory {

	/**
	 * Registry to use for instantiating setting fields.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Field_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Settings_Field_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Settings_Field_Registry();
		}
	}

	/**
	 * Instantiates a new settings field object for the given name and setting registry.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting the settings field is for.
	 * @param array                    $args    Optional. Arguments for the settings field. See {@see WP_Site_Identity_Settings_Field::__construct}
	 *                                          for a list of supported arguments.
	 * @return WP_Site_Identity_Settings_Field New settings field instance.
	 */
	public function create_field( WP_Site_Identity_Setting $setting, array $args = array() ) {
		return new WP_Site_Identity_Settings_Field( $setting, $args, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Field_Registry Registry for new settings fields.
	 */
	public function registry() {
		return $this->registry;
	}
}
