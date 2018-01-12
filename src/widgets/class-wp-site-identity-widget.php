<?php
/**
 * WP_Site_Identity_Widget class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a basic widget.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Widget extends WP_Widget {

	/**
	 * Render callback for the widget.
	 *
	 * @since 1.0.0
	 * @var callable
	 */
	protected $render_callback;

	/**
	 * Widget field data.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $fields = array();

	/**
	 * Parent registry for the widget.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Widget_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * Sets the widget properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                           $id_base         Base ID for the widget, lowercase and unique. If left empty,
	 *                                                          a portion of the widget's class name will be used. Has to be unique.
	 * @param string                           $name            Name for the widget displayed on the configuration page.
	 * @param string                           $description     Widget description.
	 * @param callable                         $render_callback Widget rendering callback. Must return its content.
	 * @param array                            $fields          Optional. Fields for the widget as `$attr => $args` pairs. Default empty array.
	 * @param WP_Site_Identity_Widget_Registry $registry        Optional. Parent registry for the widget.
	 */
	public function __construct( $id_base, $name, $description, $render_callback, array $fields = array(), WP_Site_Identity_Widget_Registry $registry = null ) {
		$this->render_callback = $render_callback;
		$this->fields          = $this->validate_fields( $fields );

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Widget_Registry();
		}

		parent::__construct( $id_base, $name, array(
			'classname'                   => 'widget_' . $id_base,
			'description'                 => $description,
			'customize_selective_refresh' => true,
		) );
	}

	/**
	 * Checks whether the widget is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the widget is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_widget( $this->tag );
	}

	/**
	 * Registers the widget.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_widget( $this );
	}

	/**
	 * Unregisters the widget.
	 *
	 * @since 1.0.0
	 */
	public function unregister() {
		$this->registry->unregister_widget( $this );
	}

	/**
	 * Outputs the content for the current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $args     Display arguments including 'before_title', 'after_title',
	 *                        'before_widget', and 'after_widget'.
	 * @param array $instance Settings for the current widget instance.
	 */
	public function widget( $args, $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->get_defaults() );

		/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

		$output = call_user_func( $this->render_callback, $instance );

		if ( empty( $output ) ) {
			return;
		}

		echo $args['before_widget']; // WPCS: XSS OK.
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title']; // WPCS: XSS OK.
		}
		echo $output; // WPCS: XSS OK.
		echo $args['after_widget']; // WPCS: XSS OK.
	}

	/**
	 * Handles updating the settings for the current widget instance.
	 *
	 * @since 1.0.0
	 *
	 * @param array $new_instance New settings for this instance as input by the user via
	 *                            WP_Widget::form().
	 * @param array $old_instance Old settings for this instance.
	 * @return array Updated settings to save.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		foreach ( $this->fields as $attr => $field ) {
			switch ( $field['type'] ) {
				case 'checkbox':
					$instance[ $attr ] = isset( $new_instance[ $attr ] ) ? (bool) $new_instance[ $attr ] : false;
					break;
				case 'select':
					if ( ! empty( $meta['multiple'] ) ) {
						$new_instance[ $attr ] = (array) $new_instance[ $attr ];
						foreach ( $new_instance[ $attr ] as $value ) {
							if ( ! isset( $field['choices'][ $value ] ) ) {
								break 2;
							}
						}
						$instance[ $attr ] = $new_instance[ $attr ];
					} elseif ( isset( $field['choices'][ $new_instance[ $attr ] ] ) ) {
						$instance[ $attr ] = $new_instance[ $attr ];
					}
					break;
				case 'radio':
					if ( isset( $field['choices'][ $new_instance[ $attr ] ] ) ) {
						$instance[ $attr ] = $new_instance[ $attr ];
					}
					break;
				case 'number':
					if ( ! empty( $field['meta']['step'] ) && is_int( $field['meta']['step'] ) ) {
						$instance[ $attr ] = (int) $new_instance[ $attr ];
					} else {
						$instance[ $attr ] = (float) $new_instance[ $attr ];
					}
					break;
				case 'textarea':
					$instance[ $attr ] = sanitize_textarea_field( $new_instance[ $attr ] );
					break;
				case 'color':
					$color = sanitize_text_field( $new_instance[ $attr ] );
					if ( preg_match( '/#([a-f0-9]{3}){1,2}\b/i', $color ) ) {
						$instance[ $attr ] = $color;
					}
					break;
				case 'email':
					$email = is_email( sanitize_email( $new_instance[ $attr ] ) );
					if ( $email ) {
						$instance[ $attr ] = $email;
					}
					break;
				case 'url':
				case 'date':
				case 'text':
				default:
					$instance[ $attr ] = sanitize_text_field( $new_instance[ $attr ] );
			}
		}

		return $instance;
	}

	/**
	 * Outputs the settings form for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Current settings.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->get_defaults() );

		foreach ( $this->fields as $attr => $field ) {
			$meta = $this->attrs( $field['meta'] );

			$id   = $this->get_field_id( $attr );
			$name = $this->get_field_name( $attr );

			?>
			<?php if ( 'radio' === $field['type'] ) : ?>
				<fieldset>
			<?php else : ?>
				<p>
			<?php endif; ?>

				<?php if ( 'radio' === $field['type'] ) : ?>
					<legend><?php echo esc_html( $field['label'] ); ?></legend>
				<?php elseif ( 'checkbox' !== $field['type'] ) : ?>
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<?php endif; ?>

				<?php
				switch ( $field['type'] ) {
					case 'checkbox':
						?>
						<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat" value="1"<?php checked( $instance[ $attr ] ); ?><?php echo $meta; // WPCS: XSS OK. ?> />
						<?php
						break;
					case 'select':
						if ( ! empty( $meta['multiple'] ) ) {
							$name .= '[]';
						}
						?>
						<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat"<?php echo $meta; // WPCS: XSS OK. ?>>
							<?php foreach ( $field['choices'] as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $instance[ $attr ], $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php
						break;
					case 'radio':
						foreach ( $field['choices'] as $value => $label ) {
							?>
							<input type="radio" id="<?php echo esc_attr( $id . '-' . $value ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"<?php checked( $instance[ $attr ], $value ); ?><?php echo $meta; // WPCS: XSS OK. ?> />
							<label for="<?php echo esc_attr( $id . '-' . $value ); ?>"><?php echo esc_html( $label ); ?></label>
							<br />
							<?php
						}
						break;
					case 'number':
						?>
						<input type="number" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="tiny-text" value="<?php echo esc_attr( $instance[ $attr ] ); ?>"<?php echo $meta; // WPCS: XSS OK. ?> />
						<?php
						break;
					case 'textarea':
						?>
						<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat"<?php echo $meta; // WPCS: XSS OK. ?>><?php echo esc_textarea( $instance[ $attr ] ); ?></textarea>
						<?php
						break;
					case 'color':
					case 'email':
					case 'url':
					case 'date':
					case 'text':
					default:
						?>
						<input type="<?php echo esc_attr( $field['type'] ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="widefat" value="<?php echo esc_attr( $instance[ $attr ] ); ?>"<?php echo $meta; // WPCS: XSS OK. ?> />
						<?php
				}
				?>

				<?php if ( 'checkbox' === $field['type'] ) : ?>
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
				<?php endif; ?>

				<?php if ( ! empty( $field['description'] ) ) : ?>
					<br />
					<span class="description"><?php wp_kses_data( $field['description'] ); ?></span>
				<?php endif; ?>

			<?php if ( 'radio' === $field['type'] ) : ?>
				</fieldset>
			<?php else : ?>
				</p>
			<?php endif; ?>
			<?php
		}
	}

	/**
	 * Gets the ID base of the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return string Widget ID base.
	 */
	public function get_id_base() {
		$prefix = $this->registry->prefix();

		if ( 0 === strpos( $this->id_base, $prefix ) ) {
			return substr( $this->id_base, strlen( $prefix ) );
		}

		return $this->id_base;
	}

	/**
	 * Gets the render callback for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return callable Widget render callback.
	 */
	public function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Gets the field data for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return array Widget field data.
	 */
	public function get_fields() {
		return $this->fields;
	}

	/**
	 * Gets default attribute values for the widget.
	 *
	 * @since 1.0.0
	 *
	 * @return Widget defaults.
	 */
	protected function get_defaults() {
		$defaults = array();

		foreach ( $this->fields as $attr => $field ) {
			$defaults[ $attr ] = $field['default'];
		}

		return $defaults;
	}

	/**
	 * Transforms an array of attributes into an attribute string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attrs Array of `$key => $value` pairs.
	 * @return string Attribute string.
	 */
	protected function attrs( $attrs ) {
		$output = '';

		foreach ( $attrs as $attr => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$output .= ' ' . $attr . '="' . esc_attr( $attr ) . '"';
				}
			} else {
				if ( is_array( $value ) || is_object( $value ) ) {
					$value = wp_json_encode( $value );
				}

				if ( is_string( $value ) && false !== strpos( $value, '"' ) ) {
					$output .= ' ' . $attr . "='" . esc_attr( $value ) . "'";
				} else {
					$output .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
				}
			}
		}

		return $output;
	}

	/**
	 * Validates multiple widget fields.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields Fields to validate as `$attr => $args` pairs.
	 * @return array Validated fields.
	 */
	protected function validate_fields( $fields ) {
		foreach ( $fields as $attr => $field ) {
			$fields[ $attr ] = $this->validate_field( $field );
		}

		return $fields;
	}

	/**
	 * Validates a single widget field arguments set.
	 *
	 * @since 1.0.0
	 *
	 * @param array $field {
	 *     Field to validate.
	 *
	 *     @type string $label       Field label.
	 *     @type string $description Field description.
	 *     @type string $type        Field type.
	 *     @type mixed  $default     Default value for the field.
	 *     @type array  $choices     Array of `$value => $label` pairs if field is some kind of select.
	 *     @type array  $meta        Associative array of additional field parameters.
	 * }
	 * @return array Validated field.
	 */
	protected function validate_field( $field ) {
		$field = wp_parse_args( $field, array(
			'label'       => '',
			'description' => '',
			'type'        => '',
			'default'     => '',
			'choices'     => array(),
			'meta'        => array(),
		) );

		$valid_types = array(
			'text',
			'checkbox',
			'textarea',
			'radio',
			'select',
			'email',
			'url',
			'number',
			'date',
			'color',
		);

		if ( ! in_array( $field['type'], $valid_types, true ) ) {
			$field['type'] = 'text';
		}

		return $field;
	}
}
