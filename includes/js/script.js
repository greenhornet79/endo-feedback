( function( $ )  {

	$(document).ready( function() {
		
		$('.endo-feedback-button').click(function() {
			var text = $(this).text();
			
			$('.endo-feedback-wrapper').toggleClass('is-visible');

			if ( text == 'Give Feedback' ) {
				$(this).text('X');
			} else {
				$(this).text('Give Feedback');
			}

		});

		$('#endo-feedback-form').submit(function(e) {
			e.preventDefault();

			$('#endo-feedback-form .is-submit').text('Submitting...');

			var message = $('textarea[name="message"]').val();
			var question = $('input[name="question"]').val();
			var nonce = $('#endo_feedback_nonce_field').val();
			var referrer = $('input[name="_wp_http_referer"]').val();

			var data = {
				action: 'endo_feedback_process',
				message: message,
				question: question,
				nonce: nonce,
				referrer: referrer
			};

			$.get(endo_feedback_script.ajaxurl, data, function(resp) {
				// $(this).after(resp).remove();
				console.log(resp);
				
				$('.endo-feedback-form-wrapper').html('<p style="margin-bottom: 10px;">Thanks for your feedback!</p>').delay(1500).fadeOut(1000);
				$('.endo-feedback-button').delay(1500).fadeOut(1000);


			});
		});

	});


})( jQuery );