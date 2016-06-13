jQuery( function ($) {
	var WSI_Generetro = window.WSI_Generetro || {};

	var totalImagesToProcess = 0;

	WSI_Generetro.init = function() {
		var $form = $('#wsi-regeneretro'),
			$fileList = $('.wsi-file-list', $form);


		$form.submit( function(e) {
			e.preventDefault();

			totalImagesToProcess = WSI_Generetro_Data.ids.length;

			// do we have anything to process at all?
			if ( totalImagesToProcess <= 0 ) {
				alert( WSI_Generetro_Data.l10n.alert_no_images );
				return;
			}

			// Disable regenerate button
			$(':submit', $form).attr('disabled', true);
			$('.status', $form).removeClass('status-display');
			$('.spinner', $form).addClass('is-active');

			WSI_Generetro.newRun( WSI_Generetro_Data.ids, $fileList, function() {
				// This will run when process is done
				$(':submit', $form).attr('disabled', false);
				$('.status', $form).addClass('status-display');
				$('.spinner', $form).removeClass('is-active');
			} );
		});
	};

	WSI_Generetro.newRun = function( ids, $el, cb, i ) {
		var currentID = ids.shift();

		// iteration
		if ( i === undefined ) {
			i = 1;
		}

		// did we end the loop?
		if ( currentID === undefined ) {
			return cb();
		}

		// Run it single time
		WSI_Generetro.singleRun( currentID, function() {
			$('<li>').text('Completed #' + currentID + ' (' + i + '/' + totalImagesToProcess + ')' ).appendTo($el);

			// re-run
			WSI_Generetro.newRun( ids, $el, cb, ++i );
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