<?php


/**
 * Get ticket count for event
 *
 * @since 1.0
 * @since 4.4.6 added $only_count_available_tickets parameter
 * @author jgabois & Justin Endler
 *
 * @param int  $post_id the event ID (optional if used in the loop)
 * @param bool $only_count_available_tickets
 *
 * @return int the number of tickets for an event
 */
function tribe_eb_get_ticket_count( $post_id = null, $only_count_available_tickets = false ) {
	$api = tribe( 'eventbrite.event' );
	$post_id = Tribe__Events__Main::postIdHelper( $post_id );
	$event = $api->get_event( $post_id );
	$count = 0;

	// If we are interested in *all* tickets regardless of availability, we can take a shortcut
	if ( ! empty( $event->ticket_classes ) && ! $only_count_available_tickets ) {
		$count = count( $event->ticket_classes );
	} elseif ( ! empty( $event->ticket_classes ) ) {
		// Otherwise we need to iterate through the tickets and count those that are definitely availalbe
		foreach ( $event->ticket_classes as $ticket ) {
			if ( 'UNAVAILABLE' !== $ticket->on_sale_status ) {
				$count++;
			}
		}
	}

	/**
	 * Count of Eventbrite tickets for the specified event.
	 *
	 * @since 4.4.6 added $post_id and $only_count_availalbe_tickets parameters
	 *
	 * @param int  $count
	 * @param int  $post_id
	 * @param bool $only_count_available_tickets
	 */
	return apply_filters( 'tribe_eb_get_ticket_count', $count, $post_id, $only_count_available_tickets );
}

/**
 * Returns the Eventbrite id for the post/event
 *
 * @since 1.0
 * @author jgabois & Justin Endler
 * @param int $post_id the event ID (optional if used in the loop)
 * @return int event id, false if no event is associated with post
 */
function tribe_eb_get_id( $post_id = null ) {
	$api = tribe( 'eventbrite.event' );
	return $api->get_event_id( $post_id );
}

/**
 * Determine if an event is live at Eventbrite
 *
 * @since 1.0
 * @author jgabois & Justin Endler
 * @param int $post_id the event ID (optional if used in the loop)
 * @return bool true if live
 */
function tribe_eb_is_live_event( $post_id = null ) {
	$api = tribe( 'eventbrite.event' );
	return $api->is_live( $post_id );
}

/**
 * Determine an event's Eventbrite status
 *
 * @since 1.0
 * @author jkudish
 * @param int $post_id the event ID (optional if used in the loop)
 * @return string the event status
 */
function tribe_eb_event_status( $post_id = null ) {
	$api = tribe( 'eventbrite.event' );
	return $api->get_event_status( $post_id );
}

/**
 * Outputs the Eventbrite ticket iFrame. The post in question must be registered with Eventbrite
 * and must have at least one ticket type associated with the event.
 *
 * @since 1.0
 * @author jkudish
 * @param int $postId the event ID (optional if used in the loop)
 * @return void
 */
function tribe_eb_event( $deprecated = null ) {
	echo Tribe__Events__Tickets__Eventbrite__Main::print_ticket_form();
}

/**
 * Determine whether to show tickets
 *
 * @since 1.0
 *
 * @param int $post_id the event ID (optional if used in the loop)
 * @param object $event Optional. The Eventbrite API response for event being checked.
 * @return bool
 */
function tribe_event_show_tickets( $post_id = null, $event = null ) {

	$post_id          = Tribe__Events__Main::postIdHelper( $post_id );
	$show_tickets     = get_post_meta( $post_id, '_EventShowTickets', true );
	$should_show_form = tribe_is_truthy( $show_tickets );

	/**
	 * By default, if an event is of the "Canceled", "Completed", "Ended" status, force the ticket form to never show.
	 *
	 * @since 4.4.7
	 *
	 * @param array $statuses The statuses where the event should forcefully have its ticket form hidden.
	 */
	$force_hide_statuses = apply_filters( 'tribe_events_eventbrite_hide_iframe_statuses', array( 'completed', 'ended', 'canceled' ) );

	if (
		! empty( $event ) &&
		! empty( $force_hide_statuses ) &&
		isset( $event->status ) &&
		in_array( $event->status, $force_hide_statuses )
	) {
		$should_show_form = false;
	}

	/**
	 * Let users override the behavior of this template tag and force the form to show.
	 *
	 * @since 1.0
	 *
	 * @param bool $should_show_form Whether to show the form or not. Defaults to yes.
	 */
	return apply_filters( 'tribe_event_show_tickets', $should_show_form );
}