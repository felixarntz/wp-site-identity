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
	 * Plugin widgets bootstrap instance.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Bootstrap_Widgets
	 */
	private $bootstrap_widgets;

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
		$this->bootstrap_widgets     = new WP_Site_Identity_Bootstrap_Widgets( $this, $this->plugin );
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
		add_action( 'widgets_init', array( $this->bootstrap_widgets, 'action_widgets_init' ), 10, 0 );
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
		remove_action( 'widgets_init', array( $this->bootstrap_widgets, 'action_widgets_init' ), 10 );
		remove_action( 'admin_menu', array( $this->bootstrap_admin_pages, 'action_admin_menu' ), 1 );
		remove_action( 'customize_register', array( $this->bootstrap_customizer, 'action_customize_register' ), 10 );
		remove_action( 'customize_controls_enqueue_scripts', array( $this->bootstrap_customizer, 'action_customize_controls_enqueue_scripts' ), 10 );
		remove_action( 'customize_preview_init', array( $this->bootstrap_customizer, 'action_customize_preview_init' ), 10 );
	}

	/**
	 * Gets the array of sections for the owner data settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Sections as `$slug => $data` pairs, where $data is an associative array containing
	 *               $slug, $title and $fields keys.
	 */
	public function get_owner_data_sections() {
		$sections = array(
			'basic'   => array(
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
			'address' => array(
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
			'contact' => array(
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

		/**
		 * Filters the sections for the owner data settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $sections Sections as `$slug => $data` pairs, where $data is an associative array containing
		 *                        $slug, $title and $fields keys.
		 */
		return apply_filters( 'wp_site_identity_owner_data_sections', $sections );
	}

	/**
	 * Gets the array of sections for the brand data settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array Sections as `$slug => $data` pairs, where $data is an associative array containing
	 *               $slug, $title and $fields keys.
	 */
	public function get_brand_data_sections() {
		$sections = array(
			'media'  => array(
				'slug'   => 'media',
				'title'  => __( 'Media', 'wp-site-identity' ),
				'fields' => array(
					'logo',
					'icon',
				),
			),
			'colors' => array(
				'slug'   => 'colors',
				'title'  => __( 'Colors', 'wp-site-identity' ),
				'fields' => array(
					'primary_color',
					'secondary_color',
					'tertiary_color',
				),
			),
		);

		/**
		 * Filters the sections for the brand data settings.
		 *
		 * @since 1.0.0
		 *
		 * @param array $sections Sections as `$slug => $data` pairs, where $data is an associative array containing
		 *                        $slug, $title and $fields keys.
		 */
		return apply_filters( 'wp_site_identity_brand_data_sections', $sections );
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
