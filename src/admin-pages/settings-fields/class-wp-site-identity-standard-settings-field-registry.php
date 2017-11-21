<?php
/**
 * WP_Site_Identity_Standard_Settings_Field_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering settings fields.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Settings_Field_Registry implements WP_Site_Identity_Settings_Field_Registry {

	/**
	 * All registered settings fields as `$slug => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Parent settings form to register settings fields for.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Form
	 */
	protected $form;

	/**
	 * Factory to create settings field objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Field_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form $form Parent settings form.
	 */
	public function __construct( WP_Site_Identity_Settings_Form $form ) {
		$this->form    = $form;
		$this->factory = new WP_Site_Identity_Settings_Field_Factory( $this );
	}

	/**
	 * Gets all registered settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_fields() {
		return $this->fields;
	}

	/**
	 * Gets a registered settings field instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings field.
	 * @return WP_Site_Identity_Settings_Field Registered settings field.
	 *
	 * @throws WP_Site_Identity_Settings_Field_Not_Found_Exception Thrown when a settings field cannot be found.
	 */
	public function get_field( $slug ) {
		if ( ! isset( $this->fields[ $slug ] ) ) {
			/* translators: %s: settings field slug */
			throw new WP_Site_Identity_Settings_Field_Not_Found_Exception( sprintf( __( 'The settings field with the slug %s could not be found.', 'wp-site-identity' ), $slug ) );
		}

		return $this->fields[ $slug ];
	}

	/**
	 * Checks whether a settings field is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings field.
	 * @return bool True if the settings field is registered, false otherwise.
	 */
	public function has_field( $slug ) {
		return isset( $this->fields[ $slug ] );
	}

	/**
	 * Registers a new settings field.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Field $field Settings field to register.
	 */
	public function register_field( WP_Site_Identity_Settings_Field $field ) {
		$slug = $field->get_slug();

		$this->fields[ $slug ] = $field;

		$args = array();
		if ( $field->has_for_attr() ) {
			$args['label_for'] = $field->get_id_attr();
		}

		add_settings_field( $field->get_slug(), $field->get_title(), array( $field, 'render' ), $this->form->get_slug(), $field->get_section_slug(), $args );
	}

	/**
	 * Gets the factory to create settings field objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Field_Factory Factory to create settings field objects.
	 */
	public function factory() {
		return $this->factory;
	}
}
