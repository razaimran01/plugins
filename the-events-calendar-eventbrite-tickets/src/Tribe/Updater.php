<?php


/**
 * Class Tribe__Events__Tickets__Eventbrite__Updater
 *
 * @since 4.5
 *
 */
class Tribe__Events__Tickets__Eventbrite__Updater extends Tribe__Events__Updater {

	protected $version_option = 'eventbrite-schema-version';

	/**
	 * Returns an array of callbacks with version strings as keys.
	 * Any key higher than the version recorded in the DB
	 * and lower than $this->current_version will have its
	 * callback called.
	 *
	 * @since 4.5
	 *
	 * @return array
	 */
	public function get_update_callbacks() {
		return array(
			'4.4' => array( $this, 'migrate_legacy_settings' ),
		);
	}

	/**
	 * Update Eventbrite default category option name
	 *
	 * @since 4.5
	 *
	 */
	public function migrate_legacy_settings() {

		$tec_options = Tribe__Settings_Manager::get_options();
		if ( ! is_array( $tec_options ) ) {
			return;
		}

		foreach ( $tec_options as $key => $value ) {
			if ( 'imported_post_status[eventbrite]' === $key ) {
				$tribe_options[ 'tribe_aggregator_default_eventbrite_post_status' ] = $value;
				unset( $tribe_options[ $key ] );
			}
		}

		Tribe__Settings_Manager::set_options( $tec_options );

	}

	/**
	 * Force upgrade script to run even without an existing version number
	 * The version was not previously stored for Filter Bar
	 *
	 * @since 4.5
	 *
	 * @return bool
	 */
	public function is_new_install() {
		return false;
	}
}
