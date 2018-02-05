/*!
 * WP Site Identity (https://github.com/felixarntz/wp-site-identity)
 * By Felix Arntz (https://leaves-and-love.net)
 * Licensed under GNU General Public License v3 (http://www.gnu.org/licenses/gpl-3.0.html)
 */
( function( wp, _ ) {

	var Select = wp.media.view.MediaFrame.Select;
	var InsertFrame;

	InsertFrame = Select.extend({
		initialize: function() {
			_.defaults( this.options, {
				multiple: false,
				editing: false,
				state: 'insert',
				metadata: {}
			});

			Select.prototype.initialize.apply( this, arguments );
		},

		createStates: function() {
			var query = this.options.mimeType ? { type: this.options.mimeType } : {};

			this.states.add([
				new wp.media.controller.Library({
					id: 'insert',
					title: this.options.title,
					selection: this.options.selection,
					priority: 20,
					toolbar: 'main-insert',
					filterable: 'dates',
					library: wp.media.query( query ),
					multiple: false,
					editable: true,
					displaySettings: false,
					displayUserSettings: false
				}),

				new wp.media.controller.EditImage({ model: this.options.editImage })
			]);
		},

		bindHandlers: function() {
			Select.prototype.bindHandlers.apply( this, arguments );

			this.on( 'toolbar:create:main-insert', this.createToolbar, this );

			this.on( 'content:render:edit-image', this.renderEditImageContent, this );
			this.on( 'toolbar:render:main-insert', this.renderMainInsertToolbar, this );
		},

		renderEditImageContent: function() {
			var view = new wp.media.view.EditImage({
				controller: this,
				model: this.state().get( 'image' )
			}).render();

			this.content.set( view );

			view.loadEditor();
		},

		renderMainInsertToolbar: function( view ) {
			var controller = this;

			view.set( 'insert', {
				style: 'primary',
				priority: 80,
				text: controller.options.buttonText,
				requires: { selection: true },
				click: function() {
					controller.close();
					controller.state().trigger( 'insert', controller.state().get( 'selection' ) ).reset();
				}
			});
		}
	});

	wp.media.view.MediaFrame.WPSIInsertFrame = InsertFrame;

})( wp, _ );
