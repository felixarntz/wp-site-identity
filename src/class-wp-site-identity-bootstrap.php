<?php
/**
 * WP_Site_Identity_Bootstrap class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Bootstrap class for the plugin's data.
 *
 * @since 1.0.0
 */
final class WP_Site_Identity_Bootstrap {

	/**
	 * Plugin instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity
	 */
	private $plugin;

	/**
	 * Plugin settings bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Settings
	 */
	private $bootstrap_settings;

	/**
	 * Plugin shortcodes bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Shortcodes
	 */
	private $bootstrap_shortcodes;

	/**
	 * Plugin admin pages bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Admin_Pages
	 */
	private $bootstrap_admin_pages;

	/**
	 * Plugin Customizer content bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Customizer
	 */
	private $bootstrap_customizer;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity $plugin Plugin instance.
	 */
	public function __construct( WP_Site_Identity $plugin ) {
		$this->plugin = $plugin;

		$this->bootstrap_settings    = new WP_Site_Identity_Bootstrap_Settings( $this, $this->plugin );
		$this->bootstrap_shortcodes  = new WP_Site_Identity_Bootstrap_Shortcodes( $this, $this->plugin );
		$this->bootstrap_admin_pages = new WP_Site_Identity_Bootstrap_Admin_Pages( $this, $this->plugin );
		$this->bootstrap_customizer  = new WP_Site_Identity_Bootstrap_Customizer( $this, $this->plugin );
	}

	/**
	 * Adds all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function add_hooks() {
		add_action( 'init', array( $this->bootstrap_settings, 'action_init' ), 1, 0 );
		add_action( 'init', array( $this->bootstrap_shortcodes, 'action_init' ), 10, 0 );
		add_action( 'admin_menu', array( $this->bootstrap_admin_pages, 'action_admin_menu' ), 1, 0 );
		add_action( 'customize_register', array( $this->bootstrap_customizer, 'action_customize_register' ), 10, 1 );
		add_action( 'customize_controls_enqueue_scripts', array( $this->bootstrap_customizer, 'action_customize_controls_enqueue_scripts' ), 10, 0 );
		add_action( 'customize_preview_init', array( $this->bootstrap_customizer, 'action_customize_preview_init' ), 10, 0 );
	}

	/**
	 * Removes all hooks for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function remove_hooks() {
		remove_action( 'init', array( $this->bootstrap_settings, 'action_init' ), 1 );
		remove_action( 'init', array( $this->bootstrap_shortcodes, 'action_init' ), 10 );
		remove_action( 'admin_menu', array( $this->bootstrap_admin_pages, 'action_admin_menu' ), 1 );
		remove_action( 'customize_register', array( $this->bootstrap_customizer, 'action_customize_register' ), 10 );
		remove_action( 'customize_controls_enqueue_scripts', array( $this->bootstrap_customizer, 'action_customize_controls_enqueue_scripts' ), 10 );
		remove_action( 'customize_preview_init', array( $this->bootstrap_customizer, 'action_customize_preview_init' ), 10 );
	}

	/**
	 * Gets the array of choices for the 'type' setting.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$value => $label` pairs.
	 */
	public function get_type_choices() {
		$choices = array(
			'individual'   => __( 'Individual', 'wp-site-identity' ),
			'organization' => __( 'Organization', 'wp-site-identity' ),
		);

		/**
		 * Filters the choices for the 'type' setting.
		 *
		 * @since 1.0.0
		 *
		 * @param array $dependencies Associative array of `$value => label` pairs.
		 */
		return apply_filters( 'wp_site_identity_type_choices', $choices );
	}

	/**
	 * Gets the array of fields that depend on the 'type' setting.
	 *
	 * @since 1.0.0
	 *
	 * @return array Associative array of `$type => $fields` pairs where $fields is another associative
	 *               array of `$field_name => $enable` pairs.
	 */
	public function get_type_dependencies() {
		$dependencies = array(
			'individual'   => array(
				'first_name'              => true,
				'last_name'               => true,
				'organization_name'       => false,
				'organization_legal_name' => false,
			),
			'organization' => array(
				'first_name'              => false,
				'last_name'               => false,
				'organization_name'       => true,
				'organization_legal_name' => true,
			),
		);

		/**
		 * Filters the fields that depend on the 'type' setting.
		 *
		 * @since 1.0.0
		 *
		 * @param array $dependencies Associative array of `$type => $fields` pairs where $fields is another associative
		 *                            array of `$field_name => $enable` pairs.
		 */
		return apply_filters( 'wp_site_identity_type_dependencies', $dependencies );
	}
}
