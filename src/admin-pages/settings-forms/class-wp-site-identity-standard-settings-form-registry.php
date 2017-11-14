<?php
/**
 * WP_Site_Identity_Standard_Settings_Form_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering settings forms.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Standard_Settings_Form_Registry implements WP_Site_Identity_Settings_Form_Registry {

	/**
	 * All registered settings forms as `$slug => $instance` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $forms = array();

	/**
	 * Factory to create settings form objects.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Form_Factory
	 */
	protected $factory;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->factory = new WP_Site_Identity_Settings_Form_Factory( $this );
	}

	/**
	 * Gets all registered settings forms.
	 *
	 * @since 1.0.0
	 *
	 * @return array Array of `$slug => $instance` pairs.
	 */
	public function get_all_forms() {
		return $this->forms;
	}

	/**
	 * Gets a registered settings form instance.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings form.
	 * @return WP_Site_Identity_Settings_Form Registered settings form.
	 *
	 * @throws WP_Site_Identity_Settings_Form_Not_Found_Exception Thrown when a settings form cannot be found.
	 */
	public function get_form( $slug ) {
		if ( ! isset( $this->forms[ $slug ] ) ) {
			/* translators: %s: settings form slug */
			throw new WP_Site_Identity_Settings_Form_Not_Found_Exception( sprintf( __( 'The settings form with the slug %s could not be found.', 'wp-site-identity' ), $slug ) );
		}

		return $this->forms[ $slug ];
	}

	/**
	 * Checks whether a settings form is registered.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug Slug of the settings form.
	 * @return bool True if the settings form is registered, false otherwise.
	 */
	public function has_form( $slug ) {
		return isset( $this->forms[ $slug ] );
	}

	/**
	 * Registers a new settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form $form Settings form to register.
	 */
	public function register_form( WP_Site_Identity_Settings_Form $form ) {
		$slug = $form->get_slug();

		$this->forms[ $slug ] = $form;
	}

	/**
	 * Unregisters an existing settings form.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Settings_Form $form Settings form to unregister.
	 */
	public function unregister_form( WP_Site_Identity_Settings_Form $form ) {
		$slug = $form->get_slug();

		if ( ! isset( $this->forms[ $slug ] ) ) {
			return;
		}

		unset( $this->forms[ $slug ] );
	}

	/**
	 * Gets the factory to create settings form objects.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Settings_Form_Factory Factory to create settings form objects.
	 */
	public function factory() {
		return $this->factory;
	}
}
