<?php
/**
 * WP_Site_Identity_Setting class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a setting.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Setting {

	/**
	 * Name of the setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $name;

	/**
	 * Title for the setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Description for the setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $description = '';

	/**
	 * Type of the setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $type = 'string';

	/**
	 * Validation callback for the setting.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $validate_callback = null;

	/**
	 * Sanitization callback for the setting.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $sanitize_callback = null;

	/**
	 * Default value for the setting.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $default = false;

	/**
	 * Whether to show the setting in the REST API.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $show_in_rest = false;

	/**
	 * Schema to use for the setting in the REST API.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $rest_schema = array();

	/**
	 * Choices to select from, as `$value => $label` pairs.
	 * Will only be considered if $type is 'string'. Used
	 * in default validation and the REST API schema,
	 * if applicable.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $choices = array();

	/**
	 * Special format to require for the setting value.
	 * Will only be considered if $type is 'string'. Used
	 * in default validation and the REST API schema,
	 * if applicable.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $format = '';

	/**
	 * Minimum value to require for the setting value.
	 * Will only be considered if $type is 'integer' or
	 * 'number'. Used in default validation and the REST
	 * API schema, if applicable.
	 *
	 * @since 1.0.0
	 * @var int|float|bool
	 */
	protected $min = false;

	/**
	 * Maximum value to require for the setting value.
	 * Will only be considered if $type is 'integer' or
	 * 'number'. Used in default validation and the REST
	 * API schema, if applicable.
	 *
	 * @since 1.0.0
	 * @var int|float|bool
	 */
	protected $max = false;

	/**
	 * Constructor.
	 *
	 * Sets the setting properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting name.
	 * @param array  $args {
	 *     Optional. Arguments for the setting.
	 *
	 *     @type string    $title             Title for the setting. Default empty string.
	 *     @type string    $description       Description for the setting. Default empty string.
	 *     @type string    $type              Type of the setting. Either 'string', 'number', 'integer',
	 *                                        'boolean', 'array' or 'object'. Default 'string'.
	 *     @type callable  $validate_callback Validation callback for the setting. In case of a
	 *                                        validation error, it must return a WP_Error or throw a
	 *                                        WP_Site_Identity_Setting_Validation_Error_Exception.
	 *                                        Default null.
	 *     @type callable  $sanitize_callback Sanitization callback for the setting. Default null.
	 *     @type mixed     $default           Default value for the setting. Default false.
	 *     @type bool      $show_in_rest      Whether to show the setting in the REST API. Default false.
	 *     @type array     $rest_schema       Schema to use for the setting in the REST API, if $show_in_rest
	 *                                        is true. Default empty array.
	 *     @type array     $choices           Choices to select from, as `$value => $label` pairs. Will only
	 *                                        be considered if $type is 'string'. Used in default validation
	 *                                        and the REST API schema, if applicable. Default empty array.
	 *     @type string    $format            Special format to require for the setting value. Will only be
	 *                                        considered if $type is 'string'. Used in default validation
	 *                                        and the REST API schema, if applicable. Default empty string.
	 *     @type int|float $min               Minimum value to require for the setting value. Will only be
	 *                                        considered if $type is 'integer' or 'number'. Used in default
	 *                                        validation and the REST API schema, if applicable. Default false.
	 *     @type int|float $max               Maximum value to require for the setting value. Will only be
	 *                                        considered if $type is 'integer' or 'number'. Used in default
	 *                                        validation and the REST API schema, if applicable. Default false.
	 * }
	 */
	public function __construct( $name, array $args ) {
		$this->name = $name;

		$arg_keys = array( 'title', 'description', 'type', 'validate_callback', 'sanitize_callback', 'default', 'show_in_rest', 'rest_schema', 'choices', 'format', 'min', 'max' );

		foreach ( $arg_keys as $arg_key ) {
			if ( ! empty( $args[ $arg_key ] ) ) {
				$this->$arg_key = $args[ $arg_key ];
			}
		}
	}

	/**
	 * Gets the name of the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string Setting name.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Gets the title of the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string Setting title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Gets the description of the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string Setting description.
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Gets the type of the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return string Setting type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Validates a value for the setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to validate.
	 * @return mixed Validated value.
	 *
	 * @throws WP_Site_Identity_Setting_Validation_Error_Exception Thrown when a validation error occurs.
	 */
	public function validate( $value ) {
		if ( isset( $this->validate_callback ) ) {
			$validated_value = call_user_func( $this->validate_callback, $value );

			if ( is_wp_error( $validated_value ) ) {
				throw new WP_Site_Identity_Setting_Validation_Error_Exception( $validated_value->get_error_message() );
			}

			return $validated_value;
		}

		return $this->validate_value( $value, $this->generate_validate_sanitize_args() );
	}

	/**
	 * Sanitizes a value for the setting.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	public function sanitize( $value ) {
		if ( isset( $this->sanitize_callback ) ) {
			return call_user_func( $this->sanitize_callback, $value );
		}

		$args = array_merge( array(
			'type'    => $this->type,
			'enum'    => array_keys( $this->choices ),
			'format'  => $this->format,
			'minimum' => $this->min,
			'maximum' => $this->max,
		), $this->rest_schema );

		return $this->sanitize_value( $value, $args );
	}

	/**
	 * Gets the default value for the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Setting default value.
	 */
	public function get_default() {
		return $this->default;
	}

	/**
	 * Checks whether the setting should be shown in the REST API.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the setting should be shown in the REST API, false otherwise.
	 */
	public function show_in_rest() {
		return (bool) $this->show_in_rest;
	}

	/**
	 * Gets the schema to use for the setting in the REST API.
	 *
	 * @since 1.0.0
	 *
	 * @return mixed Setting schema to use in the REST API.
	 */
	public function get_rest_schema() {
		return $this->rest_schema;
	}

	/**
	 * Gets the available choices to select from, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return array Choices as `$value => $label` pairs.
	 */
	public function get_choices() {
		return $this->choices;
	}

	/**
	 * Gets the required special format, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return string Special format.
	 */
	public function get_format() {
		return $this->format;
	}

	/**
	 * Gets the minimum required value, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return int|float|bool Minimum value.
	 */
	public function get_min() {
		return $this->min;
	}

	/**
	 * Gets the maximum required value, if any.
	 *
	 * @since 1.0.0
	 *
	 * @return int|float|bool Maximum value.
	 */
	public function get_max() {
		return $this->max;
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

	/**
	 * Generates the arguments array to pass to the default validation and sanitization methods.
	 *
	 * @since 1.0.0
	 *
	 * @return array Default arguments for setting validation and sanitization.
	 */
	protected function generate_validate_sanitize_args() {
		return array_merge( array(
			'type'    => $this->type,
			'enum'    => array_keys( $this->choices ),
			'format'  => $this->format,
			'minimum' => $this->min,
			'maximum' => $this->max,
		), $this->rest_schema );
	}
}
