<?php
/**
 * WP_Site_Identity_Owner_Data class
 *
 * @package WPSiteIdentity
 * @since 1.0.0
 */

/**
 * Class for an easy-to-use access point for plugin data.
 *
 * @since 1.0.0
 */
class WP_Site_Identity_Owner_Data extends WP_Site_Identity_Data {

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
			case 'address_single':
				return $this->get_address( 'single' );
			case 'address_multi':
				return $this->get_address( 'multi' );
		}

		return parent::get( $name );
	}

	/**
	 * Retrieves the value for a specific identifier as HTML.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting identifier.
	 * @return string Current value as HTML.
	 */
	public function get_as_html( $name ) {
		switch ( $name ) {
			case 'phone_link':
			case 'email_link':
			case 'website_link':
				$name = str_replace( '_link', '', $name );

				$value = $this->get( $name );
				$class = $this->get_css_class( $name . '_link' );

				$url  = $value;
				$text = $value;

				if ( 'phone' === $name ) {
					$url = 'tel:' . $url;

					$human_readable = $this->get( 'phone_human' );
					if ( ! empty( $human_readable ) ) {
						$text = $human_readable;
					}
				} elseif ( 'email' === $name ) {
					$url = 'mailto:' . $url;
				} else {
					$text = str_replace( array( 'http://', 'https://' ), '', $text );
				}

				return '<a class="' . esc_attr( $class ) . '" href="' . esc_attr( $url ) . '">' . esc_html( $text ) . '</a>';
			case 'address_multi':
			case 'address_format_multi':
				$value = $this->get( $name );
				$class = $this->get_css_class( $name );

				return '<p class="' . esc_attr( $class ) . '">' . str_replace( PHP_EOL, '<br>', $value ) . '</p>';
		}

		return parent::get_as_html( $name );
	}

	/**
	 * Gets the address in a specific format.
	 *
	 * @since 1.0.0
	 *
	 * @param string $mode Optional. Address format. Either 'single' or 'multi'. Default 'single'.
	 * @return string Formatted address. May contain linebreaks.
	 */
	protected function get_address( $mode = 'single' ) {
		$format = $this->get( 'address_format_' . $mode );
		if ( empty( $format ) ) {
			return '';
		}

		$fields = array(
			'line_1',
			'line_2',
			'city',
			'zip',
			'state',
			'state_abbrev',
			'country',
			'country_abbrev',
		);

		$data = array();
		foreach ( $fields as $field ) {
			$data[ $field ] = $this->get( 'address_' . $field );
		}

		return $this->format_address( $format, array_map( array( $this, 'make_placeholder' ), $fields ), array_values( $data ) );
	}

	/**
	 * Formats an address.
	 *
	 * @since 1.0.0
	 *
	 * @param string $format             Address format string. May contain linebreaks.
	 * @param array  $placeholders       All placeholders to replace.
	 * @param array  $placeholder_values All values to replace the placeholders with.
	 * @return string Formatted address. May contain linebreaks.
	 */
	protected function format_address( $format, array $placeholders, array $placeholder_values ) {
		$formatted = str_replace( $placeholders, $placeholder_values, $format );

		$formatted_parts = array();

		$parts = explode( PHP_EOL, $formatted );

		foreach ( $parts as $part ) {
			$part = trim( $part, ' ,;-' );

			if ( empty( $part ) ) {
				continue;
			}

			$formatted_line_parts = array();

			$line_parts = explode( ' ', $part );

			foreach ( $line_parts as $line_part ) {
				$line_part = trim( $line_part, ',;-' );

				if ( empty( $line_part ) ) {
					continue;
				}

				$formatted_line_parts[] = $line_part;
			}

			if ( empty( $formatted_line_parts ) ) {
				continue;
			}

			$formatted_parts[] = implode( ' ', $formatted_line_parts );
		}

		return implode( PHP_EOL, $formatted_parts );
	}

	/**
	 * Callback to transform a term into a placeholder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $term Term to get a placeholder for.
	 * @return string The placeholder.
	 */
	private function make_placeholder( $term ) {
		return '{' . $term . '}';
	}
}
