<?php
/**
 * WP_Site_Identity_Settings_Form class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a settings form.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Form {

	/**
	 * Slug of the settings form.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Setting registry the settings form should display fields for.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Setting_Registry
	 */
	protected $setting_registry;

	/**
	 * Parent registry for the settings form.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Form_Registry
	 */
	protected $registry;

	/**
	 * All added settings sections as `$slug => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $sections = array();

	/**
	 * All added settings fields as `$slug => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Constructor.
	 *
	 * Sets the settings form properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                                  $slug             Settings form slug.
	 * @param WP_Site_Identity_Setting_Registry       $setting_registry Setting registry the settings form should display fields for.
	 * @param WP_Site_Identity_Settings_Form_Registry $registry         Optional. Parent registry for the settings form.
	 */
	public function __construct( $slug, WP_Site_Identity_Setting_Registry $setting_registry, WP_Site_Identity_Settings_Form_Registry $registry = null ) {
		$this->slug             = $slug;
		$this->setting_registry = $setting_registry;

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Settings_Form_Registry();
		}
	}

	/**
	 * Checks whether the settings form is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the settings form is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_form( $this->name );
	}

	/**
	 * Registers the settings form.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_form( $this );
	}

	/**
	 * Unregisters the settings form.
	 *
	 * @since 1.0.0
	 */
	public function unregister() {
		$this->registry->unregister_form( $this );
	}

	/**
	 * Gets the slug of the settings form.
	 *
	 * @since 1.0.0
	 *
	 * @return string Admin page slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets the setting registry the settings form should display fields for.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting_Registry The form's setting registry.
	 */
	public function get_setting_registry() {
		return $this->setting_registry;
	}

	/**
	 * Sets default fields for all settings in the setting registry.
	 *
	 * @since 1.0.0
	 */
	public function set_defaults() {
		foreach ( $this->setting_registry->get_all_settings() as $setting ) {
			$this->register_field( new WP_Site_Identity_Settings_Field( $setting->get_name(), $setting ) );
		}
	}

	/**
	 * Gets a registered settings section instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings section.
	 * @return WP_Site_Identity_Settings_Section Registered settings section.
	 *
	 * @throws WP_Site_Identity_Settings_Section_Not_Found_Exception Thrown when a settings section cannot be found.
	 */
	public function get_section( $slug ) {
		if ( ! isset( $this->sections[ $slug ] ) ) {
			/* translators: %s: settings section name */
			throw new WP_Site_Identity_Settings_Section_Not_Found_Exception( sprintf( __( 'The settings section with the name %s could not be found.', 'wp-site-identity' ), $slug ) );
		}

		return $this->sections[ $slug ];
	}

	/**
	 * Checks whether a settings section is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings section.
	 * @return bool True if the settings section is registered, false otherwise.
	 */
	public function has_section( $slug ) {
		return isset( $this->sections[ $slug ] );
	}

	/**
	 * Registers a new settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Section $section Settings section to register.
	 */
	public function register_section( WP_Site_Identity_Settings_Section $section ) {
		$slug = $section->get_slug();

		$this->sections[ $slug ] = $section;

		add_settings_section( $section->get_slug(), $section->get_title(), array( $section, 'render' ), $this->setting_registry->group() );
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
			/* translators: %s: settings field name */
			throw new WP_Site_Identity_Settings_Field_Not_Found_Exception( sprintf( __( 'The settings field with the name %s could not be found.', 'wp-site-identity' ), $slug ) );
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

		add_settings_field( $field->get_slug(), $field->get_title(), array( $field, 'render' ), $this->setting_registry->group(), $field->get_section_slug(), $args );
	}

	/**
	 * Renders the form.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		$group = $this->setting_registry->group();

		?>
		<form method="post" action="options.php" novalidate="novalidate">
			<?php settings_fields( $group ); ?>

			<?php do_settings_sections( $group ); ?>

			<table class="form-table">
				<?php do_settings_fields( $group, 'default' ); ?>
			</table>

			<?php submit_button(); ?>
		</form>
		<?php
	}
}
