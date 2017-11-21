<?php
/**
 * WP_Site_Identity_Setting_Factory class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for instantiating setting objects.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Setting_Factory {

	/**
	 * Registry to use for instantiating settings.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting_Registry $registry Optional. Registry to use.
	 */
	public function __construct( WP_Site_Identity_Setting_Registry $registry = null ) {
		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Setting_Registry();
		}
	}

	/**
	 * Instantiates a new setting object for the given name and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting name.
	 * @param array  $args Optional. Arguments for the setting. See {@see WP_Site_Identity_Setting::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Setting New setting instance.
	 */
	public function create_setting( $name, $args = array() ) {
		if ( ! isset( $args['default'] ) ) {
			$args = $this->set_default_default_for_type( $args );
		}

		return new WP_Site_Identity_Setting( $name, $args, $this->registry );
	}

	/**
	 * Instantiates a new aggregate setting object for the given name and arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting name.
	 * @param array  $args Optional. Arguments for the setting. See {@see WP_Site_Identity_Aggregate_Setting::__construct}
	 *                     for a list of supported arguments.
	 * @return WP_Site_Identity_Setting New setting instance.
	 */
	public function create_aggregate_setting( $name, $args = array() ) {
		$args['type'] = 'object';
		$args['default'] = array();

		return new WP_Site_Identity_Aggregate_Setting( $name, $args, $this->registry );
	}

	/**
	 * Gets the registry to use for creating new settings.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Registry Registry for new settings.
	 */
	public function registry() {
		return $this->registry;
	}

	/**
	 * Gets the default default value depending on setting arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args Arguments for the setting. See {@see WP_Site_Identity_Aggregate_Setting::__construct}
	 *                    for a list of supported arguments.
	 * @return array Arguments including a default attribute.
	 */
	protected function set_default_default_for_type( array $args ) {
		$type = isset( $args['type'] ) ? $args['type'] : '';
		$min  = isset( $args['min'] ) ? $args['min'] : false;

		switch ( $type ) {
			case 'object':
			case 'array':
				$args['default'] = array();
				break;
			case 'boolean':
				$args['default'] = false;
				break;
			case 'number':
				if ( is_float( $min ) || is_int( $min ) ) {
					$args['default'] = (float) $min;
				} else {
					$args['default'] = 0.0;
				}
				break;
			case 'integer':
				if ( is_float( $min ) || is_int( $min ) ) {
					$args['default'] = (int) $min;
				} else {
					$args['default'] = 0;
				}
				break;
			case 'string':
				$args['default'] = '';
				break;
		}

		return $args;
	}
}
