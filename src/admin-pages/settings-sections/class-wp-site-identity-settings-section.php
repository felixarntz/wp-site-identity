<?php
/**
 * WP_Site_Identity_Settings_Section class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class representing a settings section.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Settings_Section {

	/**
	 * Slug of the settings section.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Title of the settings section.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $title = '';

	/**
	 * Description of the settings section.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	protected $description = '';

	/**
	 * Render callback for the settings section.
	 *
	 * @since 1.0.0
	 * @var callable|null
	 */
	protected $render_callback = null;

	/**
	 * Parent registry for the settings section.
	 *
	 * @since 1.0.0
	 * @var WP_Site_Identity_Settings_Section_Registry
	 */
	protected $registry;

	/**
	 * Constructor.
	 *
	 * Sets the settings section properties.
	 *
	 * @since 1.0.0
	 *
	 * @param string                                     $slug     Settings section slug.
	 * @param array                                      $args     {
	 *     Optional. Arguments for the settings section.
	 *
	 *     @type string   $title            Title for the settings section. Default empty string.
	 *     @type string   $description      Description for the settings section. Default empty string.
	 *     @type callable $render_callback  Render callback for the settings section. Should print the content it
	 *                                      generates. It is passed the settings section instance. Default null.
	 * }
	 * @param WP_Site_Identity_Settings_Section_Registry $registry Optional. Parent registry for the settings section.
	 */
	public function __construct( $slug, array $args = array(), WP_Site_Identity_Settings_Section_Registry $registry = null ) {
		$this->slug = $slug;

		if ( ! empty( $args['title'] ) ) {
			$this->title = $args['title'];
		}

		if ( ! empty( $args['description'] ) ) {
			$this->description = $args['description'];
		}

		if ( isset( $args['render_callback'] ) ) {
			$this->render_callback = $args['render_callback'];
		}

		if ( $registry ) {
			$this->registry = $registry;
		} else {
			$this->registry = new WP_Site_Identity_Standard_Settings_Section_Registry();
		}
	}

	/**
	 * Checks whether the settings section is registered.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the settings section is registered, false otherwise.
	 */
	public function is_registered() {
		return $this->registry->has_section( $this->slug );
	}

	/**
	 * Registers the settings section.
	 *
	 * @since 1.0.0
	 */
	public function register() {
		$this->registry->register_section( $this );
	}

	/**
	 * Renders the settings section content, if there is any.
	 *
	 * This will not render the fields of the section, only any
	 * extra content before.
	 *
	 * @since 1.0.0
	 */
	public function render() {
		if ( ! empty( $this->description ) ) {
			echo '<p class="description">' . wp_kses_data( $this->description ) . '</p>';
		}

		if ( ! $this->render_callback ) {
			return;
		}

		call_user_func( $this->render_callback, $this );
	}

	/**
	 * Gets the slug of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings section slug.
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Gets the title of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings section title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Gets the description of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @return string Settings section description.
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Gets the render callback for the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @return callable|null Render callback, or null if none set.
	 */
	public function get_render_callback() {
		return $this->render_callback;
	}

	/**
	 * Sets the description of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param string $description Settings section description.
	 */
	public function set_description( $description ) {
		$this->description = $description;
	}

	/**
	 * Sets the render callback of the settings section.
	 *
	 * @since 1.0.0
	 *
	 * @param callable|null $render_callback Settings section render callback, or null to unset.
	 */
	public function set_render_callback( $render_callback ) {
		$this->render_callback = $render_callback;
	}
}
