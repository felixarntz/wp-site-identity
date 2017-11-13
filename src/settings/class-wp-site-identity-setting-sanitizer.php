<?php
/**
 * WP_Site_Identity_Setting_Sanitizer class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for sanitizing setting values.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Setting_Sanitizer {

	/**
	 * Sanitizes a value for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                    $value   Value to sanitize.
	 * @param WP_Site_Identity_Setting $setting Setting the value belongs to.
	 * @return mixed Sanitized value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function sanitize( $value, WP_Site_Identity_Setting $setting ) {
		$sanitize_callback = $setting->get_sanitize_callback();

		if ( isset( $sanitize_callback ) ) {
			$sanitized_value = call_user_func( $sanitize_callback, $value );

			if ( is_wp_error( $sanitized_value ) ) {
				throw new WP_Site_Identity_Setting_Validation_Error_Exception( $sanitized_value->get_error_message() );
			}

			return $sanitized_value;
		}

		return $this->sanitize_value( $value, $this->generate_sanitization_args( $setting ) );
	}

	/**
	 * Generates the arguments array to pass to the sanitization method.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting for which to get sanitization arguments.
	 * @return array Default arguments for setting sanitization.
	 */
	protected function generate_sanitization_args( WP_Site_Identity_Setting $setting ) {
		return array_merge( array(
			'type'    => $setting->get_type(),
			'format'  => $setting->get_format(),
		), $setting->get_rest_schema() );
	}

	/**
	 * Sanitizes a value.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @param array $args  Sanitization arguments.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_value( $value, $args ) {
		if ( empty( $args['type'] ) ) {
			return $value;
		}

		switch ( $args['type'] ) {
			case 'array':
				return $this->sanitize_array_value( $value, $args );
			case 'object':
				return $this->sanitize_object_value( $value, $args );
			case 'boolean':
				return $this->sanitize_boolean_value( $value );
			case 'number':
			case 'integer':
				return $this->sanitize_numeric_value( $value, $args );
			case 'string':
				return $this->sanitize_string_value( $value, $args );
		}

		return $value;
	}

	/**
	 * Sanitizes a value as an array.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @param array $args  Sanitization arguments.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_array_value( $value, $args ) {
		if ( ! empty( $args['items'] ) ) {
			foreach ( $value as $index => $v ) {
				$value[ $index ] = $this->sanitize_value( $v, $args['items'] );
			}
		}

		return array_values( $value );
	}

	/**
	 * Sanitizes a value as an object.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @param array $args  Sanitization arguments.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_object_value( $value, $args ) {
		if ( $value instanceof stdClass ) {
			$value = (array) $value;
		}

		if ( isset( $args['properties'] ) ) {
			foreach ( $value as $property => $v ) {
				if ( isset( $args['properties'][ $property ] ) ) {
					$value[ $property ] = $this->sanitize_value( $v, $args['properties'][ $property ] );
				}
			}
		}

		return $value;
	}

	/**
	 * Sanitizes a value as a boolean.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_boolean_value( $value ) {
		return rest_sanitize_boolean( $value );
	}

	/**
	 * Sanitizes a value as a float or integer.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @param array $args  Sanitization arguments.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_numeric_value( $value, $args ) {
		if ( 'integer' === $args['type'] ) {
			return (int) $value;
		}

		return (float) $value;
	}

	/**
	 * Sanitizes a value as a string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @param array $args  Sanitization arguments.
	 * @return mixed Sanitized value.
	 */
	protected function sanitize_string_value( $value, $args ) {
		if ( ! empty( $args['format'] ) ) {
			switch ( $args['format'] ) {
				case 'date-time':
				case 'email':
				case 'ip':
					return sanitize_text_field( $value );
				case 'uri':
					return esc_url_raw( $value );
			}
		}

		return (string) $value;
	}
}
