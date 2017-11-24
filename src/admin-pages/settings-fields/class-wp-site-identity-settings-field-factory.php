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
	 * Registry to use for instantiating settings fields.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Field_Registry
	 */
	protected $registry;

	/**
	 * Settings field control callbacks instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Field_Control_Callbacks
	 */
	protected $callbacks;

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

		$this->callbacks = new WP_Site_Identity_Settings_Field_Control_Callbacks();
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
		if ( ! isset( $args['render_callback'] ) ) {
			$args = $this->set_default_args_for_setting( $args, $setting );
		}

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

	/**
	 * Gets the settings fields control callbacks instance.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Field_Control_Callbacks Control callbacks instance.
	 */
	public function callbacks() {
		return $this->callbacks;
	}

	/**
	 * Gets the default render callback depending on settings field arguments.
	 *
	 * @since 1.0.0
	 *
	 * @param array                    $args    Arguments for the settings field. See {@see WP_Site_Identity_Settings_Field::__construct}
	 *                                          for a list of supported arguments.
	 * @param WP_Site_Identity_Setting $setting Setting the settings field is for.
	 * @return array Arguments including a render callback.
	 */
	protected function set_default_args_for_setting( array $args, WP_Site_Identity_Setting $setting ) {
		$type = isset( $args['type'] ) ? $args['type'] : $setting->get_type();

		switch ( $type ) {
			case 'boolean':
				$args['render_for_attr'] = false;
				$args['render_callback'] = array( $this->callbacks, 'render_checkbox_control' );
				break;
			case 'number':
			case 'integer':
				$args['render_for_attr'] = true;
				$args['render_callback'] = array( $this->callbacks, 'render_number_control' );
				if ( ! isset( $args['css_classes'] ) ) {
					$args['css_classes'] = array( 'small-text' );
				}
				break;
			case 'string':
				$args['render_for_attr'] = true;

				$choices = $setting->get_choices();
				if ( ! empty( $choices ) ) {
					if ( count( $choices ) <= 5 ) {
						$args['render_callback'] = array( $this->callbacks, 'render_radio_control' );
						$args['render_for_attr'] = false;
					} else {
						$args['render_callback'] = array( $this->callbacks, 'render_select_control' );
					}
				} else {
					$css_classes = array( 'regular-text' );

					$default = $setting->get_default();
					$format  = $setting->get_format();

					if ( 'email' === $format ) {
						$args['render_callback'] = array( $this->callbacks, 'render_email_control' );
					} elseif ( 'uri' === $format ) {
						$args['render_callback'] = array( $this->callbacks, 'render_url_control' );
						$css_classes[] = 'code';
					} else {
						if ( is_string( $default ) && false !== strpos( $default, "\n" ) ) {
							$args['render_callback'] = array( $this->callbacks, 'render_textarea_control' );
						} else {
							$args['render_callback'] = array( $this->callbacks, 'render_text_control' );
						}
					}

					if ( ! isset( $args['css_classes'] ) ) {
						$args['css_classes'] = $css_classes;
					}
				}
				break;
		}

		return $args;
	}
}
