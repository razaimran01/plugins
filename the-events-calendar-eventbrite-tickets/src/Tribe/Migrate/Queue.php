<?php
class Tribe__Events__Tickets__Eventbrite__Migrate__Queue extends Tribe__Process__Queue {

	/**
	 * Which key used in transient for in which version this event was migrated
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public $key_migrate_version = '_tribe_eventbrite_migrated_version';

	/**
	 * Returns the async process action name.
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public static function action() {
		return 'eventbrite_migrate_events';
	}

	/**
	 * Filter the Update authority for the migration to always be overwrite
	 *
	 * @since 4.5
	 *
	 * @param  mixed  $value
	 * @param  string $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public function filter_update_authority( $value, $key, $default ) {
		$possible_keys = array(
			'tribe_aggregator_default_update_authority',
			'tribe_aggregator_default_eventbrite_update_authority',
		);

		// Return previous value if not the keys we want
		if ( ! in_array( $key, $possible_keys ) ) {
			return $value;
		}

		return 'overwrite';
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over.
	 *
	 * @return mixed
	 */
	protected function task( $event ) {
		$event = get_post( $event );

		// If we don't have the post we remove it from the queue
		if ( ! $event instanceof WP_Post ) {
			return false;
		}

		$data = array(
			'EventRegister' => 'yes',
			'is_migrating'  => true,
		);

		// Fetch the EB ID
		$eventbrite_id = tribe_eb_get_id( $event->ID );

		if ( ! $eventbrite_id ) {
			return false;
		}

		add_filter( 'tribe_get_option', array( $this, 'filter_update_authority' ), 15, 3 );

		$global_id = new Tribe__Utils__Global_ID;
		$global_id->type( 'eventbrite' );
		$url = $global_id->generate( array( 'id' => $eventbrite_id, 'type' => 'event' ) );

		$record_id = get_post_meta( $event->ID, Tribe__Events__Aggregator__Event::$record_key, true );

		if ( ! $record_id ) {
			$record = Tribe__Events__Aggregator__Records::instance()->get_by_origin( 'eventbrite' );
			$meta = array(
				'origin' => 'eventbrite',
				'type' => 'schedule',
				'frequency' => 'daily',
				'source' => $url,
				'callback_bypass' => true,
				'post_status' => $event->post_status,
			);
			$record_post = $record->create( 'schedule', array(), $meta );
			$record->update_meta( 'source_name', $event->post_title );
			$record->set_status( 'schedule' );
			$record->activity()->add( 'event', 'created', $event->ID );
			$record->save_activity();
		} else {
			$record = Tribe__Events__Aggregator__Records::instance()->get_by_post_id( $record_id );
		}

		Tribe__Events__Aggregator__Records::instance()->add_record_to_event( $event->ID, $record->id, 'eventbrite' );

		// Add the current version as when this event was migrated
		update_post_meta( $event->ID, $this->key_migrate_version, Tribe__Events__Tickets__Eventbrite__Main::VERSION );

		$response = Tribe__Events__Aggregator__Tabs__Scheduled::instance()->action_run_import( array( $record->id ) );

		$child = $record->get_last_child_post();

		// Load the queue
		$queue = new Tribe__Events__Aggregator__Record__Queue( $child->ID, 'fetch' );
		$queue->process( 1 );

		$data = get_post_meta( $event->ID, tribe( 'eventbrite.event' )->key_tickets, true );

		$status = get_post_status( $child->ID );

		if ( Tribe__Events__Aggregator__Records::$status->failed === $status ) {
			wp_delete_post( $child->ID, true );
		}

		remove_filter( 'tribe_get_option', array( $this, 'filter_update_authority' ), 15 );

		if ( $data ) {
			return false;
		} else {
			return $event->ID;
		}
	}

	protected function complete() {
		delete_transient( tribe( 'eventbrite.migrate' )->key_is_migrating );
		parent::complete();
	}
}