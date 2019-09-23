<?php
/**
 * Class Tribe__Events__Tickets__Eventbrite__Assets
 *
 * @since 4.5
 *
 */
class Tribe__Events__Tickets__Eventbrite__Assets {

	/**
	 * Enqueue scripts for admin views
	 *
	 * @since 4.5
	 */
	public function register_admin_assets() {

		// Set up our base list of enqueues.
		$enqueue_array = array(
			array( 'tribe-eventbrite-admin', 'eb-tec-admin.css', array() ),
		);

		tribe_assets(
			tribe( 'eventbrite.main' ),
			$enqueue_array,
			'admin_enqueue_scripts'
		);

		// This is called by tribe_asset_enqueue, but only from the metabox specifically.
		tribe_asset( tribe( 'eventbrite.main' ), 'tribe-eventbrite-metabox', 'metabox.js', array( 'tribe-events-admin' ) );
	}

	/**
	 * Fleshing out TEC's admin JS wp_localize_script data with some additional EB-specific info.
	 *
	 * @since 4.6.1
	 *
	 * @param array $data The data array passed to wp_localize_script on The Events Calendar's events-admin.js script.
	 * @return array
	 */
	public function add_metabox_data_to_admin_js( $data ) {

		if ( ! is_array( $data ) ) {
			return $data;
		}

		$event_id = get_the_ID();

		if ( ! $event_id || Tribe__Events__Main::POSTTYPE !== get_post_type( $event_id ) ) {
			return $data;
		}

		$data['should_send_post_thumbnail_to_eb'] = tribe( 'eventbrite.sync.featured_image' )->should_send_post_thumbnail_to_eb( $event_id )
			? true
			: false;

		return $data;
	}
}
