<?php
/**
 * WP_Site_Identity_Bootstrap_Admin_Pages class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class to register the plugin's admin pages.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap_Admin_Pages {

	/**
	 * Plugin bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap
	 */
	private $bootstrap;

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity
	 */
	private $plugin;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Bootstrap $bootstrap Plugin bootstrap instance.
	 * @param WP_Site_Identity           $plugin    Plugin instance.
	 */
	public function __construct( WP_Site_Identity_Bootstrap $bootstrap, WP_Site_Identity $plugin ) {
		$this->bootstrap = $bootstrap;
		$this->plugin    = $plugin;
	}

	/**
	 * Action to register the plugin's settings page in the admin.
	 *
	 * @since 1.0.0
	 */
	public function action_admin_menu() {
		$registry = $this->plugin->services()->get( 'admin_page_registry' );
		$factory  = $registry->factory();

		$factory->create_admin_submenu_page( 'settings', array(
			'title'            => __( 'Site Identity Settings', 'wp-site-identity' ),
			'capability'       => 'manage_options',
			'render_callback'  => array( $this, 'action_render_settings_page' ),
			'handle_callback'  => array( $this, 'action_handle_settings_page' ),
			'enqueue_callback' => array( $this, 'action_enqueue_settings_page' ),
			'menu_title'       => __( 'Site Identity', 'wp-site-identity' ),
			'parent_slug'      => 'options-general.php',
		) )->register();

		/**
		 * Fires when additional admin pages for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Admin_Page_Registry $registry Admin page registry instance.
		 */
		do_action( 'wp_site_identity_register_admin_pages', $registry );
	}

	/**
	 * Action to handle a request to the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	public function action_handle_settings_page() {
		$setting_registry = $this->plugin->services()->get( 'setting_registry' );

		$factory = $this->plugin->services()->get( 'settings_form_registry' )->factory();

		$owner_data_form = $factory->create_form( $setting_registry->get_setting( 'owner_data' ) );
		$owner_data_form->set_defaults();

		$owner_data_sections = $this->bootstrap->get_owner_data_sections();

		$section_factory = $owner_data_form->get_section_registry()->factory();
		$field_registry  = $owner_data_form->get_field_registry();

		foreach ( $owner_data_sections as $owner_data_section ) {
			$section_factory->create_section( $owner_data_section['slug'], array(
				'title' => $owner_data_section['title'],
			) )->register();

			foreach ( $owner_data_section['fields'] as $owner_data_field_slug ) {
				$field_registry->get_field( $owner_data_field_slug )->set_section_slug( $owner_data_section['slug'] );
			}
		}

		$field_registry->get_field( 'address_zip' )->set_css_classes( array() );
		$field_registry->get_field( 'address_format_multi' )->set_extra_attrs( array(
			'rows' => 4,
		) );

		foreach ( array( 'address_state_abbrev', 'address_country_abbrev' ) as $owner_data_field_slug ) {
			$field_registry->get_field( $owner_data_field_slug )->set_css_classes( array( 'small-text' ) );
		}

		foreach ( array( 'address_format_single', 'address_format_multi' ) as $owner_data_field_slug ) {
			$field_registry->get_field( $owner_data_field_slug )->set_css_classes( array( 'large-text', 'code' ) );
		}

		$owner_data_form->register();

		$brand_data_form = $factory->create_form( $setting_registry->get_setting( 'brand_data' ) );
		$brand_data_form->set_defaults();

		$brand_data_sections = $this->bootstrap->get_brand_data_sections();

		$section_factory = $brand_data_form->get_section_registry()->factory();
		$field_registry  = $brand_data_form->get_field_registry();
		$field_factory   = $field_registry->factory();

		foreach ( $brand_data_sections as $brand_data_section ) {
			$section_factory->create_section( $brand_data_section['slug'], array(
				'title' => $brand_data_section['title'],
			) )->register();

			foreach ( $brand_data_section['fields'] as $brand_data_field_slug ) {
				$field_registry->get_field( $brand_data_field_slug )->set_section_slug( $brand_data_section['slug'] );
			}
		}

		foreach ( array( 'logo', 'icon' ) as $brand_data_field_slug ) {
			$field_registry->get_field( $brand_data_field_slug )->set_render_callback( array( $field_factory->callbacks(), 'render_image_control' ) );
		}

		foreach ( array( 'primary_color', 'secondary_color', 'tertiary_color' ) as $brand_data_field_slug ) {
			$field_registry->get_field( $brand_data_field_slug )->set_render_callback( array( $field_factory->callbacks(), 'render_color_control' ) );
		}

		$brand_data_form->register();
	}

	/**
	 * Action to enqueue assets for the plugin's settings page.
	 *
	 * @since 1.0.0
	 */
	public function action_enqueue_settings_page() {
		if ( 'owner_data' !== $this->get_current_tab() ) {
			return;
		}

		$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_enqueue_script( 'wpsi-settings-page', $this->plugin->url( "assets/dist/js/settings-page{$min}.js" ), array(), $this->plugin->version(), true );
		wp_localize_script( 'wpsi-settings-page', 'wpsiSettingsPage', array(
			'typeDependencies' => $this->bootstrap->get_type_dependencies(),
		) );
	}

	/**
	 * Action to render the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Admin_Page $admin_page Admin page object.
	 */
	public function action_render_settings_page( $admin_page ) {
		if ( ! is_a( $admin_page, 'WP_Site_Identity_Admin_Submenu_Page' ) || 'options-general.php' !== $admin_page->get_parent_slug() ) {
			require ABSPATH . 'wp-admin/options-head.php';
		}

		$settings_forms = $this->plugin->services()->get( 'settings_form_registry' )->get_all_forms();

		$current_slug = $this->get_current_tab( $settings_forms );

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline"><?php echo esc_html( $admin_page->get_title() ); ?></h1>

			<?php
			if ( 'owner_data' === $current_slug && current_user_can( 'customize' ) ) {
				printf(
					' <a class="page-title-action hide-if-no-customize" href="%1$s">%2$s</a>',
					esc_url( add_query_arg(
						array(
							array(
								'autofocus' => array(
									'section' => $settings_forms[ $current_slug ]->get_setting_registry()->prefix( $settings_forms[ $current_slug ]->get_slug() ),
								),
							),
							// @codingStandardsIgnoreStart
							'return' => urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) )
							// @codingStandardsIgnoreEnd
						),
						admin_url( 'customize.php' )
					) ),
					esc_html__( 'Manage with Live Preview', 'wp-site-identity' )
				);
			}
			?>

			<hr class="wp-header-end">

			<?php if ( ! empty( $settings_forms ) ) : ?>
				<?php if ( count( $settings_forms ) > 1 ) : ?>
					<h2 class="nav-tab-wrapper" style="margin-bottom:1em;">
						<?php foreach ( $settings_forms as $slug => $settings_form ) : ?>
							<a class="<?php echo esc_attr( $slug === $current_slug ? 'nav-tab nav-tab-active' : 'nav-tab' ); ?>" href="<?php echo esc_url( add_query_arg( 'tab', $slug, $admin_page->get_url() ) ); ?>">
								<?php echo esc_html( $settings_form->get_setting_registry()->get_title() ); ?>
							</a>
						<?php endforeach; ?>
					</h2>
				<?php else : ?>
					<h2 class="screen-reader-text">
						<?php echo esc_html( $settings_forms[ $current_slug ]->get_setting_registry()->get_title() ); ?>
					</h2>
				<?php endif; ?>

				<?php $settings_forms[ $current_slug ]->render(); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Gets the current tab for the plugin's settings page.
	 *
	 * @since 1.0.0
	 *
	 * @param array|null $settings_forms Optional. Settings forms array. Default are all settings forms registered.
	 * @return string|null Current tab slug, or null if no settings forms are registered.
	 */
	private function get_current_tab( array $settings_forms = null ) {
		if ( null === $settings_forms ) {
			$settings_forms = $this->plugin->services()->get( 'settings_form_registry' )->get_all_forms();
		}

		$current_tab = null;

		if ( ! empty( $settings_forms ) ) {
			// @codingStandardsIgnoreStart
			if ( isset( $_GET['tab'] ) && isset( $settings_forms[ $_GET['tab'] ] ) ) {
				$current_tab = $_GET['tab'];
			} else {
				$current_tab = key( $settings_forms );
			}
			// @codingStandardsIgnoreEnd
		}

		return $current_tab;
	}
}
