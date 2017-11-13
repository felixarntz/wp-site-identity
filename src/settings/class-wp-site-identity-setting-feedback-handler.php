<?php
/**
 * WP_Site_Identity_Setting_Feedback_Handler class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class responsible for handling setting feedback messages.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Setting_Feedback_Handler {

	/**
	 * Prefix to use for all setting names in WordPress.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Adds an success feedback message for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to add the feedback message for.
	 * @param string                   $message Success feedback message.
	 */
	public function add_success( WP_Site_Identity_Setting $setting, $message ) {
		$name = $this->prefix . $setting->get_name();

		add_settings_error( $name, "valid_{$name}", $message, 'updated' );
	}

	/**
	 * Adds an error feedback message for a setting.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting $setting Setting to add the feedback message for.
	 * @param string                   $message Error feedback message.
	 */
	public function add_error( WP_Site_Identity_Setting $setting, $message ) {
		$name = $this->prefix . $setting->get_name();

		add_settings_error( $name, "invalid_{$name}", $message, 'error' );
	}

	/**
	 * Sets the prefix to use for all setting names in WordPress.
	 *
	 * @since 1.0.0
	 * @internal
	 *
	 * @param string $prefix Prefix to use.
	 */
	public function set_prefix( $prefix ) {
		$this->prefix = $prefix;
	}
}
