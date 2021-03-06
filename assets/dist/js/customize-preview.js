/*!
 * WP Site Identity (https://github.com/felixarntz/wp-site-identity)
 * By Felix Arntz (https://leaves-and-love.net)
 * Licensed under GNU General Public License v3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
( function( data ) {

	wp.customize.selectiveRefresh.partialConstructor.WPSiteIdentityPartial = wp.customize.selectiveRefresh.Partial.extend({
		initialize: function( id, options ) {
			if ( options && options.primarySetting && options.settings && 1 === options.settings.length && data.liveSettings.includes( options.primarySetting.replace( 'wpsi_owner_data[', '' ).replace( ']', '' ) ) ) {
				wp.customize( options.primarySetting, function( setting ) {
					setting.bind( function( value ) {
						var items = document.querySelectorAll( options.selector );

						Array.from( items ).forEach( function( item ) {
							item.textContent = value;
						});
					});
				});
			}

			wp.customize.selectiveRefresh.Partial.prototype.initialize.apply( this, [ id, options ] );
		}
	});

} )( wpsiCustomizePreview );
