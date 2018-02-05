<?php
/**
 * WP_Site_Identity_Brand_Data class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class for an easy-to-use access point for plugin data.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Brand_Data extends WP_Site_Identity_Data {

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
	 * @param string                            $prefix           Prefix to use for all CSS classes.
	 * @param WP_Site_Identity_Setting_Registry $setting_registry Setting registry to retrieve the data from.
	 * @param WP_Site_Identity                  $plugin           Plugin instance.
	 */
	public function __construct( $prefix, WP_Site_Identity_Setting_Registry $setting_registry, WP_Site_Identity $plugin ) {
		parent::__construct( $prefix, $setting_registry );

		$this->plugin = $plugin;
	}

	/**
	 * Retrieves the value for a specific identifier.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting identifier.
	 * @return mixed Current value.
	 */
	public function get( $name ) {
		switch ( $name ) {
			case 'primary_shade_color':
			case 'secondary_shade_color':
			case 'tertiary_shade_color':
				return $this->darken_color( parent::get( str_replace( '_shade', '', $name ) ), 8 );
			case 'primary_contrast_color':
			case 'secondary_contrast_color':
			case 'tertiary_contrast_color':
				$black = $this->plugin->get_theme_support( 'css_color_black' );
				if ( ! $black ) {
					$black = '#000000';
				}
				$white = $this->plugin->get_theme_support( 'css_color_white' );
				if ( ! $white ) {
					$white = '#ffffff';
				}
				return $this->is_dark( parent::get( str_replace( '_contrast', '', $name ) ) ) ? $white : $black;
			case 'colors':
				return array(
					'primary'            => $this->get( 'primary_color' ),
					'secondary'          => $this->get( 'secondary_color' ),
					'tertiary'           => $this->get( 'tertiary_color' ),
					'primary_shade'      => $this->get( 'primary_shade_color' ),
					'secondary_shade'    => $this->get( 'secondary_shade_color' ),
					'tertiary_shade'     => $this->get( 'tertiary_shade_color' ),
					'primary_contrast'   => $this->get( 'primary_contrast_color' ),
					'secondary_contrast' => $this->get( 'secondary_contrast_color' ),
					'tertiary_contrast'  => $this->get( 'tertiary_contrast_color' ),
				);
		}

		return parent::get( $name );
	}

	/**
	 * Darkens a hex color string about a given percentage.
	 *
	 * @since 1.0.0
	 *
	 * @param string $color      Hex color string.
	 * @param int    $percentage Percentage to darken about.
	 * @return string Darkened hex color string.
	 */
	protected function darken_color( $color, $percentage ) {
		if ( empty( $color ) ) {
			return $color;
		}

		$rgb = $this->hex_to_rgb( $color );

		$darkened = array();
		foreach ( $rgb as $channel ) {
			$darkened[] = (int) round( $channel * ( 1.0 - $percentage / 100.0 ) );
		}

		return $this->rgb_to_hex( $darkened );
	}

	/**
	 * Checks whether a hex color string is rather a dark color than a light color.
	 *
	 * @since 1.0.0
	 *
	 * @param string $color Hex color string.
	 * @return bool True if the color is rather dark, false otherwise.
	 */
	protected function is_dark( $color ) {
		if ( empty( $color ) ) {
			return false;
		}

		$rgb = $this->hex_to_rgb( $color );

		if ( array_sum( $rgb ) < 385 ) { // 255 * 3 / 2
			return true;
		}

		return false;
	}

	/**
	 * Converts a hex color string into an RGB array.
	 *
	 * @since 1.0.0
	 *
	 * @param string $color Hex color string.
	 * @return array RGB color array.
	 */
	protected function hex_to_rgb( $color ) {
		if ( strlen( $color ) === 4 ) {
			$rgb = str_split( substr( $color, 1 ), 1 );
			$rgb = array_map( array( $this, 'duplicate_char' ), $rgb );
		} else {
			$rgb = str_split( substr( $color, 1 ), 2 );
		}

		return array_map( 'hexdec', $rgb );
	}

	/**
	 * Converts an RGB array into a hex color string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $color RGB color array.
	 * @return string Hex color string.
	 */
	protected function rgb_to_hex( $color ) {
		$hex = array_map( 'zeroise', array_map( 'dechex', $color ), array( 2, 2, 2 ) );

		return '#' . $hex[0] . $hex[1] . $hex[2];
	}

	/**
	 * Duplicates a character so that it is twice that character.
	 *
	 * @since 1.0.0
	 *
	 * @param string $char Character to duplicate.
	 * @return string The value of $char twice.
	 */
	protected function duplicate_char( $char ) {
		return $char . $char;
	}
}
