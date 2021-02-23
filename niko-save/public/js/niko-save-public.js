jQuery(document).ready(function($) {

	'use strict';

	/**
	 * Create our notification
	 */
	function nikoAddNotification( text, url ) {

		var $appendTo = $('body'),
			$content = '<div class="niko-save-notification"><span class="notification-icon"></span><a href="'+url+'" class="notification-link">'+text+'</a></div>';
		$appendTo.append($content);
		setTimeout(function() {
			$('.niko-save-notification').addClass('active');
		}, 250);
		setTimeout(function() {
			$('.niko-save-notification').removeClass('active');
			setTimeout(function() {
				$('.niko-save-notification').remove();
			}, 250);
		}, 3250);
		
	}

	/**
	 * Save/unsave item.
	 */
	if ( $('.niko-save-button').length ) {

		// ^ this was to check if the button exists, now, let's do something with it
		$('.niko-save-button').on('click', function(event) {

			// Prevents the default behaviour of the button.
			event.preventDefault();

			// Make sure that the user can't click the button multiple times unless
			// AJAX call is finished.
			var anchor = $(this);
			if ( anchor.data('disabled') ) {

				return false;

			}
			anchor.data('disabled', 'disabled');

			// Let's get some basic variables here that we're going to need.
			var $this = $(this),
				item_id = $this.data('item-id'),
				nonce = $this.data('nonce');

			// Also, let's search through parent to see if this item is found on saved page.
			var parent = $this.parents('.niko-saved-item');

			// Let's do some AJAX
			$.ajax({
				type: 'post',
				url: niko_save_ajax.ajax_url,
				data: {
					'nonce': nonce,
					'item_id': item_id,
					'action': 'save_unsave_item'
				},
				success: function(data) {
					// If true, remove from saved, else, add to saved.
					if ( data.is_saved == true ) {

						$this.removeClass('saved');
						$this.find('span.niko-save-text').text(niko_save_ajax.item_save_text);

						if ( parent.hasClass('toptal-saved-item') ) {
							parent.fadeOut(500);
							setTimeout(function() {
								parent.remove();
							}, 500);
						}

					} else {

						$this.addClass('saved');
						$this.find('span.niko-save-text').text(niko_save_ajax.item_unsave_text);

						// Show our notification
						nikoAddNotification( niko_save_ajax.item_saved_text, niko_save_ajax.saved_page_url );

					}

					anchor.removeData('disabled');
				},
				error: function(error) {
					console.log(error);
				}
			});

		});

	}

});