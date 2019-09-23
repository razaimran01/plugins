<?php


/**
 * EventBrite EA Venue Sync
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Venue {

	/**
	 * Get Venue Data for Event and format it for EA
	 *
	 * @since 4.5
	 *
	 * @param WP_Post $post an WP_Post Object
	 *
	 * @return array|bool venue data or false
	 */
	public function get_venue_data( $post ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		if ( tribe_is_venue( $post->ID ) ) {
			$venue = $post;
		} elseif ( tribe_is_event( $post->ID ) ) {
			$venue_id = get_post_meta( $post->ID, '_EventVenueID', true );
			if ( $venue_id ) {
				$venue = get_post( $venue_id );
			}
		}

		if ( ! isset( $venue ) ) {
			return false;
		}

		$args = array(
			'global_id'                 => tribe( 'eventbrite.sync.utilities' )->get_global_id( $venue->ID, '_VenueEventBriteID', 'venue' ),
			'venue.name'                => tribe( 'eventbrite.sync.utilities' )->string_prepare( get_the_title( $venue->ID ) ),
			'venue.address.address_1'   => get_post_meta( $venue->ID, '_VenueAddress', true ),
			'venue.address.city'        => get_post_meta( $venue->ID, '_VenueCity', true ),
			'venue.address.region'      => tribe_get_region( $venue->ID ),
			'venue.address.postal_code' => get_post_meta( $venue->ID, '_VenueZip', true ),
			'venue.address.latitude'    => get_post_meta( $venue->ID, '_VenueLat', true ),
			'venue.address.longitude'   => get_post_meta( $venue->ID, '_VenueLng', true ),
		);

		$country = $this->get_country_code( $venue->ID );


		/**
		 * Filter if a region should be sync with Eventbrite
		 *
		 * Note: The Eventbrite API can sometimes reject venues outside the U.S.
		 * if they include a region, therefore, the region is disabled by default.
		 *
		 * @since 4.4.7
		 *
		 * @param boolean $include_region
		 */
		$include_region = apply_filters( 'tribe_events_eventbrite_include_region', false );

		if ( $country ) {
			$args['venue.address.country'] = $country;

			if ( 'US' !== $args['venue.address.country'] && ! $include_region ) {
				unset( $args['venue.address.region'] );
			}
		}

		if ( empty( $args['venue.name'] ) || ! $args['venue.name'] ) {
			$args['venue.name'] = apply_filters( 'tribe-eventbrite-no_venue_name', __( 'Unnamed Venue', 'tribe-eventbrite' ) );
		}

		if ( empty( $args['venue.address.latitude'] ) || empty( $args['venue.address.longitude'] ) ) {
			$address = array();

			if ( ! empty( $args['venue.address.address_1'] ) ) {
				$address[] = $args['venue.address.address_1'];
			}

			if ( ! empty( $args['venue.address.postal_code'] ) ) {
				$address[] = $args['venue.address.postal_code'];
			}

			if ( ! empty( $args['venue.address.city'] ) ) {
				$address[] = $args['venue.address.city'];
			}

			if ( ! empty( $args['venue.address.region'] ) ) {
				$address[] = $args['venue.address.region'];
			}

			if ( ! empty( $args['venue.address.country'] ) ) {
				$address[] = $args['venue.address.country'];
			}

			$coordinates = Tribe__Utils__Coordinates_Provider::instance()->provide_coordinates_for_address( array_filter( $address ) );

			if ( empty( $coordinates ) ) {

				/**
				 * Filters whether Venue latitude and longitude should be required for a Venue to be considered valid or not.
				 *
				 * @since 4.5.5
				 *
				 * @param bool    $lat_and_long_are_required Whether a Venue should have latitude and longitude coordinates
				 *                                           to be considered valid or not; defaults to `false`.
				 * @param WP_Post $post                      The current event post object that's beins synced.
				 * @param array   $address                   An array of the Venue address components.
				 */
				$lat_and_long_are_required = apply_filters( 'tribe_events_eb_venue_lat_and_long_are_required', false, $post, $address );

				if ( $lat_and_long_are_required ) {
					$message_error = esc_attr__( 'There was a problem determining the coordinates for your event, try using a different venue address.', 'events-eventbrite' );
					tribe( 'eventbrite.main' )->throw_notice( $venue, $message_error, $_POST );
					return false;
				}
			} else {
				// Fill in latitude and longitude details if available.
				$args['venue.address.latitude']  = $coordinates['lat'];
				$args['venue.address.longitude'] = $coordinates['lng'];
			}

		}

		return $args;

	}

	/**
	 * Get a country code from an event id
	 *
	 * @since 4.5
	 *
	 * @param  int $event_id the event id
	 *
	 * @return string $code the country code
	 */
	public function get_country_code( $event_id = null ) {
		$country = tribe_get_country( $event_id );

		if ( empty( $country ) ) {
			return;
		}

		$countries = Tribe__View_Helpers::constructCountries();
		$code      = array_search( $country, $countries );

		return $code;
	}

	/**
	 * get a country code from an event id
	 *
	 * @author bordoni
	 *
	 * @param  string $code the country code
	 *
	 * @return string $name the country name based on code
	 */
	public function get_country_name( $code ) {
		$countries = Tribe__View_Helpers::constructCountries();

		if ( ! isset( $countries[ $code ] ) ) {
			return false;
		}

		return $countries[ $code ];
	}

}
