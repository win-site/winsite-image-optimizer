jQuery( function ($) {
	var WSI_Generetro = window.WSI_Generetro || {};

	WSI_Generetro.init = function() {
		var $form = $('#wsi-regeneretro'),
			$fileList = $('.wsi-file-list', $form);


		$form.submit( function(e) {
			e.preventDefault();

			// do we have anything to process at all?
			if ( WSI_Generetro_Data.ids.length <= 0 ) {
				alert( WSI_Generetro_Data.l10n.alert_no_images );
			}

			// loop it all
			$.each( WSI_Generetro_Data.ids, function( idx, id ) {
				WSI_Generetro.singleRun( id, function() {
					$('<li>').text('Completed ' + id).appendTo($form);
				} );
			});
		});
	};
	WSI_Generetro.singleRun = function( id, cb ) {
		// loop ID by ID and run it
		var payload = {
			action: 'wsi-regeneretro',
			id: id
		};

		$.post(ajaxurl, payload, function( res ) {
			cb(res);
		});
	};

	WSI_Generetro.init();
});