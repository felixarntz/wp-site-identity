<?php
/**
 * WP_Site_Identity_Service_Container class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Plugin service container.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Service_Container {

	/**
	 * All service definitions part of the container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $definitions = array();

	/**
	 * All service instances already created by the container.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $instances = array();

	/**
	 * Gets a service.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Identifier of the service.
	 * @return mixed Service instance.
	 *
	 * @throws WP_Site_Identity_Service_Not_Found_Exception Thrown when the service cannot be found.
	 */
	public function get( $id ) {
		if ( ! isset( $this->instances[ $id ] ) ) {
			if ( ! isset( $this->definitions[ $id ] ) ) {
				/* translators: %s: service identifier */
				throw new WP_Site_Identity_Service_Not_Found_Exception( sprintf( __( 'The service with the identifier %s could not be found.', 'wp-site-identity' ), $id ) );
			}

			$reflected_class = new ReflectionClass( $this->definitions[ $id ]['class_name'] );

			$constructor = $reflected_class->getConstructor();
			if ( $constructor ) {
				$constructor_params = $this->definitions[ $id ]['constructor_params'];

				// Resolve service references.
				foreach ( $constructor_params as $index => $constructor_param ) {
					if ( is_a( $constructor_param, 'WP_Site_Identity_Service_Reference' ) ) {
						$constructor_params[ $index ] = $this->get( $constructor_param->get_id() );
					}
				}

				$this->instances[ $id ] = $reflected_class->newInstanceArgs( $constructor_params );
			} else {
				$this->instances[ $id ] = $reflected_class->newInstanceWithoutConstructor();
			}
		}

		return $this->instances[ $id ];
	}

	/**
	 * Checks whether a service can be retrieved.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Identifier of the service.
	 * @return bool True if the service can be retrieved, false otherwise.
	 */
	public function has( $id ) {
		return isset( $this->definitions[ $id ] );
	}

	/**
	 * Registers a new service.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id                 Identifier of the service.
	 * @param string $class_name         Class name of the service.
	 * @param array  $constructor_params Optional. Parameters to pass into the class constructor. Default empty array.
	 *
	 * @throws WP_Site_Identity_Service_Already_Registered_Exception Thrown when a service with the $id already exists.
	 */
	public function register( $id, $class_name, $constructor_params = array() ) {
		if ( isset( $this->definitions[ $id ] ) ) {
			/* translators: %s: service identifier */
			throw new WP_Site_Identity_Service_Already_Registered_Exception( sprintf( __( 'The service with the identifier %s is already registered.', 'wp-site-identity' ), $id ) );
		}

		$this->definitions[ $id ] = array(
			'class_name'         => $class_name,
			'constructor_params' => $constructor_params,
		);
	}
}
