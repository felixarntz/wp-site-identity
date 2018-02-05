( function( data ) {

	var typeRadios       = document.querySelectorAll( '#wpsi-owner-data-type input[type="radio"]' );
	var typeRadioChecked = document.querySelector( '#wpsi-owner-data-type input[type="radio"]:checked' );
	var colors           = document.querySelectorAll( '.form-table input[data-colorpicker]' );
	var images           = document.querySelectorAll( '.form-table input[data-imagepicker]' );

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

	function createMediaPreviewContent( attachment ) {
		var imageUrl = attachment.url;
		if ( attachment.sizes ) {
			if ( attachment.sizes.medium ) {
				imageUrl = attachment.sizes.medium.url;
			} else if ( attachment.sizes.large ) {
				imageUrl = attachment.sizes.large.url;
			} else if ( attachment.sizes.full ) {
				imageUrl = attachment.sizes.full.url;
			}
		}

		return '<img src="' + imageUrl + '" alt="' + attachment.alt + '" />';
	}

	function openImageMediaModal() {
		var field        = document.getElementById( this.dataset.target );
		var preview      = document.getElementById( this.dataset.target + '-preview' );
		var selectButton = this;
		var deleteButton = document.getElementById( this.dataset.target + '-delete-button' );

		var models     = field.attachment ? [ field.attachment ] : [];
		var selection  = new window.wp.media.model.Selection( models, {
			multiple: false
		});
		var mediaFrame = new window.wp.media.view.MediaFrame.WPSIInsertFrame({
			title: data.imageButtonLabels.frameTitle,
			buttonText: data.imageButtonLabels.frameButton,
			frame: 'select',
			state: 'insert',
			selection: selection,
			mimeType: 'image',
			multiple: false
		});

		window.wp.media.frame = mediaFrame;

		mediaFrame.on( 'insert', function() {
			var attachment = {};

			window._.extend( attachment, mediaFrame.state().get( 'selection' ).first().toJSON() );

			field.value       = attachment.id;
			field.attachment  = attachment;
			preview.innerHTML = createMediaPreviewContent( attachment );

			selectButton.textContent = data.imageButtonLabels.change;
			deleteButton.style.setProperty( 'display', 'inline-block' );
		});

		selection.on( 'destroy', function( attachment ) {
			if ( parseInt( field.value, 10 ) === attachment.get( 'id' ) ) {
				field.value       = 0;
				field.attachment  = null;
				preview.innerHTML = '';

				selectButton.textContent = data.imageButtonLabels.select;
				deleteButton.style.setProperty( 'display', 'none' );
			}
		});

		mediaFrame.open();
		mediaFrame.$el.find( '.media-frame-menu .media-menu-item.active' ).focus();
	}

	function removeImage() {
		var field        = document.getElementById( this.dataset.target );
		var preview      = document.getElementById( this.dataset.target + '-preview' );
		var selectButton = document.getElementById( this.dataset.target + '-select-button' );
		var deleteButton = this;

		field.value       = 0;
		field.attachment  = null;
		preview.innerHTML = '';

		selectButton.textContent = data.imageButtonLabels.select;
		deleteButton.style.setProperty( 'display', 'none' );
	}

	function initializeImageField( field ) {
		var selectButton = document.createElement( 'button' );
		var deleteButton = document.createElement( 'button' );
		var value        = parseInt( field.value, 10 );
		var attachment   = document.getElementById( field.id + '-attachment-data' );

		if ( attachment ) {
			field.attachment = JSON.parse( attachment.textContent );
		}

		field.type = 'hidden';

		selectButton.type = 'button';
		selectButton.id = field.id + '-select-button';
		selectButton.classList.add( 'button' );
		selectButton.textContent = value ? data.imageButtonLabels.change : data.imageButtonLabels.select;
		selectButton.dataset.target = field.id;
		selectButton.addEventListener( 'click', openImageMediaModal );

		deleteButton.type = 'button';
		deleteButton.id = field.id + '-delete-button';
		deleteButton.classList.add( 'button-link', 'button-link-delete' );
		deleteButton.textContent = data.imageButtonLabels.remove;
		deleteButton.dataset.target = field.id;
		deleteButton.addEventListener( 'click', removeImage );
		deleteButton.style.setProperty( 'margin-left', '10px' );
		deleteButton.style.setProperty( 'padding-bottom', '1px' );
		deleteButton.style.setProperty( 'line-height', '26px' );
		if ( ! value ) {
			deleteButton.style.setProperty( 'display', 'none' );
		}

		field.parentNode.insertBefore( selectButton, field );
		field.parentNode.insertBefore( deleteButton, field );
	}

	if ( typeRadios.length && data.typeDependencies ) {
		Array.from( typeRadios ).forEach( function( typeRadio ) {
			typeRadio.addEventListener( 'change', function() {
				toggleTypeDependencies( this.value );
			});
		});

		if ( typeRadioChecked ) {
			toggleTypeDependencies( typeRadioChecked.value );
		}
	}

	if ( colors.length && window.jQuery && window.jQuery.fn.wpColorPicker ) {
		Array.from( colors ).forEach( function( color ) {
			window.jQuery( color ).wpColorPicker();
		});
	}

	if ( images.length && window.wp.media && data.imageButtonLabels ) {
		Array.from( images ).forEach( initializeImageField );
	}

} )( wpsiSettingsPage );
