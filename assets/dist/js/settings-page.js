/*!
 * WP Site Identity (https://github.com/felixarntz/wp-site-identity)
 * By Felix Arntz (https://leaves-and-love.net)
 * Licensed under GNU General Public License v3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
( function( data ) {

	var typeRadios       = document.querySelectorAll( '#wpsi-owner-data-type input[type="radio"]' );
	var typeRadioChecked = document.querySelector( '#wpsi-owner-data-type input[type="radio"]:checked' );

	if ( ! typeRadios.length ) {
		return;
	}

	function toggleTypeDependencies( type ) {
		var dependencies = data.typeDependencies[ type ];
		var names;
		var wrap;

		if ( ! dependencies ) {
			return;
		}

		names = Object.keys( dependencies );

		names.forEach( function( name ) {
			wrap = document.querySelector( '[name="wpsi_owner_data\[' + name + '\]"]' );

			while ( wrap && 'tr' !== wrap.tagName.toLowerCase() ) {
				wrap = wrap.parentNode;
			}

			if ( ! wrap ) {
				return;
			}

			if ( dependencies[ name ] ) {
				wrap.style.setProperty( 'display', 'table-row' );
			} else {
				wrap.style.setProperty( 'display', 'none' );
			}
		});
	}

	Array.from( typeRadios ).forEach( function( typeRadio ) {
		typeRadio.addEventListener( 'change', function() {
			toggleTypeDependencies( this.value );
		});
	});

	if ( typeRadioChecked ) {
		toggleTypeDependencies( typeRadioChecked.value );
	}

} )( wpsiSettingsPage );
