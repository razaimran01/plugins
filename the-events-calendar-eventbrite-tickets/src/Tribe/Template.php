<?php
/**
 * @for Address Module Template
 * This file contains the hook logic required to create an effective address module view.
 *
 * @package Tribe__Events__MainCalendar
 * @since  2.1
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( ! class_exists( 'Tribe__Events__Tickets__Eventbrite__Template' ) ) {
	class Tribe__Events__Tickets__Eventbrite__Template extends Tribe__Events__Template_Factory {
		/**
		 * fire up the EventBrite Template
		 * @return void
		 * @author tim@imaginesimplicity.com
		 */
		public static function init() {
			// Start address template
			add_filter( 'tribe_events_eventbrite_before_the_tickets', array( __CLASS__, 'before_tickets' ), 1, 1 );

			// Address meta
			add_filter( 'tribe_events_eventbrite_the_tickets', array( __CLASS__, 'the_tickets' ), 1, 1 );

			// End address template
			add_filter( 'tribe_events_eventbrite_after_the_tickets', array( __CLASS__, 'after_tickets' ), 1, 2 );
		}

		/**
		 * before tickets injection
		 * @param  int $post_id
		 * @return string
		 * @author tim@imaginesimplicity.com
		 */
		public static function before_tickets( $post_id ) {
			$html = '';
			return apply_filters( 'tribe_template_factory_debug', $html, 'tribe_events_eventbrite_before_the_tickets' );
		}

		/**
		 * Ticket html form.
		 *
		 * @return string
		 */
		public static function the_tickets() {
			$post_id  = get_the_ID();
			$api = tribe( 'eventbrite.event' );
			$event = $api->get_event( $post_id );

			if ( ! $event ) {
				return;
			}

			$event_id = $event->id;
			$num_visible_tickets = 0;

			foreach ( $event->ticket_classes as $ticket ) {
				$hidden_ticket = isset( $ticket->hidden ) ? $ticket->hidden : false;

				if ( ! $hidden_ticket ) {
					$num_visible_tickets++;
				}
			}

			$iframe_url = 'https://www.eventbrite.com/tickets-external?eid=%s&amp;ref=etckt&v=2';
			$iframe_url = apply_filters( 'tribe_events_eb_iframe_url', sprintf( $iframe_url, $event_id ) );
			$iframe_height = 166 + ( $num_visible_tickets * 103 ); // Best guess, great room for variation

			/**
			 * Sets the height of the event ticket iFrame.
			 *
			 * @param int    $iframe_height        Height of the iFrame.
			 * @param int    $post_id              Post ID for this event.
			 * @param object $event                The event object retrieved from the Eventbrite API.
			 * @param int    $num_visible_tickets Number of tickets this event has.
			 */
			$iframe_height = apply_filters( 'tribe_events_eventbrite_iframe_height', $iframe_height, $post_id, $event, $num_visible_tickets );

			$html = '';

			$display_iframe = ! empty( $event_id ) &&
			                  ( isset( $event->listed ) && $event->listed ) &&
			                  $api->is_live( $post_id ) &&
			                  tribe_event_show_tickets( $post_id, $event );

			/**
			 * Filters whether the ticket iFrame should be displayed or not.
			 *
			 * By default the iFrame will not be displayed if the event is not listed, is not live or
			 * the event is not set to display tickets at all.
			 * This filters allows ignoring these checks and displaying, or not, the iFrame with other
			 * arbitrary conditions.
			 *
			 * @since 4.5.4
			 *
			 * @param bool   $display_iframe      Whether the iFrame should be displayed or not.
			 * @param int    $post_id             Post ID for this event.
			 * @param object $event               The event object retrieved from the Eventbrite API.
			 * @param int    $num_visible_tickets Number of tickets this event has.
			 */
			$display_iframe = apply_filters( 'tribe_events_eventbrite_iframe_display', $display_iframe, $post_id, $event, $num_visible_tickets );

			if ( $display_iframe ) {
				$html = sprintf(
					'<div class="eventbrite-ticket-embed">
						<iframe id="eventbrite-tickets-%1$s" src="%2$s" height="%3$s" width="100%%" frameborder="0" allowtransparency="true"></iframe>
						<div style="font-family:Helvetica, Arial, sans-serif; font-size:12px;margin:2px 0; width:100%; text-align:left;" >
							<a class="eventbrite-powered-by-eb" style="color: #ADB0B6; text-decoration: none;" target="_blank" rel="noopener" href="http://www.eventbrite.com/">Powered by Eventbrite</a>
						</div>
					</div>',
					$event_id,
					$iframe_url,
					$iframe_height
				);
			}

			/**
			 * Allows Eventbrite iframe HTML to be modified.
			 *
			 * @param string $html
			 * @param string $event_id associated Eventbrite ID
			 * @param int    $post_id
			 */
			return apply_filters( 'tribe_events_eb_iframe_html', $html, $event_id, $post_id );
		}

		/**
		 * after ticket injection
		 * @param  int $post_id
		 * @return string
		 * @author tim@imaginesimplicity.com
		 */
		public static function after_tickets( $post_id ) {
			$html = '';
			return apply_filters( 'tribe_template_factory_debug', $html, 'tribe_events_eventbrite_after_the_tickets' );
		}
	}
}
