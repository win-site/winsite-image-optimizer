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

			WSI_Generetro.newRun( WSI_Generetro_Data.ids, $fileList );
		});
	};

	WSI_Generetro.newRun = function( ids, $el ) {
		var currentID = ids.shift();

		// did we end the loop?
		if ( currentID === undefined )
			return;

		// Run it single time
		WSI_Generetro.singleRun( currentID, function() {
			$('<li>').text('Completed #' + currentID).appendTo($el);

			// re-run
			WSI_Generetro.newRun( ids, $el );
		} );
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