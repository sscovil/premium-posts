jQuery( document ).ready( function($) {
    $( document ).on( 'click', '.editinline', function(){
        var post_tr_id = $( this ).parents( 'tr' ).attr( 'id' );
        var is_checked = $( ':input[name="premium_post_placeholder"]', '#'+post_tr_id ).is( ':checked' );
        if ( true == is_checked ) {
            $( ':input[name="premium_post"]', '.inline-edit-row' ).prop( 'checked', true );
        } else {
            $( ':input[name="premium_post"]', '.inline-edit-row' ).prop( 'checked', false );
        }
        return false;
    });
});