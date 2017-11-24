<?php
/**
 * WP_Site_Identity_Settings_Field class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a settings field.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Field {

	/**
	 * Slug of the settings field.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Setting the settings field is for.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settin
	 */
	protected $setting;

	/**
	 * Title of the settings field.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Description of the settings field.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $description = '';

	/**
	 * Render callback for the settings field.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $render_callback = null;

	/**
	 * Slug of the parent settings section.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $section_slug = '';

	/**
	 * ID attribute for the settings field control.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $id_attr = '';

	/**
	 * Name attribute for the settings field control.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $name_attr = '';

	/**
	 * CSS classes for the settings field control.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $css_classes = array();

	/**
	 * Whether to render the 'for' attribute on the label in WordPress.
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	protected $render_for_attr = true;

	/**
	 * Extra attributes for the settings field control as `$attr => $value` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_attrs = array();

	/**
	 * Extra props for the settings field control as `$prop => $enabled` pairs.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $extra_props = array();

	/**
	 * Parent registry for the settings field.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Field_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * Sets the settings field properties.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Site_Identity_Setting                 $setting  Setting the settings field is for.
	 * @param array                                    $args     {
	 *     Optional. Arguments for the settings field.
	 *
	 *     @type string   $title            Title for the settings field. Default empty string.
	 *     @type string   $description      Description for the settings field. Default empty string.
	 *     @type callable $render_callback  Render callback for the settings field. Should print the content it
	 *                                      generates. It is passed the current value and the settings field
	 *                                      instance. Default null.
	 *     @type string   $section_slug     Slug of the parent settings section. Default 'default'.
	 *     @type array    $css_classes      CSS classes list for the settings field control. Default empty array.
	 *     @type bool     $render_for_attr  Whether WordPress should render the 'for' attribute on the label.
	 *                                      Default true.
	 * }
	 * @param WP_Site_Identity_Settings_Field_Registry $registry Optional. Parent registry for the settings field.
	 */
	public function __construct( WP_Site_Identity_Setting $setting, array $args = array(), WP_Site_Identity_Settings_Field_Registry $registry = null ) {
		$this->slug    = $setting->get_name();
		$this->setting = $setting;

		if ( ! empty( $args['title'] ) ) {
			$this->title = $args['title'];
		} else {
			$this->title = $this->setting->get_title();
		}

		if ( ! empty( $args['description'] ) ) {
			$this->set_description( $args['description'] );
		} else {
			$this->set_description( $this->setting->get_description() );
		}

		if ( isset( $args['render_callback'] ) ) {
			$this->set_render_callback( $args['render_callback'] );
		}

		if ( ! empty( $args['section_slug'] ) ) {
			$this->section_slug = $args['section_slug'];
		} else {
			$this->section_slug = 'default';
		}

		if ( ! empty( $args['css_classes'] ) ) {
			$this->set_css_classes( $args['css_classes'] );
		}

		if ( isset( $args['render_for_attr'] ) ) {
			$this->set_render_for_attr( $args['render_for_attr'] );
		}

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Settings_Field_Registry();
		}

		$this->set_default_attrs();
	}

	/**
	 * Checks whether the settings field is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the settings field is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_field( $this->name );
	}

	/**
	 * Registers the settings field.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_field( $this );
	}

	/**
	 * Renders the settings field content, if there is any.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( ! $this->render_callback ) {
			return;
		}

		$value = $this->setting->get_registry()->get_value( $this->setting->get_name() );

		call_user_func( $this->render_callback, $value, $this );
	}

	/**
	 * Gets the slug of the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings field slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets the setting the settings field is for.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Site_Identity_Setting Setting of the settings field.
	 */
	public function get_setting() {
		return $this->setting;
	}

	/**
	 * Gets the title of the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings field title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Gets the description of the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings field description.
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Gets the render callback for the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Render callback, or null if none set.
	 */
	public function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Gets the slug of the settings field's parent section.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings field's parent section slug.
	 */
	public function get_section_slug() {
		return $this->section_slug;
	}

	/**
	 * Gets the ID attribute for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @return string ID attribute.
	 */
	public function get_id_attr() {
		return $this->id_attr;
	}

	/**
	 * Gets the name attribute for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @return string Name attribute.
	 */
	public function get_name_attr() {
		return $this->name_attr;
	}

	/**
	 * Gets the CSS classes for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @return array CSS classes list.
	 */
	public function get_css_classes() {
		return $this->css_classes;
	}

	/**
	 * Gets the extra attributes for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Extra attributes as `$attr => $value` pairs.
	 */
	public function get_extra_attrs() {
		return $this->extra_attrs;
	}

	/**
	 * Gets the extra props for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @return array Extra props as `$prop => $enabled` pairs.
	 */
	public function get_extra_props() {
		return $this->extra_props;
	}

	/**
	 * Checks whether WordPress should render the label 'for' attribute for the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the label 'for' attribute should be rendered, false otherwise.
	 */
	public function has_for_attr() {
		return $this->render_for_attr;
	}

	/**
	 * Sets the description of the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $description Settings field description.
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Sets the render callback of the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @param callable|null $render_callback Settings field render callback, or null to unset.
	 */
	public function set_render_callback( $render_callback ) {
		$this->render_callback = $render_callback;
	}

	/**
	 * Sets the slug of the settings field's parent section.
	 *
	 * @since 1.0.0
	 *
	 * @param string $section_slug Settings field's parent section slug.
	 */
	public function set_section_slug( $section_slug ) {

		// TODO: Update in WordPress if already registered.
		$this->section_slug = $section_slug;
	}

	/**
	 * Sets the CSS classes list for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @param array $css_classes CSS classes list.
	 */
	public function set_css_classes( array $css_classes ) {
		$this->css_classes = $css_classes;
	}

	/**
	 * Sets extra attributes for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @param array $extra_attrs Extra attributes as `$attr => $value` pairs.
	 */
	public function set_extra_attrs( array $extra_attrs ) {
		$this->extra_attrs = $extra_attrs;
	}

	/**
	 * Sets extra props for the settings field control.
	 *
	 * @since 1.0.0
	 *
	 * @param array $extra_props Extra props as `$prop => $enabled` pairs.
	 */
	public function set_extra_props( array $extra_props ) {
		$this->extra_props = $extra_props;
	}

	/**
	 * Sets whether WordPress should render the label 'for' attribute for the settings field.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $render_for_attr Whether to render the label 'for' attribute.
	 */
	public function set_render_for_attr( $render_for_attr ) {

		// TODO: Update in WordPress if already registered.
		$this->render_for_attr = $render_for_attr;
	}

	/**
	 * Sets the ID and name attributes.
	 *
	 * These are automatically determined and cannot be changed.
	 *
	 * @since 1.0.0
	 */
	protected function set_default_attrs() {
		$registry = $this->setting->get_registry();

		if ( is_a( $registry, 'WP_Site_Identity_Aggregate_Setting' ) ) {
			$this->id_attr   = str_replace( '_', '-', $registry->prefix( $registry->get_name() . '-' . $this->slug ) );
			$this->name_attr = $registry->prefix( $registry->get_name() ) . '[' . $this->slug . ']';
		} else {
			$this->id_attr   = str_replace( '_', '-', $registry->prefix( $this->slug ) );
			$this->name_attr = $registry->prefix( $this->slug );
		}
	}
}
