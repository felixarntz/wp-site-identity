<?php
/**
 * WP_Site_Identity_Bootstrap_Shortcodes class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class to register the plugin's shortcodes.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap_Shortcodes {

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
	 * Action to register the plugin's shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function action_init() {
		$registry = $this->plugin->services()->get( 'shortcode_registry' );
		$factory  = $registry->factory();

		foreach ( $this->plugin->services()->get( 'setting_registry' )->get_all_settings() as $aggregate_setting ) {
			if ( ! is_a( $aggregate_setting, 'WP_Site_Identity_Setting_Registry' ) ) {
				continue;
			}

			$icon = 'appearance' === $aggregate_setting->get_name() ? 'dashicons-admin-appearance' : 'dashicons-admin-users';

			foreach ( $aggregate_setting->get_all_settings() as $setting ) {
				$setting_name  = $setting->get_name();
				$setting_title = $setting->get_title();

				switch ( $setting_name ) {
					case 'address_format_single':
						$setting_name  = 'address_single';
						$setting_title = __( 'Address (Single Line)', 'wp-site-identity' );
						break;
					case 'address_format_multi':
						$setting_name  = 'address_multi';
						$setting_title = __( 'Address (Multiple Lines)', 'wp-site-identity' );
						break;
				}

				$callback_name = 'shortcode_callback_' . $aggregate_setting->get_name() . '_setting_' . $setting_name;

				$factory->create_shortcode( $setting_name, array( $this, $callback_name ), array(
					'label'         => __( 'Site Identity:', 'wp-site-identity' ) . ' ' . $setting_title,
					'listItemImage' => $icon,
				) )->register();
			}
		}

		/**
		 * Fires when additional shortcodes for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Shortcode_Registry $registry Shortcode registry instance.
		 */
		do_action( 'wp_site_identity_register_shortcodes', $registry );
	}

	/**
	 * Magic call method.
	 *
	 * Acts as a dynamic shortcode render callback.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method Method name.
	 * @param array  $args   Method arguments.
	 * @return mixed Method results.
	 */
	public function __call( $method, $args ) {
		$is_shortcode_callback = preg_match( '/^shortcode_callback_([a-z_]+)_setting_([a-z_]+)$/', $method, $matches );

		if ( $is_shortcode_callback ) {
			$aggregate_setting_name = $matches[1];
			$setting_name           = $matches[2];

			$data = call_user_func( array( $this->plugin, $aggregate_setting_name ) );

			return $data->get_as_html( $setting_name );
		}
	}
}
