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
	 * @param WP_Site_Identity $plugin Plugin instance.
	 */
	public function __construct( WP_Site_Identity $plugin ) {
		$this->plugin = $plugin;
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
			'enqueue_callback' => null,
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

		$owner_data_sections = array(
			array(
				'slug'   => 'basic',
				'title'  => __( 'Basic Information', 'wp-site-identity' ),
				'fields' => array(
					'type',
					'first_name',
					'last_name',
					'organization_name',
					'organization_legal_name',
				),
			),
			array(
				'slug'   => 'address',
				'title'  => __( 'Address', 'wp-site-identity' ),
				'fields' => array(
					'address_line_1',
					'address_line_2',
					'address_city',
					'address_zip',
					'address_state',
					'address_state_abbrev',
					'address_country',
					'address_country_abbrev',
					'address_format_single',
					'address_format_multi',
				),
			),
			array(
				'slug'   => 'contact',
				'title'  => __( 'Contact Data', 'wp-site-identity' ),
				'fields' => array(
					'email',
					'website',
					'phone',
					'phone_human',
				),
			),
		);

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

		$appearance_form = $factory->create_form( $setting_registry->get_setting( 'appearance' ) );
		$appearance_form->set_defaults();

		// TODO: Add appearance settings sections and fields.
		$appearance_form->register();
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

		$current_slug = null;

		if ( ! empty( $settings_forms ) ) {
			// @codingStandardsIgnoreStart
			if ( isset( $_GET['tab'] ) && isset( $settings_forms[ $_GET['tab'] ] ) ) {
				$current_slug = $_GET['tab'];
			} else {
				$current_slug = key( $settings_forms );
			}
			// @codingStandardsIgnoreEnd
		}

		?>
		<div class="wrap">
			<h1><?php echo esc_html( $admin_page->get_title() ); ?></h1>

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
}
