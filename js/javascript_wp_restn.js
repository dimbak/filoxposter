( function( $ ) {
	// Send request

	
	const postTitleField = document.querySelector('.form-control');
	const postTitleField1 = document.querySelector('.post-title');

	postTitleField.addEventListener('focusout', callEndPoint);

	function callEndPoint(e) {

		$.ajax( {
			url: wpApiSettings.root + 'awhitepixel/v1/getsomedata/'+ e.target.value,
			method: 'GET',
			// data: {
			// 	product_id: 14
			// },
		})
		.done ( function( data )  {
				console.log( data );

		})
		.fail( function() {
		//	alert('asdsa')
		});

		
	}
} )( jQuery );
