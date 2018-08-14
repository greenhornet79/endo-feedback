var ef = new Vue({
	el: '#endo-feedback',
	data: {
		show: false,
		submitting: false,
		question: 'What do you think of our new design?',
		ctaButton: 'Give Feedback',
		submitButton: 'Submit',
		message: '',
		success: false
	},
	methods: {
		onSubmit: function() {
			this.submitting = true;
			this.submitButton = 'Submitting...';

			fetch(endo_feedback_script.ajaxurl, {
		      method: 'POST',
		      credentials: 'same-origin',
		      headers: new Headers({'Content-Type': 'application/x-www-form-urlencoded'}),
		      body: 'action=endo_feedback_process&message=' + this.message + '&question=' + this.question + '&referrer=' + document.querySelector('input[name="_wp_http_referer"]').value
		    })
		    .then((resp) => resp.json())
		    .then(function(data) {
		   
		      if(data.response == "success"){
		      	ef.show = false;
		        ef.success = true;
		        setTimeout(function(){
	                ef.success = false;
	            }, 1000);
		      }
		    })
		    .catch(function(error) {
		      console.log(JSON.stringify(error));
		    });

		}
	}
});