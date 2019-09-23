( function( $, obj ) {
	'use strict';

	obj.selector = {
		migrate: '#eventbrite-migrate-events',
	};

	$( document ).ready( function() {
		$( obj.selector.migrate ).on( 'click', function( event ) {
			event.preventDefault();
			var $notice = $( obj.selector.migrate ).parents( '.tribe-dismiss-notice' );

			$notice.find( '.spinner' ).css( 'visibility', 'visible' );

			$.ajax( ajaxurl, {
				dataType: 'json',
				method: 'POST',
				data: {
					action: 'tribe_eventbrite_migrate_events',
					'eventbrite-migrate-events': true,
				},
				success: function() {
					$notice.remove();
				}
			} );

			return false;
		} );
	} );
} )( jQuery, {} );
