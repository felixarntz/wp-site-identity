<?php
/**
 * Plugin initialization file
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name: WP Site Identity
 * Plugin URI:  https://github.com/felixarntz/wp-site-identity
 * Description: Enables you to manage your site identity data and appearance in one centralized location.
 * Version:     1.0.0
 * Author:      Felix Arntz
 * Author URI:  https://leaves-and-love.net
 * License:     GNU General Public License v3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: wp-site-identity
 * Tags:        site identity, personal data, business data, appearance, centralized, customization, customizer, widgets, shortcodes
 */

/**
 * Loads the plugin files.
 *
 * @since 1.0.0
 */
function wpsi_load() {
	$classes_dir = plugin_dir_path( __FILE__ ) . 'src/';

	// Main.
	require_once $classes_dir . 'class-wp-site-identity.php';
	require_once $classes_dir . 'class-wp-site-identity-service-container.php';
	require_once $classes_dir . 'class-wp-site-identity-service-reference.php';

	// Exceptions.
	require_once $classes_dir . 'exceptions/class-wp-site-identity-admin-page-not-found-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-service-already-registered-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-service-not-found-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-setting-not-found-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-setting-validation-error-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-settings-form-not-found-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-settings-section-not-found-exception.php';
	require_once $classes_dir . 'exceptions/class-wp-site-identity-settings-field-not-found-exception.php';

	// Settings.
	require_once $classes_dir . 'settings/interface-wp-site-identity-setting-registry.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-standard-setting-registry.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-setting.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-aggregate-setting.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-setting-factory.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-setting-feedback-handler.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-setting-validator.php';
	require_once $classes_dir . 'settings/class-wp-site-identity-setting-sanitizer.php';

	// Admin pages.
	require_once $classes_dir . 'admin-pages/interface-wp-site-identity-admin-page-registry.php';
	require_once $classes_dir . 'admin-pages/class-wp-site-identity-standard-admin-page-registry.php';
	require_once $classes_dir . 'admin-pages/class-wp-site-identity-admin-page.php';
	require_once $classes_dir . 'admin-pages/class-wp-site-identity-admin-menu-page.php';
	require_once $classes_dir . 'admin-pages/class-wp-site-identity-admin-submenu-page.php';
	require_once $classes_dir . 'admin-pages/class-wp-site-identity-admin-page-factory.php';

	// Settings forms.
	require_once $classes_dir . 'admin-pages/settings-forms/interface-wp-site-identity-settings-form-registry.php';
	require_once $classes_dir . 'admin-pages/settings-forms/class-wp-site-identity-standard-settings-form-registry.php';
	require_once $classes_dir . 'admin-pages/settings-forms/class-wp-site-identity-settings-form.php';
	require_once $classes_dir . 'admin-pages/settings-forms/class-wp-site-identity-settings-form-factory.php';

	// Settings sections.
	require_once $classes_dir . 'admin-pages/settings-sections/interface-wp-site-identity-settings-section-registry.php';
	require_once $classes_dir . 'admin-pages/settings-sections/class-wp-site-identity-standard-settings-section-registry.php';
	require_once $classes_dir . 'admin-pages/settings-sections/class-wp-site-identity-settings-section.php';
	require_once $classes_dir . 'admin-pages/settings-sections/class-wp-site-identity-settings-section-factory.php';

	// Settings fields.
	require_once $classes_dir . 'admin-pages/settings-fields/interface-wp-site-identity-settings-field-registry.php';
	require_once $classes_dir . 'admin-pages/settings-fields/class-wp-site-identity-standard-settings-field-registry.php';
	require_once $classes_dir . 'admin-pages/settings-fields/class-wp-site-identity-settings-field.php';
	require_once $classes_dir . 'admin-pages/settings-fields/class-wp-site-identity-settings-field-factory.php';
	require_once $classes_dir . 'admin-pages/settings-fields/class-wp-site-identity-settings-field-control-callbacks.php';
}

/**
 * Gets the plugin main class instance.
 *
 * If it does not exist yet, it will be instantiated and its hooks will be added.
 *
 * @since 1.0.0
 *
 * @return WP_Site_Identity Plugin main class instance.
 */
function wpsi() {
	static $wp_site_identity = null;

	if ( null === $wp_site_identity ) {
		$wp_site_identity = new WP_Site_Identity( __FILE__, '1.0.0' );
		$wp_site_identity->add_hooks();
	}

	return $wp_site_identity;
}

/**
 * Shows an admin notice if the WordPress version installed is not supported.
 *
 * @since 1.0.0
 */
function wpsi_requirements_notice() {
	$plugin_file = plugin_basename( __FILE__ );

	// WordPress before 4.9 didn't have a dedicated capability for this.
	if ( version_compare( $GLOBALS['wp_version'], '4.9', '<' ) ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'deactivate_plugin', $plugin_file ) ) {
			return;
		}
	}

	$deactivate_url = wp_nonce_url( add_query_arg( array(
		'action'        => 'deactivate',
		'plugin'        => $plugin_file,
		'plugin_status' => 'all',
	), self_admin_url( 'plugins.php' ) ), 'deactivate-plugin_' . $plugin_file );

	?>
	<div class="notice notice-warning is-dismissible">
		<p>
			<?php
			/* translators: %s: URL to deactivate plugin */
			echo wp_kses( sprintf( __( 'Please note: WP Site Identity requires WordPress 4.7 or higher. <a href="%s">Deactivate plugin</a>.', 'wp-site-identity' ), esc_url( $deactivate_url ) ), array(
				'a' => array(
					'href' => array(),
				),
			) );
			?>
		</p>
	</div>
	<?php
}

if ( version_compare( $GLOBALS['wp_version'], '4.7', '<' ) ) {
	add_action( 'admin_notices', 'wpsi_requirements_notice' );
} else {
	wpsi_load();
	add_action( 'plugins_loaded', 'wpsi' );
}
