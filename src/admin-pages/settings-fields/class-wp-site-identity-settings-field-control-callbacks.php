<?php
/**
 * WP_Site_Identity_Settings_Field_Control_Callbacks class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class containing settings field callbacks.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Settings_Field_Control_Callbacks {

	/**
	 * Renders a text control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value      Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field      Field instance.
	 * @param string                          $input_type Optional. Input type for the control. Default 'text'.
	 */
	public function render_text_control( $value, WP_Site_Identity_Settings_Field $field, $input_type = 'text' ) {
		$attrs = $this->make_base_attrs( $field, array(
			'check_description' => true,
		) );
		$props = $this->make_base_props( $field );

		?>
		<input type="<?php echo esc_attr( $input_type ); ?>"<?php $this->attrs( $attrs ); ?> value="<?php echo esc_attr( $value ); ?>"<?php $this->props( $props ); ?> />
		<?php

		$this->maybe_render_description( $field );
	}

	/**
	 * Renders a email control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_email_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$this->render_text_control( $value, $field, 'email' );
	}

	/**
	 * Renders a URL control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_url_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$this->render_text_control( $value, $field, 'url' );
	}

	/**
	 * Renders a color control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_color_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$extra_attrs                     = $field->get_extra_attrs();
		$extra_attrs['data-colorpicker'] = 'true';

		$field->set_extra_attrs( $extra_attrs );

		$this->render_text_control( $value, $field, 'text' );
	}

	/**
	 * Renders an image control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_image_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$extra_attrs                     = $field->get_extra_attrs();
		$extra_attrs['data-imagepicker'] = 'true';

		$field->set_extra_attrs( $extra_attrs );

		$image_url = '';
		$image_alt = '';
		if ( ! empty( $value ) ) {
			$image_url = (string) wp_get_attachment_image_url( $value, 'medium' );
			$image_alt = trim( strip_tags( get_post_meta( $value, '_wp_attachment_image_alt', true ) ) );
		}
		?>
		<div class="wpsi-image-control-wrap">
			<?php $this->render_text_control( $value, $field, 'number' ); ?>

			<div id="<?php echo esc_attr( $field->get_id_attr() . '-preview' ); ?>" class="wpsi-image-control-image" data-input-id="<?php echo esc_attr( $field->get_id_attr() ); ?>">
				<?php if ( ! empty( $image_url ) ) : ?>
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_alt ); ?>" />
				<?php endif; ?>
			</div>

			<?php if ( ! empty( $value ) ) : ?>
				<script type="application/json" id="<?php echo esc_attr( $field->get_id_attr() . '-attachment-data' ); ?>">
					<?php echo wp_json_encode( wp_prepare_attachment_for_js( $value ) ); ?>
				</script>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Renders a textarea control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_textarea_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field, array(
			'check_description' => true,
		) );
		$props = $this->make_base_props( $field );

		?>
		<textarea<?php $this->attrs( $attrs ); ?><?php $this->props( $props ); ?>><?php echo esc_textarea( $value ); ?></textarea>
		<?php

		$this->maybe_render_description( $field );
	}

	/**
	 * Renders a number control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_number_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field, array(
			'check_description' => true,
		) );
		$props = $this->make_base_props( $field );

		$type = $field->get_setting()->get_type();
		$min  = $field->get_setting()->get_min();
		$max  = $field->get_setting()->get_max();

		if ( is_float( $min ) || is_int( $min ) ) {
			if ( 'integer' === $type ) {
				$attrs['min'] = (int) $min;
			} else {
				$attrs['min'] = $min;
			}
		}

		if ( is_float( $max ) || is_int( $max ) ) {
			if ( 'integer' === $type ) {
				$attrs['max'] = (int) $max;
			} else {
				$attrs['max'] = $max;
			}
		}

		if ( ! isset( $attrs['step'] ) ) {
			if ( 'integer' === $type ) {
				$attrs['step'] = 1;
			} else {
				$attrs['step'] = 0.01;
			}
		}

		?>
		<input type="number"<?php $this->attrs( $attrs ); ?> value="<?php echo esc_attr( $value ); ?>"<?php $this->props( $props ); ?> />
		<?php

		$this->maybe_render_description( $field );
	}

	/**
	 * Renders a checkbox control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_checkbox_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field );
		$props = $this->make_base_props( $field );

		$description = $field->get_description();

		?>
		<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( $field->get_title() ); ?></legend>

			<input type="checkbox"<?php $this->attrs( $attrs ); ?> value="1"<?php checked( $value ); ?><?php $this->props( $props ); ?> />
			<label for="<?php echo esc_attr( $attrs['id'] ); ?>"><?php echo esc_html( $description ); ?></label>
		</fieldset>
		<?php
	}

	/**
	 * Renders a select control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_select_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field, array(
			'check_description' => true,
		) );
		$props = $this->make_base_props( $field );

		$choices = $field->get_setting()->get_choices();

		?>
		<select<?php $this->attrs( $attrs ); ?><?php $this->props( $props ); ?>>
			<?php foreach ( $choices as $choice => $label ) : ?>
				<option value="<?php echo esc_attr( $choice ); ?>"<?php selected( $value, $choice ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		$this->maybe_render_description( $field );
	}

	/**
	 * Renders a multiselect control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_multiselect_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field, array(
			'check_description' => true,
			'multiple'          => true,
		) );
		$props = $this->make_base_props( $field );

		$props['multiple'] = true;

		$choices = $field->get_setting()->get_choices();

		?>
		<select<?php $this->attrs( $attrs ); ?><?php $this->props( $props ); ?>>
			<?php foreach ( $choices as $choice => $label ) : ?>
				<option value="<?php echo esc_attr( $choice ); ?>"<?php $this->selected_multi( $value, $choice ); ?>><?php echo esc_html( $label ); ?></option>
			<?php endforeach; ?>
		</select>
		<?php

		$this->maybe_render_description( $field );
	}

	/**
	 * Renders a radio control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_radio_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field );
		$props = $this->make_base_props( $field );

		$choices = $field->get_setting()->get_choices();

		?>
		<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( $field->get_title() ); ?></legend>

			<ul id="<?php echo esc_attr( $attrs['id'] ); ?>" style="margin:0;">
				<?php foreach ( $choices as $choice => $label ) : ?>
					<?php
					$radio_attrs        = $attrs;
					$radio_attrs['id'] .= '-' . sanitize_title( $choice );
					?>
					<li>
						<input type="radio"<?php $this->attrs( $radio_attrs ); ?> value="<?php echo esc_attr( $choice ); ?>"<?php checked( $value, $choice ); ?><?php $this->props( $props ); ?> />
						<label for="<?php echo esc_attr( $radio_attrs['id'] ); ?>"><?php echo esc_html( $label ); ?></label>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php $this->maybe_render_description( $field ); ?>
		</fieldset>
		<?php
	}

	/**
	 * Renders a multibox control for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed                           $value Current value for the field.
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	public function render_multibox_control( $value, WP_Site_Identity_Settings_Field $field ) {
		$attrs = $this->make_base_attrs( $field, array(
			'multiple' => true,
		) );
		$props = $this->make_base_props( $field );

		$choices = $field->get_setting()->get_choices();

		?>
		<fieldset>
			<legend class="screen-reader-text"><?php echo esc_html( $field->get_title() ); ?></legend>

			<ul id="<?php echo esc_attr( $attrs['id'] ); ?>" style="margin:0;">
				<?php foreach ( $choices as $choice => $label ) : ?>
					<?php
					$checkbox_attrs        = $attrs;
					$checkbox_attrs['id'] .= '-' . sanitize_title( $choice );
					?>
					<li>
						<input type="checkbox"<?php $this->attrs( $checkbox_attrs ); ?> value="<?php echo esc_attr( $choice ); ?>"<?php $this->checked_multi( $value, $choice ); ?><?php $this->props( $props ); ?> />
						<label for="<?php echo esc_attr( $checkbox_attrs['id'] ); ?>"><?php echo esc_html( $label ); ?></label>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php $this->maybe_render_description( $field ); ?>
		</fieldset>
		<?php
	}

	/**
	 * Renders a control description for a field if available.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 */
	private function maybe_render_description( WP_Site_Identity_Settings_Field $field ) {
		$description = $field->get_description();
		if ( empty( $description ) ) {
			return;
		}

		$id = $field->get_id_attr() . '-description';

		?>
		<p id="<?php echo esc_attr( $id ); ?>" class="description"><?php echo wp_kses_data( $description ); ?></p>
		<?php
	}

	/**
	 * Creates base attributes for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 * @param array                           $args  {
	 *     Optional. Extra arguments for creating the attributes.
	 *
	 *     @type bool $check_description Whether to add 'aria-describedby' if there is a description. Default false.
	 *     @type bool $multiple          Whether to append a '[]' to the name attribute. Default false.
	 * }
	 * @return array Attributes as `$attr => $value` pairs.
	 */
	private function make_base_attrs( WP_Site_Identity_Settings_Field $field, array $args = array() ) {
		$attrs = array(
			'id'    => $field->get_id_attr(),
			'name'  => $field->get_name_attr(),
			'class' => implode( ' ', $field->get_css_classes() ),
		);

		if ( ! empty( $args['check_description'] ) ) {
			$description = $field->get_description();
			if ( ! empty( $description ) ) {
				$attrs['aria-describedby'] = $attrs['id'] . '-description';
			}
		}

		if ( ! empty( $args['multiple'] ) ) {
			$attrs['name'] .= '[]';
		}

		$extra_attrs = array_diff_key( $field->get_extra_attrs(), $attrs );

		if ( empty( $attrs['class'] ) ) {
			unset( $attrs['class'] );
		}

		return array_merge( $attrs, $extra_attrs );
	}

	/**
	 * Creates base props for a field.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field $field Field instance.
	 * @return array Props as `$prop => $enabled` pairs.
	 */
	private function make_base_props( WP_Site_Identity_Settings_Field $field ) {
		return $field->get_extra_props();
	}

	/**
	 * Prints an array of HTML attributes in a string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attrs Attributes as `$attr => $value` pairs.
	 */
	private function attrs( array $attrs ) {
		foreach ( $attrs as $attr => $value ) {
			if ( is_array( $value ) || is_object( $value ) ) {
				$value = wp_json_encode( $value );
			}

			if ( is_string( $value ) && false !== strpos( $value, '"' ) ) {
				echo ' ' . $attr . "='" . esc_attr( $value ) . "'"; // WPCS: XSS OK.
			} else {
				echo ' ' . $attr . '="' . esc_attr( $value ) . '"'; // WPCS: XSS OK.
			}
		}
	}

	/**
	 * Prints an array of HTML props in a string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $props Props as `$key => $enabled` pairs.
	 */
	private function props( array $props ) {
		foreach ( $props as $prop => $value ) {
			if ( $value ) {
				echo ' ' . $prop . "='" . esc_attr( $prop ) . "'";  // WPCS: XSS OK.
			}
		}
	}

	/**
	 * Outputs a HTML 'checked' attribute if the list of values contains a given choice.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $list   List of values.
	 * @param string $choice Choice to check if included.
	 */
	private function checked_multi( $list, $choice ) {
		$this->checked_selected_multi_helper( $list, $choice, 'checked' );
	}

	/**
	 * Outputs a HTML 'selected' attribute if the list of values contains a given choice.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $list   List of values.
	 * @param string $choice Choice to check if included.
	 */
	private function selected_multi( $list, $choice ) {
		$this->checked_selected_multi_helper( $list, $choice, 'selected' );
	}

	/**
	 * Outputs a HTML prop if the list of values contains a given choice.
	 *
	 * @since 1.0.0
	 *
	 * @param array  $list   List of values.
	 * @param string $choice Choice to check if included.
	 * @param string $type   Optional. Prop to print. Either 'selected' or 'checked'. Default 'checked'.
	 */
	private function checked_selected_multi_helper( $list, $choice, $type = 'checked' ) {
		$list = array_map( 'strval', (array) $list );

		if ( in_array( (string) $choice, $list, true ) ) {
			if ( 'selected' === $type ) {
				echo ' selected="selected"';
			} else {
				echo ' checked="checked"';
			}
		}
	}
}
