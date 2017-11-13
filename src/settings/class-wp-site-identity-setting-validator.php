<?php
/**
 * WP_Site_Identity_Setting_Validator class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for validating setting values.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Setting_Validator {

	/**
	 * Validates a value for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                    $value   Value to validate.
	 * @param WP_Site_Identity_Setting $setting Setting the value belongs to.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate( $value, WP_Site_Identity_Setting $setting ) {
		$validate_callback = $setting->get_validate_callback();

		if ( isset( $validate_callback ) ) {
			$validated_value = call_user_func( $validate_callback, $value, $setting );

			if ( is_wp_error( $validated_value ) ) {
				throw new WP_Site_Identity_Setting_Validation_Error_Exception( $validated_value->get_error_message() );
			}

			return $validated_value;
		}

		return $this->validate_value( $value, $this->generate_validation_args( $setting ) );
	}

	/**
	 * Generates the arguments array to pass to the validation method.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting for which to get validation arguments.
	 * @return array Default arguments for setting validation.
	 */
	protected function generate_validation_args( WP_Site_Identity_Setting $setting ) {
		return array_merge( array(
			'type'    => $setting->get_type(),
			'enum'    => array_keys( $setting->get_choices() ),
			'format'  => $setting->get_format(),
			'minimum' => $setting->get_min(),
			'maximum' => $setting->get_max(),
		), $setting->get_rest_schema() );
	}

	/**
	 * Validates a value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @param array $args  Validation arguments.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_value( $value, $args ) {
		if ( empty( $args['type'] ) ) {
			return $value;
		}

		switch ( $args['type'] ) {
			case 'array':
				return $this->validate_array_value( $value, $args );
			case 'object':
				return $this->validate_object_value( $value, $args );
			case 'boolean':
				return $this->validate_boolean_value( $value );
			case 'number':
			case 'integer':
				return $this->validate_numeric_value( $value, $args );
			case 'string':
				return $this->validate_string_value( $value, $args );
		}

		return $value;
	}

	/**
	 * Validates a value as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @param array $args  Validation arguments.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_array_value( $value, $args ) {
		if ( ! wp_is_numeric_array( $value ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $value, 'array' ) );
		}

		if ( ! empty( $args['items'] ) ) {
			foreach ( $value as $index => $v ) {
				$value[ $index ] = $this->validate_value( $v, $args['items'] );
			}
		}

		return $value;
	}

	/**
	 * Validates a value as an object.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @param array $args  Validation arguments.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_object_value( $value, $args ) {
		if ( $value instanceof stdClass ) {
			$value = (array) $value;
		}

		if ( ! is_array( $value ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $value, 'object' ) );
		}

		if ( isset( $args['properties'] ) ) {
			foreach ( $value as $property => $v ) {
				if ( isset( $args['properties'][ $property ] ) ) {
					$value[ $property ] = $this->validate_value( $v, $args['properties'][ $property ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Validates a value as a boolean.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_boolean_value( $value ) {
		if ( ! rest_is_boolean( $value ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $value, 'boolean' ) );
		}

		return $value;
	}

	/**
	 * Validates a value as a float or integer.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @param array $args  Validation arguments.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_numeric_value( $value, $args ) {
		if ( ! is_numeric( $value ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $value, $args['type'] ) );
		}

		if ( 'integer' === $args['type'] && round( floatval( $value ) ) !== floatval( $value ) ) {
			/* translators: 1: value, 2: type name */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not of type %2$s.', 'wp-site-identity' ), $value, 'integer' ) );
		}

		if ( isset( $args['minimum'] ) && ( is_float( $args['minimum'] ) || is_int( $args['minimum'] ) ) && $value < $args['minimum'] ) {
			/* translators: 1: value, 2: minimum number */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s must be greater than or equal to %2$s.', 'wp-site-identity' ), $value, number_format_i18n( $args['minimum'] ) ) );
		}

		if ( isset( $args['maximum'] ) && ( is_float( $args['maximum'] ) || is_int( $args['maximum'] ) ) && $value > $args['maximum'] ) {
			/* translators: 1: value, 2: maximum number */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s must be less than or equal to %2$s.', 'wp-site-identity' ), $value, number_format_i18n( $args['maximum'] ) ) );
		}

		return $value;
	}

	/**
	 * Validates a value as a string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @param array $args  Validation arguments.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	protected function validate_string_value( $value, $args ) {
		$value = trim( (string) $value );

		if ( ! empty( $args['enum'] ) && ! in_array( $value, $args['enum'], true ) ) {
			/* translators: 1: value, 2: list of valid values */
			throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%1$s is not one of %2$s.', 'wp-site-identity' ), $value, implode( ', ', $args['enum'] ) ) );
		}

		if ( ! empty( $args['format'] ) ) {
			switch ( $args['format'] ) {
				case 'date-time':
					if ( ! rest_parse_date( $value ) ) {
						/* translators: %s: value */
						throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%s is not a valid date.', 'wp-site-identity' ), $value ) );
					}
					break;
				case 'email':
					if ( ! is_email( $value ) ) {
						/* translators: %s: value */
						throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%s is not a valid email address.', 'wp-site-identity' ), $value ) );
					}
					break;
				case 'ip':
					if ( ! rest_is_ip_address( $value ) ) {
						/* translators: %s: value */
						throw new WP_Site_Identity_Setting_Validation_Error_Exception( sprintf( __( '%s is not a valid IP address.', 'wp-site-identity' ), $value ) );
					}
					break;
			}
		}

		return $value;
	}
}
