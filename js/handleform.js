( function( $ ) {

	const elForm = $('#form-new-post');	
	const el_form_submit = $('.submit');	
	const postTitleField = $('#post_title_id');
	const postContentField = $('#post_content_id');
	const postAuthor = $('.post_author1');
	const postTitleLabel = document.querySelector('.post-title-label');
	const postContentLabel = document.querySelector('.post-content-label');

	el_form_submit.attr('disabled', 'disabled');
	
	postTitleField.on('focusout', getEndpoint);
	
	postTitleField.on('focus',function(){
		el_form_submit.attr('disabled', 'disabled');
	})
	
	elForm.on('submit', function (e) {
        e.preventDefault();
		if ( postContentField.val() !=='') {
			console.log(postContentField.text())
			console.log(postContentField.val())
			$('.login-link + a').remove();
			postEndpoint();
		} else {
			postContentLabel.textContent = 'Content empty';
		}
    });

	function getEndpoint(e) {
		
		// Send request
		$.ajax( {
			url: wpApiSettings.root + 'filoxposter/v1/check-post-title/' + e.target.value,
			})
			.done(function(data){
				postTitleLabel.textContent = 'cool';
				el_form_submit.removeAttr('disabled');
			})
			.fail(function(data){
				console.log( 'fail' );
				postTitleLabel.textContent = 'failed';
			})
	}
	
	function postEndpoint(e) {
		
			// Send request
			$.ajax( {
				url: wpApiSettings.root + 'filoxposter/v1/create-post',
				method: 'POST',
				beforeSend: function ( xhr ) {
					xhr.setRequestHeader( 'X-WP-Nonce', wpApiSettings.nonce );
				},
				dataType: 'json',
				data: {
					'post_title': postTitleField.val(),
					'post_content': postContentField.val(),
					'post_author':  postAuthor.val(),
				},
				})
				.done(function(data){
					console.log( 'success' );
					el_form_submit.attr('disabled', 'disabled');
					postTitleField.val('') ;
					postContentField.val('');
					postTitleLabel.textContent = 'Please enter the post title';
					elForm.append(
						$(document.createElement('a')).prop({
							target: '_self',
							href: data.guid,
							innerText: data.post_title

						})
					);

				})
				.fail(function(data){
					console.log( 'fail' );
					postTitleLabel.textContent = 'failed';
				})
			
	}

} )( jQuery );