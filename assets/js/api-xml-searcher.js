jQuery(document).ready(function($){
    const url = ajax_values.ajax_url;
    $('#domain-searcher-submit').click(function(e){
        e.preventDefault();
        let search_text = $('#domain-searcher-input').val();

        $.ajax({
            url : url,
            data: {
                action: 'query_xml_api',
                search_text: search_text
            },
            type: 'POST',
            success: function( result ){
                let domain_response = JSON.parse(result);
                $.each(domain_response, function(index,value){
                    // console.log(index);
                    // console.log(value);
                    
                    if ( 1 == value.availability ) {
                        $('#registration').append( '<p>' + value.name + ' is available for purchase <button>Register</button></p>' );
                    }
                    if ( 0 == value.availability ) {
                        $('#registration').append( '<p>' + value.name + ' is NOT available for purchase</p>' );
                    }
                });
                // console.log( domain_response );
            },
            error: function( result ){
                console.log( result );
            },
        });
        console.log(search_text);
    });
});