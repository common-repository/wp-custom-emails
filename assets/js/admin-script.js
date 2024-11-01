
/*
 * Manages animations of the setting details panel
 */
function wtbp_toggle_settings_fields( target, action ) {

    var row = target.parent().parent();
    var title = row.next();
    var message = row.next().next();
    var btn_show = row.find( '.wtbp-show-details' );
    var btn_hide = row.find( '.wtbp-hide-details' );

    if ( 'show' === action ) {

        // buttons animations 
        btn_show.hide( 100 );
        btn_hide.show( 100 );

        // the rows animations 
        title.show( 300 );
        message.show( 300 );

    } else if ( 'hide' === action ) {

        // buttons animations 
        btn_hide.hide( 100 );
        btn_show.show( 100 );

        // the rows animations 
        title.hide( 300 );
        message.hide( 300 );

    } else if ( 'custom' === action ) {

        // buttons
        btn_show.hide( 300 );
        btn_hide.show( 300 );

        title.show( 300 );
        message.show( 300 );

    } else if ( 'bypass' === action || 'disabled' === action ) {

        // buttons
        btn_hide.hide( 100 );
        btn_show.hide( 100 );

        title.hide( 300 );
        message.hide( 300 );


    }

}


jQuery( document ).ready( function ( $ ) {

    // hide some fields
    $( '.wtbp-ce-hidden-field' ).hide();
    $( '.wtbp-details' ).hide();


    $( "#wtbp-settings select" ).each( function () {

        if ( 'custom' === $( this ).val() ) {
            $( this ).next().children( '.wtbp-show-details' ).show( 100 );
        }

    } );


    // change mode
    $( "#wtbp-settings select" ).change( function () {
        var action = $( this ).val();
        wtbp_toggle_settings_fields( $( this ), action );

    } );

    // button
    $( ".wtbp-show-details" ).click( function () {
        var target = $( this ).parent().prev();
        wtbp_toggle_settings_fields( target, 'show' );


    } );

    // button
    $( ".wtbp-hide-details" ).click( function () {
        var target = $( this ).parent().prev();
        wtbp_toggle_settings_fields( target, 'hide' );

    } );

    // Restore default title and message
    $( ".wtbp-ce-default-btn" ).click( function () {
        var title_orig = $( this ).parent().parent().parent().prev().find( 'input:first' );
        var title_default = title_orig.next( '.wtbp-ce-default-title' );
        var mes_orig = $( this ).parent().parent().find( '.wtbp_ce_message_textarea' );
        var mes_default = $( this ).parent().parent().find( '.wtbp-ce-default-text' );

        var r = confirm( "Are you sure you want to restore the default value?" );
        if ( r == true ) {




            // Title
            title_orig.hide();
            title_orig.val( title_default.val() );
            title_orig.fadeIn();

            // Message
            if ( mes_orig.hasClass( 'wp-editor-area' ) ) { // if is tinyMCE
                var id = mes_orig.attr( 'id' );
                var val = mes_default.val();
                tinymce.get( id ).setContent( val.replace( /\n/g, "<br />" ) );
            } else {
                mes_orig.hide();
                mes_orig.val( mes_default.val() );
                mes_orig.fadeIn();
            }

        }

    } );



} );