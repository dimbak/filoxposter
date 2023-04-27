(function ($) {

    var el_form = $('#form-new-post'),
        el_form_submit = $('.submit', el_form);

    // Fires when the form is submitted.
    el_form.on('submit', function (e) {
        e.preventDefault();

        el_form_submit.attr('disabled', 'disabled');

        new_post();
    });

    // Ajax request.
    function new_post() {
        $.ajax({
            url: localized_new_post_form.admin_ajax_url,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'new_post', // Set action without prefix 'wp_ajax_'.
                form_data: el_form.serialize()
            },
            cache: false
        }).done(function (r) {
			console.log(r);

			//3. When form is submitted 
			// if post_title doesn't exist create the post and return the link of the new post. 
			// If post exists or post_title/post_content is empty return error message.

            if (r.post !== '' && r.preview_link !== '') {
                $('[name="ID"]', el_form).attr('value', r.post.ID);
                $('.preview-link', el_form)
                    .attr('href', r.preview_link)
                    .show();
                el_form_submit.attr('data-is-updated', 'true');
                el_form_submit.text(el_form_submit.data('is-update-text'));
            }  else { 
				
				$('.error-message', el_form)
				.text('Error')
				.css('color',"red")
				.show();
			} 

            el_form_submit.removeAttr('disabled');
        });
    }

    // Used to trigger/simulate post submission without user action.
    function trigger_new_post() {
        el_form.trigger('submit');
    }

    // Sets interval so the post the can be updated automatically provided that it was already created.
    setInterval(function () {
        if (el_form_submit.attr('data-is-updated') === 'false') {
            return false;
        }

        trigger_new_post();
    }, 5000); // Set to 5 seconds.

})(jQuery);