<?php


/**
 * EventBrite EA Main Sync
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Main {

	/**
	 * Hook to Sync Local event with Eventbrite
	 *
	 * @since 4.5
	 *
	 * @param $event
	 *
	 * @return bool|void
	 */
	public function action_sync_event( $event, $data = array() ) {
		$event = get_post( $event );

		if ( ! is_object( $event ) || ! $event instanceof WP_Post ) {
			return false;
		}

		if ( ! tribe_is_event( $event->ID ) ) {
			return false;
		}

		// Skip events being saved from EA import.
		if ( did_action( 'tribe_aggregator_before_insert_posts' ) ) {
			return false;
		}

		// When data is empty we rely on $_POST
		if ( empty( $data ) ) {
			$data = $_POST;
		}

		// if event not owned by user make changes to show tickets and image sync and then return
		if (  ! empty( $data['eventbrite_import_not_owner'] ) ) {
			// local Eventbrite setting to show tickets
			$show_tickets = ( ! empty( $data['EventShowTickets'] ) ? esc_attr( $data['EventShowTickets'] ) : 'yes' );
			update_post_meta( $event->ID, '_EventShowTickets', $show_tickets );

			// If the event's not owned by the user, disable featured image syncing altogether.
			update_post_meta( $event->ID, tribe( 'eventbrite.sync.featured_image' )->key_meta_flag, 'no' );

			return false;
		}

		// Clean if Register Event is not Yes
		if ( empty( $data['EventRegister'] ) || 'yes' !== $data['EventRegister'] ) {
			tribe( 'eventbrite.sync.utilities' )->clear_details( $event );

			return false;
		}

		$is_migrating  = isset( $data['is_migrating'] ) && tribe_is_truthy( $data['is_migrating'] );
		$eventbrite_id = tribe_eb_get_id( $event->ID );

		if ( isset( $data['EventBriteUsePostThumb'] ) && tribe_is_truthy( $data['EventBriteUsePostThumb'] ) ) {
			update_post_meta( $event->ID, tribe( 'eventbrite.sync.featured_image' )->key_meta_flag, 'yes' );
		} else {
			update_post_meta( $event->ID, tribe( 'eventbrite.sync.featured_image' )->key_meta_flag, 'no' );
		}

		$is_listed = ! empty( $data['EventBritePrivacy'] ) && 'not_listed' === $data['EventBritePrivacy'] ? 0 : 1;

		$args = array(
			'status'       => ( ! empty( $data['EventBriteStatus'] ) ? esc_attr( wp_kses( $data['EventBriteStatus'], array() ) ) : 'draft' ),
			'listed'       => (int) $is_listed,
			'show_tickets' => ( ! empty( $data['EventShowTickets'] ) ? esc_attr( wp_kses( $data['EventShowTickets'], array() ) ) : 'yes' ),
			'is_migrating' => $is_migrating,
		);

		if ( wp_is_post_revision( $event->ID ) ) {
			return tribe( 'eventbrite.main' )->throw_notice( $event, __( 'This Event is a revision and cannot sync to Eventbrite.', 'tribe-eventbrite' ), $data );
		}

		// if not the owner and not migrating then do not try to sync with Eventbrite
		$api      = tribe( 'eventbrite.event' );
		$event_eb = $api->get_event( $event->ID );

		if (
			! $is_migrating &&
			! empty( $eventbrite_id ) &&
			empty( $event_eb->is_owner )
		) {
			return false;
		}

		// with an eventbrite id already in place sync event and not create
		if ( is_numeric( $eventbrite_id ) ) {
			$event_data = tribe( 'eventbrite.sync.event' )->get_event_data( $event, $args );
		} else {
			$event_data = tribe( 'eventbrite.sync.event' )->create_event( $event, $args );
		}

		//if no event data we have an error and display the message and do not try to sync
		if ( empty( $event_data ) ) {
			return false;
		}

		$this->sync_event( $event, $event_data );

		return true;
	}

	/**
	 * Sync Event Data with EA
	 *
	 * @since 4.5
	 *
	 * @param $post
	 * @param $data
	 */
	public function sync_event( $post, $data ) {
		$args = tribe( 'events-aggregator.service' )->get_eventbrite_args();

		$args['secret_key'] = tribe( 'events-aggregator.settings' )->get_eb_security_key()->security_key;
		$args['type']       = 'event';
		$args['data']       = json_encode( $data );

		$response = tribe( 'events-aggregator.service' )->post( 'eventbrite/sync', $args );

		if ( is_wp_error( $response ) ) {
			tribe( 'eventbrite.main' )->throw_notice( $post, $response->get_error_message(), $_POST );

			return;
		}

		if ( empty( $response->status ) ) {
			tribe( 'eventbrite.main' )->throw_notice( $post, __( 'An Unknown Error Occurred and the event may not have synced to Eventbrite.', 'tribe-eventbrite' ), $_POST );

			return;
		}

		if ( 'error' === $response->status ) {
			// If the response provides a detailed message then let's return that specific one.
			$message = ! empty( $response->message ) ? $response->message : __( 'Eventbrite Event Not Owned.', 'tribe-eventbrite' );
			tribe( 'eventbrite.main' )->throw_notice( $post, $message, $_POST );
		}

		if ( 'success' === $response->status ) {
			//tribe( 'eventbrite.main' )->throw_notice( $post, $response->message, $response->data );
			//todo add a success message to display
			$this->save_on_success( $post, $data, $response );
		}
	}

	/**
	 * On Success from EA Update Local Infomation
	 *
	 * @since 4.5
	 *
	 * @param $post
	 * @param $data
	 * @param $response
	 */
	public function save_on_success( $post, $data, $response ) {
		// Save the Response ID
		tribe( 'eventbrite.sync.utilities' )->link_post( $post->ID, $response->data->eventbrite_id );

		// Set the sync event checkbox.
		update_post_meta( $post->ID, '_EventRegister', 'yes' );

		// save event global id if there was none
		if ( empty( $data['global_id'] ) ) {
			tribe( 'eventbrite.sync.utilities' )->save_global_id( $post->ID, $response->data->global_id, 'event' );
		}

		// save venue global id if there was none
		if ( empty( $data['event.venue']['global_id'] ) && ! empty( $response->data->venue ) ) {
			tribe( 'eventbrite.sync.utilities' )->save_global_id( $post->ID, $response->data->venue->global_id, 'venue' );
		}

		// save organizer global id if there was none
		if ( empty( $data['event.organizer']['global_id'] ) && ! empty( $response->data->organizer ) ) {
			tribe( 'eventbrite.sync.utilities' )->save_global_id( $post->ID, $response->data->organizer->global_id, 'organizer' );
		}

		$record_id = get_post_meta( $post->ID, Tribe__Events__Aggregator__Event::$record_key, true );

		if ( ! $record_id ) {
			$record     = Tribe__Events__Aggregator__Records::instance()->get_by_origin( 'eventbrite' );
			$aggregator = tribe( 'events-aggregator.main' );

			$meta = array(
				'origin'          => 'eventbrite',
				'type'            => 'manual',
				'source'          => $response->data->url,
				'callback_bypass' => true,
				'site'            => site_url(),
				'finalized'       => true,
			);

			$record_post = $record->create( 'manual', array(), $meta );

			$record->update_meta( 'source_name', $data['event.name.html'] );
			$record->activity()->add( 'event', 'created', $post->ID );
			$record->save_activity();

			// create the import on the Event Aggregator service
			$meta['start']    = date( Tribe__Date_Utils::DATEONLYFORMAT );
			$meta['callback'] = home_url( '/event-aggregator/insert/?key=' . urlencode( $record->meta['hash'] ) );
			$ea_response      = $aggregator->api( 'import' )->create( $meta );

			if ( is_wp_error( $ea_response ) ) {
				tribe( 'eventbrite.main' )->throw_notice( $post, $ea_response->get_error_message(), $_POST );
				$record->set_status_as_failed();
			} elseif ( empty( $ea_response->status ) || 'error' === $ea_response->status ) {
				tribe( 'eventbrite.main' )->throw_notice( $post, __( 'An Unknown Error Occurred and syncing from Eventbrite is not enabled.', 'tribe-eventbrite' ), $_POST );
				$record->set_status_as_failed();
			} elseif ( 'success' === $ea_response->status ) {
				$record->update_meta( 'import_id', $ea_response->data->import_id );
				$record->set_status_as_success();
			}
		} else {
			$record = Tribe__Events__Aggregator__Records::instance()->get_by_post_id( $record_id );
		}

		Tribe__Events__Aggregator__Records::instance()->add_record_to_event( $post->ID, $record->id, 'eventbrite' );

		// Save the event information from EB into the Local Post meta
		tribe( 'eventbrite.aggregator' )->save_event_meta( $post, $response->data, $record );
	}
}
