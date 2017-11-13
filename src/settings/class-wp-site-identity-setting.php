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
	 *                                        WP_Site_Identity_Setting_Validation_Error_Exception. The
	 *                                        callback is passed the value as first parameter and the
	 *                                        setting object as second parameter. Default null.
	 *     @type callable  $sanitize_callback Sanitization callback for the setting. The callback is
	 *                                        passed the value as first parameter and the setting
	 *                                        object as second parameter. Default null.
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
	 * Gets the validation callback for the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Validation callback, or null if none set.
	 */
	public function get_validate_callback() {
		return $this->validate_callback;
	}

	/**
	 * Gets the sanitization callback for the setting.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Sanitization callback, or null if none set.
	 */
	public function get_sanitize_callback() {
		return $this->sanitize_callback;
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
}
