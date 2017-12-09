/*!
 * WP Site Identity (https://github.com/felixarntz/wp-site-identity)
 * By Felix Arntz (https://leaves-and-love.net)
 * Licensed under GNU General Public License v3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
( function( data ) {

	wp.customize.bind( 'ready', function() {

		wp.customize( 'wpsi_owner_data[type]', function( setting ) {
			function toggleTypeDependencies( type ) {
				var dependencies = data.typeDependencies[ type ];
				var names;
				var control;

				if ( ! dependencies ) {
					return;
				}

				names = Object.keys( dependencies );

				names.forEach( function( name ) {
					control = wp.customize.control( 'wpsi_owner_data[' + name + ']' );

					if ( ! control ) {
						return;
					}

					if ( dependencies[ name ] ) {
						control.container.slideDown( 180 );
					} else {
						control.container.slideUp( 180 );
					}
				});
			}

			toggleTypeDependencies( setting.get() );
			setting.bind( function() {
				toggleTypeDependencies( setting.get() );
			});
		});
	});

} )( wpsiCustomizeControls );
