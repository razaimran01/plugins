<?php
/**
 * Class Tribe__Events__Tickets__Eventbrite__Service_Provider
 *
 * Provides the Eventbrite Tickets service.
 *
 * This class should handle implementation binding, builder functions and hooking for any first-level hook and be
 * devoid of business logic.
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Service_Provider extends tad_DI52_ServiceProvider {
	/**
	 * Binds and sets up implementations.
	 *
	 * @since 4.5
	 */
	public function register() {

		$this->container->singleton( 'eventbrite.assets', new Tribe__Events__Tickets__Eventbrite__Assets() );
		$this->container->singleton( 'eventbrite.event', 'Tribe__Events__Tickets__Eventbrite__Event' );
		$this->container->singleton( 'eventbrite.aggregator', 'Tribe__Events__Tickets__Eventbrite__Aggregator' );
		$this->container->singleton( 'eventbrite.sync.main', 'Tribe__Events__Tickets__Eventbrite__Sync__Main' );
		$this->container->singleton( 'eventbrite.sync.utilities', 'Tribe__Events__Tickets__Eventbrite__Sync__Utilities' );
		$this->container->singleton( 'eventbrite.sync.event', 'Tribe__Events__Tickets__Eventbrite__Sync__Event' );
		$this->container->singleton( 'eventbrite.sync.venue', 'Tribe__Events__Tickets__Eventbrite__Sync__Venue' );
		$this->container->singleton( 'eventbrite.sync.organizer', 'Tribe__Events__Tickets__Eventbrite__Sync__Organizer' );
		$this->container->singleton( 'eventbrite.sync.tickets', 'Tribe__Events__Tickets__Eventbrite__Sync__Tickets' );
		$this->container->singleton( 'eventbrite.sync.featured_image', 'Tribe__Events__Tickets__Eventbrite__Sync__Featured_Image' );
		$this->container->singleton( 'eventbrite.migrate', 'Tribe__Events__Tickets__Eventbrite__Migrate', array( 'hook' ) );
		$this->container->singleton( 'eventbrite.migrate.queue', 'Tribe__Events__Tickets__Eventbrite__Migrate__Queue' );

		$this->hook();
		$this->deprecated();
	}

	/**
	 * Any hooking for any class needs happen here.
	 *
	 * In place of delegating the hooking responsibility to the single classes they are all hooked here.
	 *
	 * @since 4.5
	 */
	protected function hook() {
		tribe( 'eventbrite.assets' )->register_admin_assets();
		tribe( 'eventbrite.migrate' );

		add_action( 'tribe_plugins_loaded', tribe_callback( 'eventbrite.aggregator', 'hook' ) );
		add_action( 'tribe_events_update_meta', tribe_callback( 'eventbrite.sync.main', 'action_sync_event' ), 20 );

		add_filter( 'tribe_events_admin_js_ajax_url_data', tribe_callback( 'eventbrite.assets', 'add_metabox_data_to_admin_js' ) );
		add_filter( 'admin_post_thumbnail_html', tribe_callback( 'eventbrite.sync.featured_image', 'add_featured_image_control' ), 10, 3 );
	}

	/**
	 * Binds and sets up implementations at boot time.
	 *
	 * @since 4.5
	 */
	public function boot() {
		// no ops
	}

	/**
	 * Load Deprecated Classes
	 *
	 * @since 4.5
	 */
	public function deprecated() {

		// register compatibility class to prevent fatal errors
		$this->container->singleton( 'eventbrite.api', 'Tribe__Events__Tickets__Compatibility_Eventbrite__API' );
	}
}
