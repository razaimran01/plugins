<?php


/**
 * EventBrite EA Sync
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Event {

	/**
	 * Get Event Data and format it for EA
	 *
	 * @since 4.5
	 *
	 * @param WP_Post $post   an WP_Post Object
	 * @param array   $params data to send to EA, usually started by the creation of a new EB event
	 *
	 * @return array|bool event data or false
	 */
	public function get_event_data( $post, $params = array() ) {

		$post = get_post( $post );
		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		if ( wp_is_post_revision( $post->ID ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				tribe( 'eventbrite.main' )->throw_notice( $post, __( 'This Event is a revision and cannot sync to Eventbrite.', 'tribe-eventbrite' ), $_POST );
			}

			return false;
		} else {
			delete_post_meta( $post->ID, Tribe__Events__Main::EVENTSERROROPT );
		}

		$is_migrating = isset( $params['is_migrating'] ) && tribe_is_truthy( $params['is_migrating'] );

		$eventbrite_id = get_post_meta( $post->ID, '_EventBriteId', true );

		// Legacy timezone mode relates to a practice used before core timezone support in The
		// Events Calendar arrived, where the event was stored locally in the site's WP timezone
		// but where the original timezone was still maintained on eventbrite.com
		$legacy_timezone_mode = (bool) get_post_meta( $post->ID, '_EventbriteTZAdjust', true );
		$eventbrite_timezone  = (string) get_post_meta( $post->ID, '_EventbriteTZ', true );

		// As of release/120 (3.12) timezones may be stored per-event by the core plugin
		if ( class_exists( 'Tribe__Events__Timezones' ) ) {
			$timezone = Tribe__Events__Timezones::get_event_timezone_string( $post->ID );

			if ( strpos( strtolower( $timezone ), 'utc' ) === 0 ) {
				$offset = (int) str_replace( array( 'UTC', 'utc' ), '', $timezone );

				// try to get timezone from gmt_offset, respecting daylight savings
				$timezone = timezone_name_from_abbr( null, $offset * 3600, true );

				// if that didn't work, maybe they don't have daylight savings
				if ( false === $timezone ) {
					$timezone = timezone_name_from_abbr( null, $offset * 3600, false );
				}

				// and if THAT didn't work, round the gmt_offset down and then try to get the timezone respecting daylight savings
				if ( false === $timezone ) {
					$timezone = timezone_name_from_abbr( null, (int) $offset * 3600, true );
				}

				// lastly if that didn't work, round the gmt_offset down and maybe that TZ doesn't do daylight savings
				if ( false === $timezone ) {
					$timezone = timezone_name_from_abbr( null, (int) $offset * 3600, false );
				}
			}
		}

		// If all else fails, fall back on the site/local timezone
		if ( empty( $timezone ) ) {
			$timezone = tribe( 'eventbrite.sync.utilities' )->local_timezone();
		}

		/**
		 * Provides an opportunity to modify the event timezone passed to Eventbrite.
		 *
		 * @param string $timezone
		 * @param int    $event_id
		 */
		$timezone = apply_filters( 'tribe_eb_event_timezone', $timezone, $post->ID );
		do_action( 'log', 'Timezone to EB', 'tribe-eventbrite', $timezone );

		// Load the start/end datetimes in UTC if available
		$start = get_post_meta( $post->ID, '_EventStartDateUTC', true );
		$end   = get_post_meta( $post->ID, '_EventEndDateUTC', true );

		// Fallback on the default event times if UTC is not available (and then convert them)
		if ( empty( $start ) ) {
			$start = get_post_meta( $post->ID, '_EventStartDate', true );
			$end   = get_post_meta( $post->ID, '_EventEndDate', true );

			$start = date( tribe( 'eventbrite.sync.utilities' )->date_format, tribe( 'eventbrite.sync.utilities' )->wp_strtotime( $start ) );
			$end   = date( tribe( 'eventbrite.sync.utilities' )->date_format, tribe( 'eventbrite.sync.utilities' )->wp_strtotime( $end ) );
		} // ...and if UTC times *were* available, we still need to apply the correct formatting
		else {
			$start = date( tribe( 'eventbrite.sync.utilities' )->date_format, strtotime( $start ) );
			$end   = date( tribe( 'eventbrite.sync.utilities' )->date_format, strtotime( $end ) );
		}

		$defaults = array(
			'tickets'         => array(),
			'status'          => 'draft',
			'show_tickets'    => 'yes',
			'sync_image'      => true,
		);

		/**
		 * Whether to try and update the event on eventbrite.com to use the same featured image
		 * as set locally (ie, within WordPress).
		 *
		 * @var bool $synch   whether to synchronize
		 * @var int  $post_id post ID of the event being updated
		 */
		$defaults['sync_image'] = apply_filters( 'tribe_eb_push_image', tribe( 'eventbrite.sync.featured_image' )->should_send_post_thumbnail_to_eb( $post->ID ), $post->ID );

		// Parse Defaults with params
		$params = wp_parse_args( $params, $defaults );

		$should_use_wp_image = tribe_is_truthy( $params['sync_image'] );

		// Prepare/Sanitize $params
		$params['status'] = strtolower( $params['status'] );

		if ( ! in_array( $params['show_tickets'], array( 'yes', 'no' ) ) ) {
			$params['show_tickets'] = 'yes';
		}

		// Most meta that is fully on our side updates here
		update_post_meta( $post->ID, '_EventShowTickets', $params['show_tickets'] );

		$global_id = tribe_get_event_meta( $post->ID, '_tribe_aggregator_global_id', true );

		$saved_privacy = get_post_meta( $post->ID, '_EventBritePrivacy', true );
		$is_listed     = ! empty( $saved_privacy ) && 'not_listed' === $saved_privacy ? (int) 0 : (int) 1;

		// If the privacy setting is changing in the wp-admin, honor that change.
		if ( isset( $params['listed'] ) && (int) $params['listed'] !== $is_listed ) {

			if ( 1 === (int) $params['listed'] ) {
				update_post_meta( $post->ID, '_EventBritePrivacy', 'listed' );
				$is_listed = (int) 1;
			} else {
				update_post_meta( $post->ID, '_EventBritePrivacy', 'not_listed' );
				$is_listed = (int) 0;
			}
		}

		$args = array(
			'global_id'              => tribe( 'eventbrite.sync.utilities' )->get_global_id( $post->ID, '_EventBriteId', 'event' ),
			'status'                 => esc_attr( $params['status'] ),
			'event.name.html'        => tribe( 'eventbrite.sync.utilities' )->string_prepare( get_the_title( $post ) ),
			'event.description.html' => apply_filters( 'the_content', $post->post_content ),
			'event.start.utc'        => $start,
			'event.start.timezone'   => $timezone,
			'event.end.utc'          => $end,
			'event.end.timezone'     => $timezone,
			'event.currency'         => 'USD',
			'event.online_event'     => 0,
			'event.listed'           => (int) $is_listed,
			'event.shareable'        => 0,
			'event.invite_only'      => 0,
			'event.show_remaining'   => 1,
			'event.organizer'        => tribe( 'eventbrite.sync.organizer' )->get_organizer_data( $post ),
			'event.venue'            => tribe( 'eventbrite.sync.venue' )->get_venue_data( $post ),
		);

		// Sync Image
		if ( ! $is_migrating && $should_use_wp_image ) {
			$args['event.logo'] = tribe( 'eventbrite.sync.utilities' )->sync_image( $post );

		// Never send logo if we don't have it, Evenbrite API freaks out
		} elseif ( isset( $args['event.logo'] ) ) {
			unset( $args['event.logo'] );
		}

		// Sync Ticket, this will happen on initial event creation only
		if ( ! empty( $params['tickets'] ) ) {
			$args['event.tickets'][] = tribe( 'eventbrite.sync.tickets' )->sync_ticket( $post->ID, $params['tickets'] );
		}

		$mode = ( ! empty( $eventbrite_id ) ? 'update' : 'create' );
		$eventbrite_data = null;

		if ( 'update' === $mode ) {
			$eventbrite_data = get_post_meta( $post->ID, tribe( 'eventbrite.event' )->key_tickets, true );

			// When updating pass the correct EB currency if available
			if ( ! empty( $eventbrite_data->currency ) ) {
				$args['event.currency'] = esc_attr( $eventbrite_data->currency );
			}
		}

		if ( 'update' === $mode && $legacy_timezone_mode ) {
			$args['event.start.timezone'] = $eventbrite_timezone;
			$args['event.end.timezone']   = $eventbrite_timezone;
		}

		$args = apply_filters( 'tribe_eb_api_sync_event', $args, $mode, $eventbrite_id, $post, $params );

		if ( empty( $args['event.name.html'] ) ) {
			tribe( 'eventbrite.main' )->throw_notice( $post, __( 'This Event requires a Title to sync to Eventbrite.', 'tribe-eventbrite' ), $_POST );

			return false;
		}

		if ( ! isset( $args['event.venue'] ) || false === $args['event.venue'] ) {
			if ( 'create' === $mode ) {

				/**
				 * Whether the Venue data is required for events to be posted to Eventbrite or not.
				 *
				 * @since 4.5.5
				 *
				 * @param bool    $venue_is_required If set to `true` then the event will not be synced
				 *                                   to Eventbrite if the Venue data is missing; default
				 *                                   `false`.
				 * @param WP_Post $post              The post object to sync.
				 * @param array   $array             An array of arguments that will be sent to Eventbrite.
				 */
				$venue_is_required = apply_filters( 'tribe_events_eb_venue_is_required', false, $post, $args );

				if ( $venue_is_required ) {
					tribe( 'eventbrite.main' )->throw_notice( $post, __( 'Eventbrite requires a Venue and it must have a valid Address.', 'tribe-eventbrite' ), $_POST );

					return false;
				}
			}
			unset( $args['event.venue'] );
		}

		if ( ! isset( $args['event.organizer'] ) || false === $args['event.organizer'] ) {
			if ( 'create' === $mode ) {
				tribe( 'eventbrite.main' )->throw_notice( $post, __( 'This Event Requires a Organizer to sync to Eventbrite', 'tribe-eventbrite' ), $_POST );

				return false;
			}
			unset( $args['event.organizer'] );
		}

		return $args;
	}

	/**
	 * Setup Data to Create a New Event with Eventbrite
	 *
	 * @since 4.5
	 *
	 * @param WP_Post $post   an WP_Post Object
	 * @param array   $params data to send to EA, usually started by the creation of a new EB event
	 *
	 * @return array|bool event data or false
	 */
	public function create_event( $event, $args = array() ) {
		$evenbrite_id = get_post_meta( $event->ID, '_EventBriteId', true );

		// Create New Event with EA
		$event_title = get_the_title( $event );
		if ( empty( $event_title ) ) {
			tribe( 'eventbrite.main' )->throw_notice( $event, __( 'An Event Title is required', 'tribe-eventbrite' ), $_POST );

			return false;
		}

		$venue = get_post_meta( $event->ID, '_EventVenueID', true );
		if ( is_numeric( $venue ) ) {
			$venue = get_post( $venue );
		}

		if ( ! $venue instanceof WP_Post || ! tribe_is_venue( $venue->ID ) ) {

			/**
			 * Whether the Venue data is required for events to be posted to Eventbrite or not.
			 *
			 * @since 4.5.5
			 *
			 * @see Tribe__Events__Tickets__Eventbrite__Sync__Venue::get_venue_data() for filter documentation.
			 */
			$venue_is_required = apply_filters( 'tribe_events_eb_venue_is_required', false, $event, $args );

			if ( $venue_is_required ) {
				tribe( 'eventbrite.main' )->throw_notice( $event, __( 'The venue is missing', 'tribe-eventbrite' ), $_POST );

				return false;
			}
		} else {
			$venue->metas = array(
				'title'   => array(
					'value'   => get_the_title( $venue->ID ),
					'message' => __( 'The Venue is missing the Title', 'tribe-eventbrite' ),
				),
				'address' => array(
					'value'   => get_post_meta( $venue->ID, '_VenueAddress', true ),
					'message' => __( 'No Address for this Venue', 'tribe-eventbrite' ),
				),
				'city'    => array(
					'value'   => get_post_meta( $venue->ID, '_VenueCity', true ),
					'message' => __( 'This Venue is missing the City', 'tribe-eventbrite' ),
				),
			);

			$throw_notice = false;
			foreach ( $venue->metas as $name => $meta ) {
				if ( empty( $meta['value'] ) ) {

					/**
					 * Whether a specific Venue data piece is required for events to be posted to Eventbrite or not.
					 *
					 * @since 4.5.5
					 *
					 * @param bool    $venue_data_is_required Whether this piece of Venue data is required to sync the event
					 *                                        to Eventbrite or not; defaults to `false`.
					 * @param WP_Post $event                  The event that should be synced to Eventbrite.
					 * @param array   $params                 Data to send to EA, usually started by the creation of a new EB event
					 *
					 * @see   Tribe__Events__Tickets__Eventbrite__Sync__Venue::get_venue_data() for filter documentation.
					 */
					$venue_data_is_required = apply_filters( "tribe_events_eb_venue_{$name}_is_required", false, $event, $args );

					if ( $venue_data_is_required ) {
						$throw_notice = true;
						tribe( 'eventbrite.main' )->throw_notice( $event, $meta['message'], $_POST );
					}
				}
			}

			if ( $throw_notice ) {
				return false;
			}
		}

		$organizer = get_post_meta( $event->ID, '_EventOrganizerID', true );
		if ( is_numeric( $organizer ) ) {
			$organizer = get_post( $organizer );
		}

		if ( ! $organizer instanceof WP_Post || ! tribe_is_organizer( $organizer->ID ) ) {
			tribe( 'eventbrite.main' )->throw_notice( $event, __( 'An organizer is required', 'tribe-eventbrite' ), $_POST );

			return false;
		}

		// make sure all required fields are present for a ticket on creation
		$required_fields = array(
			'EventBriteTicketName'      => __( 'Ticket Name', 'tribe-eventbrite' ),
			'EventBriteTicketStartDate' => __( 'Date to Start Ticket Sales', 'tribe-eventbrite' ),
			'EventBriteTicketEndDate'   => __( 'Date to End Ticket Sales', 'tribe-eventbrite' ),
			'EventBriteIsDonation'      => __( 'Ticket Type', 'tribe-eventbrite' ),
			'EventBriteEventCost'       => __( 'Ticket Cost', 'tribe-eventbrite' ),
			'EventBriteTicketQuantity'  => __( 'Ticket Quantity', 'tribe-eventbrite' ),
			'EventBriteIncludeFee'      => __( 'Ticket - Include Fee in Price', 'tribe-eventbrite' ),
		);

		$missing_fields = array();

		foreach ( $required_fields as $key => $label ) {
			if ( ! isset( $_POST[ $key ] ) || '' === $_POST[ $key ] || is_null( $_POST[ $key ] ) ) {
				$missing_fields[ $key ] = $label;
			}
		}

		// if all fields are missing, assume the fields weren't meant to be filled out
		if ( count( $missing_fields ) != count( $required_fields ) ) {
			// if ticket type is set to Donation or Free, allow cost to be set to null
			if ( isset( $_POST['EventBriteIsDonation'] ) && 0 != $_POST['EventBriteIsDonation'] ) {
				if ( isset( $missing_fields['EventBriteEventCost'] ) ) {
					unset( $missing_fields['EventBriteEventCost'] );
				}
			} elseif ( isset( $_POST['EventBriteEventCost'] ) && ! tribe( 'tec.cost-utils' )->is_valid_cost( $_POST['EventBriteEventCost'], false ) ) {
				$missing_fields['EventBriteEventCost'] = __( 'Ticket Cost (must be numeric)', 'tribe-eventbrite' );
			}

			// if ticket type is set to free, fee inclusion to be set to null
			if ( isset( $_POST['EventBriteIsDonation'] ) && 2 === $_POST['EventBriteIsDonation'] ) {
				if ( isset( $missing_fields['EventBriteIncludeFee'] ) ) {
					unset( $missing_fields['EventBriteIncludeFee'] );
				}
			}

			if ( ! empty( $missing_fields ) ) {
				$html = '<ul>';
				foreach ( $missing_fields as $key => $message ) {
					$html .= '<li>' . esc_html( $message ) . '</li>';
				}
				$html    .= '</ul>';
				$message = sprintf( __( 'Missing Fields: %s', 'tribe-eventbrite' ), $html );

				tribe( 'eventbrite.main' )->throw_notice( $event, $message, $_POST );

				return false;
			}
		}

		// check the dates of the ticket
		if ( isset( $_POST['EventBriteTicketStartDate'] ) ) {
			$date_errors = array();

			// Get the event datetime data
			$event_timezone = class_exists( 'Tribe__Events__Timezones' ) ? Tribe__Events__Timezones::get_event_timezone_string( $event->ID ) : '';

			// If we have an event-specific timezone we can also pull the UTC time directly
			$event_end_date = $event_timezone ? strtotime( get_post_meta( $event->ID, '_EventEndDateUTC', true ) ) : tribe( 'eventbrite.sync.utilities' )->wp_strtotime( get_post_meta( $event->ID, '_EventEndDate', true ) );

			$datepicker_format = Tribe__Date_Utils::datepicker_formats( tribe_get_option( 'datepickerFormat' ) );

			// Build Start Date
			$ticket_start = Tribe__Date_Utils::datetime_from_format( $datepicker_format, $_POST['EventBriteTicketStartDate'] );
			$ticket_start .= ' ' . $_POST['EventBriteTicketStartHours'] . ':' . $_POST['EventBriteTicketStartMinutes'];
			$ticket_start .= ( isset( $_POST['EventBriteTicketStartMeridian'] ) ) ? $_POST['EventBriteTicketStartMeridian'] : null;

			// Escaping ticket_start
			$ticket_start = esc_attr( wp_kses( $ticket_start, array() ) );

			// Apply timezone
			if ( $event_timezone ) {
				$ticket_start           = Tribe__Events__Timezones::to_utc( $ticket_start, $event_timezone );
				$ticket_start_timestamp = strtotime( $ticket_start );
			} else {
				$ticket_start_timestamp = tribe( 'eventbrite.sync.utilities' )->wp_strtotime( $ticket_start );
			}

			// Build End Date
			$ticket_end = Tribe__Date_Utils::datetime_from_format( $datepicker_format, $_POST['EventBriteTicketEndDate'] );
			$ticket_end .= ' ' . $_POST['EventBriteTicketEndHours'] . ':' . $_POST['EventBriteTicketEndMinutes'];
			$ticket_end .= ( isset( $_POST['EventBriteTicketEndMeridian'] ) ) ? $_POST['EventBriteTicketEndMeridian'] : null;

			// Escaping ticket_end
			$ticket_end = esc_attr( wp_kses( $ticket_end, array() ) );

			// Apply timezone
			if ( class_exists( 'Tribe__Events__Timezones' ) ) {
				$ticket_end           = Tribe__Events__Timezones::to_utc( $ticket_end, $event_timezone );
				$ticket_end_timestamp = strtotime( $ticket_end );
			} else {
				$ticket_end_timestamp = tribe( 'eventbrite.sync.utilities' )->wp_strtotime( $ticket_end );
			}

			// verify the event start and end date is not older then 1 day in the past giving some wiggle room for timezones
			$start_of_yesterday = strtotime( 'midnight', current_time( 'U' ) ) - 86400;
			if ( $ticket_start_timestamp < $start_of_yesterday ) {
				$date_errors[] = __( 'Ticket sales start date cannot be before today', 'tribe-eventbrite' );
			}

			if ( $ticket_end_timestamp < $start_of_yesterday ) {
				$date_errors[] = __( 'Ticket sales end date cannot be before today', 'tribe-eventbrite' );
			}

			if ( $ticket_end_timestamp > $event_end_date ) {
				$date_errors[] = __( 'Ticket sales end date cannot be after the event ends', 'tribe-eventbrite' );
			}

			if ( $ticket_start_timestamp > $ticket_end_timestamp ) {
				$date_errors[] = __( 'Ticket sales start date cannot be after ticket sales end date', 'tribe-eventbrite' );
			}

			if ( $ticket_start_timestamp === $ticket_end_timestamp ) {
				$date_errors[] = __( 'Ticket sales start and end datetime must not be the same', 'tribe-eventbrite' );
			}

			if ( ! empty( $date_errors ) ) {
				$html = '<ul>';
				foreach ( $date_errors as $key => $message ) {
					$html .= '<li>' . esc_html( $message ) . '</li>';
				}
				$html    .= '</ul>';
				$message = sprintf( __( 'The dates you have chosen for your ticket sales are inconsistent: %s', 'tribe-eventbrite' ), $html );

				tribe( 'eventbrite.main' )->throw_notice( $event, $message, $_POST );

				return false;
			}
		}

		// Capture and store the privacy setting early.
		if ( isset( $_POST['EventBritePrivacy'] ) && ! empty( $_POST['EventBritePrivacy'] ) ) {
			update_post_meta( $event->ID, '_EventBritePrivacy', $_POST['EventBritePrivacy'] );
		}

		if ( ! $evenbrite_id ) {
			$cost = tribe( 'tec.cost-utils' )->parse_cost_range( '00.00 ' . $_POST['EventBriteEventCost'], 2 );
			$cost = array_keys( $cost );
			$cost = end( $cost );

			$args['tickets'] = array(
				'name'        => $_POST['EventBriteTicketName'],
				'description' => $_POST['EventBriteTicketDescription'],
				'start'       => $ticket_start_timestamp,
				'end'         => $ticket_end_timestamp,
				'type'        => $_POST['EventBriteIsDonation'],
				'cost'        => $cost,
				'qty'         => $_POST['EventBriteTicketQuantity'],
				'include_fee' => $_POST['EventBriteIncludeFee'],
			);
		}

		return $this->get_event_data( $event, $args );
	}

	/**
	 * Sets the WP event's privacy meta, which will be synced with the Eventbrite.com version of the event.
	 *
	 * @since 4.5.3
	 *
	 * @param int $wp_event_it ID of the WP post.
	 * @param object $eventbrite_event_obj The Eventbrite API response being checked.
	 * @return bool True if postmeta updated successfully.
	 */
	public function set_event_privacy_meta( $wp_event_id, $eventbrite_event_obj ) {
		$privacy = 'listed';

		if ( ! isset( $eventbrite_event_obj->listed ) || empty( $eventbrite_event_obj->listed ) ) {
			$privacy = 'not_listed';
		}

		if ( isset( $eventbrite_event_obj->listed ) && (int) 1 !== $eventbrite_event_obj->listed ) {
			$privacy = 'not_listed';
		}

		return update_post_meta( $wp_event_id, '_EventBritePrivacy', $privacy );
	}
}
