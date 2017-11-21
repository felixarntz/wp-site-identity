<?php
/**
 * WP_Site_Identity_Standard_Settings_Section_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering settings sections.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Settings_Section_Registry implements WP_Site_Identity_Settings_Section_Registry {

	/**
	 * All registered settings sections as `$slug => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $sections = array();

	/**
	 * Parent settings form to register settings sections for.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Form
	 */
	protected $form;

	/**
	 * Factory to create settings section objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Section_Factory
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
		$this->factory = new WP_Site_Identity_Settings_Section_Factory( $this );
	}

	/**
	 * Gets all registered settings sections.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_sections() {
		return $this->sections;
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
			/* translators: %s: settings section slug */
			throw new WP_Site_Identity_Settings_Section_Not_Found_Exception( sprintf( __( 'The settings section with the slug %s could not be found.', 'wp-site-identity' ), $slug ) );
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

		add_settings_section( $section->get_slug(), $section->get_title(), array( $section, 'render' ), $this->form->get_slug() );
	}

	/**
	 * Gets the factory to create settings section objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Section_Factory Factory to create settings section objects.
	 */
	public function factory() {
		return $this->factory;
	}
}
