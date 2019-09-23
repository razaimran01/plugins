<?php


/**
 * EventBrite EA Sync Utilities
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Utilities {

	/**
	 * Eventbrite Statuses
	 *
	 * @var array
	 */
	public $live_statuses = array( 'live', 'started', 'ended', 'completed' );

	/**
	 * Eventbrite Date Format
	 *
	 * @var string
	 *
	 */
	public $date_format = 'Y-m-d\TH:i:s\Z';


	/**
	 * Get the live statues for Eventbrite
	 *
	 * @since 4.5
	 *
	 * @return array         return an array of statues
	 */
	public function get_live_statues() {

		/**
		 * Let the user customize what statuses are considered "live".
		 *
		 * "Started"- and "Ended"-status events should remain "published" and visible, and so are considered "Live" even
		 * though not literally of the "Live" status. You can change that behavior by removing them via this filter.
		 *
		 * @since 4.5
		 *
		 * @param array $statuses The Eventbrite API statuses to treat as "Live".
		 */
		return apply_filters( 'tribe_events_eventbrite_live_statuses', $this->live_statuses );

	}

	/**
	 * Replace the wp_texturize stuff
	 *
	 * @since 4.5
	 *
	 * @param  string $string string to be untexturized
	 *
	 * @return string         Returns the untexturized
	 */
	public function string_prepare( $string, $html = array() ) {
		return wp_specialchars_decode( str_replace( array(
			_x( '&#8220;', 'opening curly double quote' ),
			_x( '&#8221;', 'closing curly double quote' ),
			_x( '&#8217;', 'apostrophe' ),
			_x( '&#8242;', 'prime' ),
			_x( '&#8243;', 'double prime' ),
			_x( '&#8216;', 'opening curly single quote' ),
			_x( '&#8217;', 'closing curly single quote' ),
			_x( '&#8211;', 'en dash' ),
			_x( '&#8212;', 'em dash' ),
		), array(
			'"',
			'"',
			'\'',
			'\'',
			'"',
			'\'',
			'\'',
			'-',
			'-',
		), $string ) );
	}

	/**
	 * Using a Post Object/ID to download and Sync the Event Image from Eventbrite to WordPress
	 *
	 * @since 4.5
	 *
	 * @param  int/WP_Post  $post      Post Object/ID
	 * @param  boolean $overwrite Overwrites the current featured image if there is one in place
	 *
	 * @return array|bool
	 */
	public function sync_image( $post, $overwrite = false ) {
		$post = get_post( $post );
		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );

		if ( ! $thumbnail_id ) {
			return false;
		}

		if ( tribe( 'eventbrite.sync.featured_image' )->should_send_post_thumbnail_to_eb( $post->ID ) ) {

			$global_id = tribe_get_event_meta( $thumbnail_id, '_tribe_aggregator_global_id', true );

			$args = array(
				'global_id' => empty( $global_id ) ? null : esc_attr( $global_id ),
			);

			if ( ! empty( $global_id ) ) {
				return $args;
			}

			// We will need Mimetype and Filename of the Attachment
			$attachment = get_attached_file( $thumbnail_id );
			$file       = wp_check_filetype_and_ext( $attachment, basename( $attachment ) );
			$args       = array(
				'global_id' => empty( $global_id ) ? null : esc_attr( $global_id ),
				'type'      => empty( $file['type'] ) ? null : esc_attr( $file['type'] ),
				'name'      => sanitize_file_name( basename( $attachment ) ),
				'data'      => base64_encode( file_get_contents( $attachment ) ),
			);

			return $args;
		}

		return false;
	}

	/**
	 * Converts a locally-formatted date to a unix timestamp. This is a drop-in
	 * replacement for `strtotime()`, except that where strtotime assumes GMT, this
	 * assumes local time (as described below). If a timezone is specified, this
	 * function defers to strtotime().
	 *
	 * If there is a timezone_string available, the date is assumed to be in that
	 * timezone, otherwise it simply subtracts the value of the 'gmt_offset'
	 * option.
	 *
	 * @since 4.5
	 *
	 * @see   strtotime()
	 * @uses  get_option() to retrieve the value of 'gmt_offset'.
	 *
	 * @param string $string A date/time string. See `strtotime` for valid formats.
	 *
	 * @return int UNIX timestamp.
	 */
	public static function wp_strtotime( $string ) {
		// If there's a timezone specified, we shouldn't convert it
		try {
			$test_date = new DateTime( $string );
			if ( 'UTC' != $test_date->getTimezone()->getName() ) {
				return strtotime( $string );
			}
		} catch ( Exception $e ) {
			return strtotime( $string );
		}

		$tz = get_option( 'timezone_string' );
		if ( ! empty( $tz ) ) {
			$date = date_create( $string, new DateTimeZone( $tz ) );
			if ( ! $date ) {
				return strtotime( $string );
			}
			$date->setTimezone( new DateTimeZone( 'UTC' ) );

			return $date->format( 'U' );
		} else {
			$offset    = (float) get_option( 'gmt_offset' );
			$seconds   = intval( $offset * HOUR_IN_SECONDS );
			$timestamp = strtotime( $string ) - $seconds;

			return $timestamp;
		}
	}

	/**
	 * Tries to return the local timezone as a timezone string format, even if it is
	 * configured as an offset.
	 *
	 * @since 4.5
	 *
	 * @return mixed|string|void
	 */
	public function local_timezone() {
		if ( '' != get_option( 'timezone_string', '' ) ) {
			$timezone = get_option( 'timezone_string' );
		} elseif ( false !== get_option( 'gmt_offset' ) ) {
			$current_offset = get_option( 'gmt_offset' );

			// try to get timezone from gmt_offset, respecting daylight savings
			$timezone = timezone_name_from_abbr( null, $current_offset * 3600, true );

			// if that didn't work, maybe they don't have daylight savings
			if ( false === $timezone ) {
				$timezone = timezone_name_from_abbr( null, $current_offset * 3600, false );
			}

			// and if THAT didn't work, round the gmt_offset down and then try to get the timezone respecting daylight savings
			if ( false === $timezone ) {
				$timezone = timezone_name_from_abbr( null, (int) $current_offset * 3600, true );
			}

			// lastly if that didn't work, round the gmt_offset down and maybe that TZ doesn't do daylight savings
			if ( false === $timezone ) {
				$timezone = timezone_name_from_abbr( null, (int) $current_offset * 3600, false );
			}

			// if all else fails, use UTC
			if ( false === $timezone ) {
				$timezone = 'UTC';
			}
		} else {
			$timezone = 'UTC';
		}

		return $timezone;
	}

	/**
	 * Link Post with Eventbrite
	 *
	 * @since 4.5
	 *
	 * @param $post_id
	 * @param $eventbrite_id
	 *
	 * @return bool|int
	 */
	public function link_post( $post_id, $eventbrite_id ) {

		if ( is_object( $post_id ) && $post_id instanceof WP_Post ) {
			$post_id = $post_id->ID;
		}

		if ( ! is_numeric( $post_id ) ) {
			return false;
		}

		if ( is_object( $eventbrite_id ) || $eventbrite_id instanceof WP_Post ) {
			return false;
		}

		if ( ! is_numeric( $eventbrite_id ) ) {
			return false;
		}

		return update_post_meta( $post_id, '_EventBriteId', $eventbrite_id, true );
	}

	/**
	 * returns filter value for tribe-post-origin.
	 *
	 * @since 4.5
	 *
	 * @return string $origin
	 */
	public function filter_imported_origin() {

		return 'eventbrite-tickets';

	}

	/**
	 * A 32bit absolute integer method, returns as String
	 *
	 * @since 4.5
	 *
	 * @param  string $number A numeric Integer
	 *
	 * @return string         Sanitized version of the Absolute Integer
	 */
	public function sanitize_absint( $number = null ) {
		// If it's not numeric we forget about it
		if ( ! is_numeric( $number ) ) {
			return false;
		}

		$number = preg_replace( '/[^0-9]/', '', $number );

		// After the Replace return false if Empty
		if ( empty( $number ) ) {
			return false;
		}

		// After that it should be good to ship!
		return $number;
	}

	/**
	 * Clears/deletes all Eventbrite meta from an event
	 *
	 * @since 4.5
	 *
	 *
	 * @author jgabois & Justin Endler
	 *
	 * @param int $postId the ID of the event being edited
	 *
	 * @uses   self::metaTags
	 * @return void
	 */
	public function clear_details( $event ) {
		$event = get_post( $event );

		if ( ! is_object( $event ) || ! $event instanceof WP_Post ) {
			return false;
		}

		foreach ( Tribe__Events__Tickets__Eventbrite__Main::$metaTags as $meta ) {
			delete_post_meta( $event->ID, $meta );
		}

		return true;
	}


	/**
	 * Get or Build Global ID
	 *
	 * @since 4.5
	 *
	 * @param int         $id          post id
	 * @param string|null $eb_meta_key meta key
	 * @param string|null $type        type of global id
	 *
	 * @return bool|mixed|string
	 */
	public function get_global_id( $id, $eb_meta_key = null, $type = null ) {

		$global_id = tribe_get_event_meta( (int) $id, '_tribe_aggregator_global_id', true );

		if ( ! empty( $global_id ) ) {
			return $global_id;
		}

		if ( empty( $eb_meta_key ) || empty( $type ) ) {
			return false;
		}


		$eb_id = (int) tribe_get_event_meta( $id, $eb_meta_key, true );
		if ( empty( $eb_id ) ) {
			return false;
		}

		return 'eventbrite.com?id=' . tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $eb_id ) . '&type=' . esc_attr( $type );
	}

	/**
	 * Save the Global ID to Post Meta by Type
	 *
	 * @since 4.5
	 *
	 * @param int    $id        a post id
	 * @param string $global_id global id from EA
	 * @param string $type      the type of global id
	 */
	public function save_global_id( $id, $global_id, $type ) {

		if ( 'event' === $type ) {
			//save event global id
			update_post_meta( $id, '_tribe_aggregator_global_id', esc_attr( $global_id ) );

		} elseif ( 'venue' === $type ) {
			// save venue global id
			$venue_id = get_post_meta( $id, '_EventVenueID', true );
			update_post_meta( $venue_id, '_tribe_aggregator_global_id', esc_attr( $global_id ) );

		} elseif ( 'organizer' === $type ) {
			//save organizer global id
			$org_id = get_post_meta( $id, '_EventOrganizerID', true );
			update_post_meta( $org_id, '_tribe_aggregator_global_id', esc_attr( $global_id ) );
		}

	}

}