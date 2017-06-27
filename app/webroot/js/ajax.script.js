$( document ).ready( function() {
    function handleSuccess( response ) {
        var data = response.data;
        data = JSON.parse( data );
        
        $("#js-search-tags").empty();
        data.forEach( function( element ){
            $( "#js-search-tags" ).append( "<li><a class='w3-button' href='/search?tags=" + 
                                    element + "'>" + element + "</a></li>" ); 
        });
        $( "#js-search-tags" ).show();
    }
    
    function handleFail() {
        $( "#js-search-tags" ).empty();
        $( "#js-search-tags" ).append( "<li>Ничего не найдено</li>" );
        $( "#js-search-tags" ).show();
    }
    
    $( "#js-search" ).on( {
        keyup: function( event ) {
            if ( $( "#js-search" ).val().length >= 2 ) {
                var data =  $( this ).serialize();
                
                console.log(data);
                
                $.ajax({
                    type: 'POST',
                    url: '/search/bar',
                    data: data,
                    statusCode: {
                        200: handleSuccess,
                        404: handleFail
                    }
                });
            }
        },
        
        blur: function( event ) {
            if ( $( "#js-search" ).val().length == 0) {
                $( "#js-search" ).val('');
                $( "#js-search-tags" ).hide();
            }
        }
    });
});