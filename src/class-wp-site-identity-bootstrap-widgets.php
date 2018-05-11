<?php
/**
 * WP_Site_Identity_Bootstrap_Widgets class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class to register the plugin's widgets.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap_Widgets {

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
	 * Action to register the plugin's widgets.
	 *
	 * @since 1.0.0
	 */
	public function action_widgets_init() {
		$registry = $this->plugin->services()->get( 'widget_registry' );
		$factory  = $registry->factory();

		$factory->create_widget( 'data', __( 'Site Identity Data', 'wp-site-identity' ), __( 'Your site identity owner data.', 'wp-site-identity' ), array( $this, 'render_site_identity_data_widget' ), array(
			'title'        => array(
				'label' => __( 'Title', 'wp-site-identity' ),
				'type'  => 'text',
			),
			'show_name'    => array(
				'label'   => __( 'Show name?', 'wp-site-identity' ),
				'type'    => 'checkbox',
				'default' => true,
			),
			'show_address' => array(
				'label'   => __( 'Show address?', 'wp-site-identity' ),
				'type'    => 'checkbox',
				'default' => true,
			),
			'show_contact' => array(
				'label'   => __( 'Show contact information?', 'wp-site-identity' ),
				'type'    => 'checkbox',
				'default' => true,
			),
		) )->register();

		/**
		 * Fires when additional widgets for the plugin can be registered.
		 *
		 * @since 1.0.0
		 *
		 * @param WP_Site_Identity_Widget_Registry $registry Widget registry instance.
		 */
		do_action( 'wp_site_identity_register_widgets', $registry );
	}

	/**
	 * Render callback for the site identity data widget.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance Associative array of widget instance data.
	 * @return string Generated HTML output.
	 */
	public function render_site_identity_data_widget( $instance ) {
		$owner_data = $this->plugin->owner_data();

		$output = '';

		if ( $instance['show_name'] ) {
			if ( 'organization' === $owner_data->get( 'type' ) ) {
				$legal_name = $owner_data->get( 'organization_legal_name' );
				if ( ! empty( $legal_name ) ) {
					$name = $owner_data->get_as_html( 'organization_legal_name' );
				} else {
					$name = $owner_data->get_as_html( 'organization_name' );
				}
				$output .= '<p><strong>' . $name . '</strong></p>';
			} else {
				$output .= '<p><strong>' . $owner_data->get_as_html( 'first_name' ) . ' ' . $owner_data->get_as_html( 'last_name' ) . '</strong></p>';
			}
		}

		if ( $instance['show_address'] ) {
			$output .= '<p>' . $owner_data->get_as_html( 'address_multi' ) . '</p>';
		}

		if ( $instance['show_contact'] ) {
			$contact_data = array();

			$phone = $owner_data->get( 'phone' );
			if ( ! empty( $phone ) ) {
				$contact_data[] = esc_html__( 'Phone:', 'wp-site-identity' ) . ' ' . $owner_data->get_as_html( 'phone_link' );
			}

			$email = $owner_data->get( 'email' );
			if ( ! empty( $email ) ) {
				$contact_data[] = esc_html__( 'Email:', 'wp-site-identity' ) . ' ' . $owner_data->get_as_html( 'email_link' );
			}

			$website = $owner_data->get( 'website' );
			if ( ! empty( $website ) ) {
				$contact_data[] = esc_html__( 'Website:', 'wp-site-identity' ) . ' ' . $owner_data->get_as_html( 'website_link' );
			}

			if ( ! empty( $contact_data ) ) {
				$output .= '<p>' . implode( '<br>', $contact_data ) . '</p>';
			}
		}

		return $output;
	}
}
