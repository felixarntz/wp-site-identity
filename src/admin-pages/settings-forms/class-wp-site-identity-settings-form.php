<?php
/**
 * WP_Site_Identity_Settings_Form_Registry class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for registering settings forms.
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

		// TODO: Create fields from all settings.
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
