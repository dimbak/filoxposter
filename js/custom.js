var postForm = $( '#post-form' );
var jsonData = function( form ) {
    var arrData = form.serializeArray(),
        objData = {};
    
    $.each( arrData, function( index, elem ) {
        objData[elem.name] = elem.value;
    });
    
    return JSON.stringify( objData );
};
postForm.on( 'submit', function( e ) {
    e.preventDefault();
    
    $.ajax({
        url: 'https://dbtest.gr/rain/wp-json/wp/v2/posts',
        method: 'POST',
        data: jsonData( postForm ),
        crossDomain: true,
        contentType: 'application/json',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'Authorization', 'Basic username:password' );
        },
        success: function( data ) {
            console.log( data );
        },
        error: function( error ) {
            console.log( error );
        }
    });
});