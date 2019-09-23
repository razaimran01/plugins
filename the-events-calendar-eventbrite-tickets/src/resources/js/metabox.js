// No custom events from WP core to signify an update on the Featured Image post meta box (in Classic Editor),
// and the input we *could* use JS's change event on is removed and replaced. So we have to listen to DOM changes directly.
( function() {

    'use strict';

    var obj = {
        checkbox : document.getElementById( 'tribe-eventbrite--image-sync-checkbox' ),
        imagediv : document.getElementById( 'postimagediv' ),
        updateCheckbox : function( e ) {
            window.TEC.should_send_post_thumbnail_to_eb = e.target.checked;
        }
    };

    obj.checkbox.addEventListener( 'change', obj.updateCheckbox );

    var observer = new MutationObserver( function( mutations ) {

        mutations.forEach( function( mutation ) {

            if ( 'childList' !== mutation.type ) {
                return;
            }

            // Use the new element inserted by WP's AJAX response.
            obj.checkbox = document.getElementById( 'tribe-eventbrite--image-sync-checkbox' );
            obj.checkbox.addEventListener( 'change', obj.updateCheckbox );

            if ( true != window.TEC.should_send_post_thumbnail_to_eb ) {
                obj.checkbox.removeAttribute( 'checked' );
            } else {
                obj.checkbox.checked = true;
            }
        });
    });

    observer.observe( obj.imagediv, {
        subtree: true,
        childList: true,
    } );

} )();