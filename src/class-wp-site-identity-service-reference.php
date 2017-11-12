<?php
/**
 * WP_Site_Identity_Service_Reference class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Utility class that references a specific service.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Service_Reference {

	/**
	 * Identifier of the referenced service.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $id;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Identifier of the service to reference.
	 */
	public function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 * Gets the identifier of the referenced service.
	 *
	 * @since 1.0.0
	 *
	 * @return string Service identifier.
	 */
	public function get_id() {
		return $this->id;
	}
}
