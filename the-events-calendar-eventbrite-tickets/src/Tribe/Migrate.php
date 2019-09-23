<?php

/**
 * Class Tribe__Events__Tickets__Eventbrite__Migrate
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Migrate {
	/**
	 * Migration flag transient key in the Database
	 *
	 * @since 4.5
	 *
	 * @var string
	 */
	public $key_is_migrating = '_tribe_eventbrite_is_migrating';

	/**
	 * Hook the correct Actions and Filter
	 *
	 * @since 4.5
	 *
	 * @return void
	 */
	public function hook() {
		tribe_notice(
			'eventbrite-needs-migration',
			array( $this, 'notice_needs_migration' ),
			array( 'type' => 'warning', 'dismiss' => 0 ),
			array( $this, 'needs_migration' )
		);

		tribe_notice(
			'eventbrite-is-migrating',
			array( $this, 'notice_is_migrating' ),
			array( 'type' => 'warning', 'dismiss' => 0 ),
			array( $this, 'is_migrating' )
		);

		tribe_asset(
			tribe( 'eventbrite.main' ),
			'tribe-eventbrite-migrate',
			'migrate.js',
			array( 'jquery' ),
			'admin_enqueue_scripts',
			array(
				'conditionals' => array( $this, 'needs_migration' ),
			)
		);

		add_action( 'wp_ajax_tribe_eventbrite_migrate_events', array( $this, 'ajax_maybe_dispatch' ) );
		add_filter( 'tribe_process_queues', array( $this, 'add_process_queue' ) );
	}

	/**
	 * Hook the correct Actions and Filter
	 *
	 * @since 4.5
	 *
	 * @param array $queue Queues currently added
	 *
	 * @return array
	 */
	public function add_process_queue( $queues = array() ) {
		$queues[] = 'Tribe__Events__Tickets__Eventbrite__Migrate__Queue';

		return $queues;
	}

	/**
	 * Notice HTML for when we are actually migrating things
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function notice_is_migrating() {
		$html = array();
		$html[] = '<p>';
		$html[] = esc_html__( 'Currently migrating Eventbrite events to ensure proper data is displayed when editing.', 'tribe-eventbrite' );
		$html[] = '</p>';

		return implode( "\n", $html );
	}

	/**
	 * Notice HTML for when we need to migrate
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function notice_needs_migration() {
		$html = array();
		$html[] = '<p>';
		$html[] = esc_html__( 'To ensure all Eventbrite Imports work properly you will need to trigger the Events Migration.', 'tribe-eventbrite' );
		$html[] = get_submit_button( __( 'Migrate Events', 'tribe-eventbrite' ), 'primary', 'eventbrite-migrate-events', false );
		$html[] = '<span class="spinner" style="float:none; display:inline-block; margin-top: -2px;"></span>';
		$html[] = '</p>';

		return implode( "\n", $html );
	}

	/**
	 * Dispatch the Queue for migration printing out the AJAX json responses
	 *
	 * @since 4.5
	 *
	 * @return string
	 */
	public function ajax_maybe_dispatch() {
		$trigger_migration = tribe_get_request_var( 'eventbrite-migrate-events', false );

		if ( ! $trigger_migration ) {
			return wp_send_json_error();
		}

		if ( ! $this->needs_migration() ) {
			return wp_send_json_error();
		}

		$queue = new Tribe__Events__Tickets__Eventbrite__Migrate__Queue;
		$events = $this->get_events();

		foreach ( $events as $event ) {
			$queue->push_to_queue( $event );
		}

		// Save as Q will throw error if you try to get ID before
		$queue->save();

		// Transient with the Migration key, transients for better QA interactions
		set_transient( $this->key_is_migrating, $queue->get_id(), 6 * HOUR_IN_SECONDS );

		$queue->dispatch();

		return wp_send_json_success( $events );
	}

	/**
	 * Check if we are migrating Eventbrite events currently
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function is_migrating() {
		$value = get_transient( $this->key_is_migrating );
		return ! empty( $value );
	}

	/**
	 * Check if we need to migrate events on Eventbrite
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function needs_migration() {
		// If no EB security token then we are not connected to EA and cannot migrate yet
		$eb_token = tribe_get_option( 'eb_security_key' );
		if ( empty( $eb_token ) ) {
			return false;
		}

		return ! $this->is_migrating() && $this->has_events();
	}

	/**
	 * Check if we have events to be migrated
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function has_events() {
		$events = $this->get_events();
		return count( $events ) > 0;
	}

	/**
	 * Gets the Events that needs Migration
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_events() {
		$args = array(
			'post_type' => Tribe__Events__Main::POSTTYPE,
			'posts_per_page' => -1,
			'post_status' => 'any',
			'fields' => 'ids',
			'suppress_filters' => true,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => '_EventBriteId',
					'compare' => 'EXISTS',
				),
				array(
					'key' => Tribe__Events__Aggregator__Event::$record_key,
					'compare' => 'NOT EXISTS',
				),
			),
		);
		$events = new WP_Query( $args );

		return $events->posts;
	}
}
