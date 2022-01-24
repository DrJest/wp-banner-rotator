jQuery( document ).ready( function( $ ){

  $( document ).on('click', '.wpbr-banner', function(e) {

    wp.ajax.post( 'wpbr_track', {

      banner_id: $(this).attr('data-id')

    } );

  });

});
