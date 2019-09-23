<?php
_deprecated_file( __FILE__, '4.5', 'Import API moved to Event Aggregator' );
// @codingStandardsIgnoreStart

/**
 * EventBrite API
 */
class Tribe__Events__Tickets__Eventbrite__API {

	protected static $instance;

	public $key;
	public $secret;
	public $token;
	public $user_id;

	public $live_statuses = array( 'live', 'started', 'ended', 'completed' );
	public $valid_oauth   = true;

	public static $date_format = 'Y-m-d\TH:i:s\Z';

	public static $expire = 900;

	/**
	 * inforce singleton factory method
	 *
	 * @since 3.9.5
	 * @author bordoni
	 * @return Tribe__Events__Tickets__Eventbrite__API
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			$className = __CLASS__;
			self::$instance = new $className;
		}

		return self::$instance;
	}

	public function __construct() {
		$this->key = apply_filters( 'tribe-eventbrite-key', Tribe__Settings_Manager::get_option( 'eventbrite-app_key', null ) );
		$this->secret = apply_filters( 'tribe-eventbrite-secret', Tribe__Settings_Manager::get_option( 'eventbrite-client_secret', null ) );
		$this->token = apply_filters( 'tribe-eventbrite-token', Tribe__Settings_Manager::get_option( 'eventbrite-token', null ) );
		$this->user_id = apply_filters( 'tribe-eventbrite-user_id', Tribe__Settings_Manager::get_option( 'eventbrite-user_id', null ) );

		/**
		 * Let the user customize what statuses are considered "live".
		 *
		 * "Started"- and "Ended"-status events should remain "published" and visible, and so are considered "Live" even
		 * though not literally of the "Live" status. You can change that behavior by removing them via this filter.
		 *
		 * @since 4.4.7
		 *
		 * @param array $statuses The Eventbrite API statuses to treat as "Live".
		 */
		$this->live_statuses = apply_filters( 'tribe_events_eventbrite_live_statuses', $this->live_statuses );
	}

	public function is_ready() {
		if ( empty( $this->valid_oauth ) || ! $this->valid_oauth  ) {
			return false;
		}

		if ( empty( $this->key ) || ! $this->key  ) {
			return false;
		}

		if ( empty( $this->secret ) || ! $this->secret ) {
			return false;
		}

		if ( empty( $this->token ) || ! $this->token  ) {
			return false;
		}

		if ( empty( $this->user_id ) || ! $this->user_id  ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the API base URL
	 *
	 * @uses  apply_filters
	 * @since 4.0
	 *
	 * @param  string $endpoint Append an Endpoint to make it easier to create URL
	 * @return string           Return the Concat of Base URL and Endpoint
	 */
	public function get_base_url( $endpoint = '', $token = null, $args = null ) {
		if ( ! $this->is_ready() ) {
			$this->key = apply_filters( 'tribe-eventbrite-key', Tribe__Settings_Manager::get_option( 'eventbrite-app_key', null ) );
			$this->secret = apply_filters( 'tribe-eventbrite-secret', Tribe__Settings_Manager::get_option( 'eventbrite-client_secret', null ) );
			$this->token = apply_filters( 'tribe-eventbrite-token', Tribe__Settings_Manager::get_option( 'eventbrite-token', null ) );
			$this->user_id = apply_filters( 'tribe-eventbrite-user_id', Tribe__Settings_Manager::get_option( 'eventbrite-user_id', null ) );
		}

		$baseurl = apply_filters( 'tribe-eventbrite-base_api_url', 'https://www.eventbriteapi.com/v3/' );

		if ( is_null( $token ) ) {
			$token = $this->token;
		}

		if ( is_null( $args ) ) {
			$args = apply_filters( 'tribe-eventbrite-base_api_args', array(
				'token' => $token,
				'expand' => array( 'ticket_classes', 'organizer', 'venue', 'logo' ),
			), $baseurl, $endpoint );
		} else {
			$args['token'] = $token;
		}

		// This allows better usage of the variable on the filter
		if ( isset( $args['expand'] ) && is_array( $args['expand'] ) ) {
			$args['expand'] = implode( ',', $args['expand'] );
		}

		return add_query_arg( $args, $baseurl . $endpoint );
	}

	/**
	 * Get the base URL
	 *
	 * @link   http://developer.eventbrite.com/docs/auth/
	 * @uses   apply_filters
	 * @uses   wp_parse_args
	 * @uses   add_query_arg
	 * @since 4.0
	 *
	 * @param  array  $args     The URL encoded arguments
	 * @param  string $endpoint Endpoint used
	 * @return string           Return the Concat the Base URL for the auth
	 */
	public function get_auth_url( $args = array(), $endpoint = 'authorize' ) {
		$baseurl = apply_filters( 'tribe-eventbrite-auth_url', 'https://www.eventbrite.com/oauth/' );
		$url = $baseurl . $endpoint;

		$defaults = array();
		$args = wp_parse_args( $args, $defaults );

		return add_query_arg( $args, $url );
	}

	public function request( $url, $args = array(), $method = 'post' ) {
		/**
		 * Backwards compatibility
		 * @since 4.1
		 * @deprecated
		 */
		if ( 'get' === $args ) {
			$method = 'get';
			$args = array();
		}

		if ( 'get' === $method ) {
			$url = add_query_arg( $args, $url );
			$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		} else {
			$response = wp_remote_post( $url, array( 'timeout' => 15, 'body' => $args ) );
		}

		if ( ! is_wp_error( $response ) ) {
			$json = (object) json_decode( $response['body'] );

			// Invalidate oauth
			set_transient( 'tribe_oauth_verification_failed', ( ! empty( $response->error ) && 'INVALID_AUTH' === $response->error ), 3 * HOUR_IN_SECONDS );

			return $json;
		} else {
			return $response;
		}
	}

	public function authorize( $code = null ) {
		if ( is_null( $code ) ) {
			$args = array(
				'response_type' => 'code',
				'client_id' => Tribe__Settings_Manager::get_option( 'eventbrite-app_key' ),
			);

			wp_redirect( $this->get_auth_url( $args ) );
			die;
		} else {
			$code = esc_attr( wp_kses( $code, array() ) );
			Tribe__Settings_Manager::set_option( 'eventbrite-auth_code', $code );

			$args = array(
				'code' => $code,
				'client_secret' => $this->secret,
				'client_id' => $this->key,
				'grant_type' => 'authorization_code',
			);

			$response = $this->request( $this->get_auth_url( array(), 'token' ), $args );

			if ( ! empty( $response->access_token ) ) {
				Tribe__Settings_Manager::set_option( 'eventbrite-token', $response->access_token );
				$response = $this->request( $this->get_base_url( 'users/me' ), 'get' );

				if ( ! is_wp_error( $response ) && isset( $response->id ) ) {
					Tribe__Settings_Manager::set_option( 'eventbrite-user_id', $response->id );
					return true;
				}
			}

			return is_wp_error( $response )
				? $response
				: new WP_Error( 'tribe-eventbrite-authorize-fail', __( 'Unable to authorize.', 'tribe-eventbrite' ) );
		}
	}

	public function link_post( $post_id, $eventbrite_id ) {

		if (  is_object( $post_id ) &&  $post_id instanceof WP_Post ) {
			$post_id = $post_id->ID;
		}

		if ( ! is_numeric( $post_id ) ) {
			return false;
		}

		if (  is_object( $eventbrite_id ) ||  $eventbrite_id instanceof WP_Post ) {
			return false;
		}

		if ( ! is_numeric( $eventbrite_id ) ) {
			return false;
		}

		return update_post_meta( $post_id, '_EventBriteId', $eventbrite_id, true );
	}

	/**
	 * sends the organizer data to Eventbrite API
	 *
	 * @since  4.1
	 * @author bordoni
	 * @param  string $term Search query term
	 *
	 * @return mixed the response string or an error on failure
	 */
	public function user_events( $term ) {
		if ( ! is_string( $term ) ) {
			return false;
		}

		$args = array(
			'name_filter' => $term,
		);

		$response = $this->request( $this->get_base_url( 'users/' . $this->user_id . '/events' ), $args, 'get' );

		return $response;
	}

	/**
	 * sends the organizer data to Eventbrite API
	 *
	 * @since  4.0
	 * @author bordoni
	 * @param  int|WP_Post $event The event ID or an WP_Post Object
	 * @return mixed the response string or an error on failure
	 */
	public function sync_organizer( $post ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$args = array(
			'organizer.name' => '',
			'organizer.description.html' => '',
			// 'organizer.logo.id' => null,
		);

		if ( tribe_is_organizer( $post->ID ) ) {
			$organizer = $post;
		} else {
			$organizer = get_post( get_post_meta( $post->ID, '_EventOrganizerID', true ) );

			if ( ! is_object( $organizer ) || is_wp_error( $organizer ) ) {
				return false;
			}
		}

		if ( $organizer ) {
			$eb_organizer_id = tribe_get_event_meta( $organizer->ID, '_OrganizerEventBriteID', true );
			$origin          = tribe_get_event_meta( $organizer->ID, '_OrganizerOrigin', true );
			$name            = $this->string_prepare( get_the_title( $organizer->ID ) );
			$content         = apply_filters( 'the_content', $organizer->post_content );
			$email           = tribe_get_organizer_email( $post->ID );
			$phone           = tribe_get_organizer_phone( $post->ID );

			// if an organizer is imported from Eventbrite do not send the permalink as the website when Pro is active
			if ( Tribe__Events__Tickets__Eventbrite__Main::instance()->filter_imported_origin() !== $origin && class_exists( 'Tribe__Events__Pro__Main' ) ) {
				$website = esc_url_raw( get_permalink( $organizer->ID ) );
			} else {
				$website = tribe_get_organizer_website_url( $post->ID );
			}
		} else {
			// if there's no organizer associated to this event, let's make the author the organizer
			if ( ! empty( $post->post_author ) ) {
				$user = new WP_User( $post->post_author );
				if ( $user->exists() ) {
					$eb_organizer_id = get_user_meta( $user->ID, '_OrganizerEventBriteID', true );
					$name            = $this->string_prepare( $user->display_name );
					$email           = $user->user_email;
					$website         = $user->user_url;
					$content         = $this->string_prepare( $user->description );
				}
			}
		}

		if ( empty( $name ) || ! $name ) {
			$name = apply_filters( 'tribe-eventbrite-no_organizer_name', __( 'Unnamed Organizer', 'tribe-eventbrite' ) );
		}

		// if no stored EB Organizer ID, let's search it just to be safe
		if ( ! isset( $eb_organizer_id ) || ! $eb_organizer_id ) {
			$eb_organizer_id = false;

			$organizers = $this->request( $this->get_base_url( 'users/' . $this->user_id . '/organizers' ), 'get' );
			if ( ! empty( $organizers ) && isset( $organizers->organizers ) ) {
				$organizer_names = wp_list_pluck( $organizers->organizers, 'name' );
				$organizer_index = array_search( $name, $organizer_names );
				if ( false !== $organizer_index ) {
					$eb_organizer_id = $organizers->organizers[ $organizer_index ]->id;
				}
			}
		}

		$mode = ( ! empty( $eb_organizer_id ) ? 'update' : 'create' );

		$args['organizer.name'] = $name;

		if ( ! empty( $content ) ) {
			$args['organizer.description.html'] .= $content;
		}

		if ( ! empty( $email ) ) {
			$args['organizer.description.html'] .= '<p>' . esc_attr__( 'Email:', 'events-eventbrite' ) . ' ' . $email . '</p>' . "\r\n";
		}

		if ( ! empty( $website ) ) {
			$args['organizer.description.html'] .= '<p>' . esc_attr__( 'Website:', 'events-eventbrite' ) . ' ' . esc_url( $website ) . '</p>' . "\r\n";
		}

		if ( ! empty( $phone ) ) {
			$args['organizer.description.html'] .= '<p>' . esc_attr__( 'Phone:', 'events-eventbrite' ) . ' ' . $phone . '</p>' . "\r\n";
		}

		if ( 'create' === $mode ) {
			$response = $this->request( $this->get_base_url( 'organizers' ), $args );
		} else {
			$response = $this->request( $this->get_base_url( 'organizers/' . $eb_organizer_id ), $args );
			if ( ! empty( $response->error ) && 'ARGUMENTS_ERROR' === $response->error ) {
				if ( $organizer ) {
					delete_post_meta( $organizer->ID, '_OrganizerEventBriteID', $eb_organizer_id );
				} else {
					delete_user_meta( $user->ID, '_OrganizerEventBriteID', $eb_organizer_id );
				}
				$eb_organizer_id = null;
				$response = $this->request( $this->get_base_url( 'organizers' ), $args );
			}
		}

		$eb_organizer_id = ( isset( $response->id ) ? $response->id : $eb_organizer_id );

		if ( empty( $user ) ) {
			update_post_meta( $organizer->ID, '_OrganizerEventBriteID', $eb_organizer_id );
			return $eb_organizer_id;
		} else {
			// This might create problems, revision this - @bordoni
			update_user_meta( $user->ID, '_OrganizerEventBriteID', $eb_organizer_id );
			return $eb_organizer_id;
		}

		return false;
	}

	public function sync_venue( $post ) {
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

		$eb_venue_id = get_post_meta( $venue->ID, '_VenueEventBriteID', true );
		$args = array(
			'venue.name' => $this->string_prepare( get_the_title( $venue->ID ) ),
			'venue.address.address_1' => get_post_meta( $venue->ID, '_VenueAddress', true ),
			'venue.address.city' => get_post_meta( $venue->ID, '_VenueCity', true ),
			'venue.address.region' => tribe_get_region( $venue->ID ),
			'venue.address.postal_code' => get_post_meta( $venue->ID, '_VenueZip', true ),
			'venue.address.latitude' => get_post_meta( $venue->ID, '_VenueLat', true ),
			'venue.address.longitude' => get_post_meta( $venue->ID, '_VenueLng', true ),
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

		// We need this Class Exists to prevent bugs on version 4.1.2
		// @todo remove class_exist after 4.2
		if ( class_exists( 'Tribe__Utils__Coordinates_Provider' ) && ( empty( $args['venue.address.latitude'] ) || empty( $args['venue.address.longitude'] ) ) ) {
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
				$message_error = esc_attr__( 'There was a problem determining the coordinates for your event, try using a diferent venue address.', 'events-eventbrite' );
				tribe( 'eventbrite.main' )->throw_notice( $venue, $message_error, $_POST );
				return false;
			}

			$args['venue.address.latitude']  = $coordinates['lat'];
			$args['venue.address.longitude'] = $coordinates['lng'];
		}

		// if no stored EB Venue ID, let's search it just to be safe
		if ( ! isset( $eb_venue_id ) || ! $eb_venue_id ) {
			$venues = $this->request( $this->get_base_url( 'users/' . $this->user_id . '/venues' ), 'get' );
			if ( ! empty( $venues ) && isset( $venues->venues ) ) {
				$venue_names = wp_list_pluck( $venues->venues, 'name' );
				$venue_index = array_search( $args['venue.name'], $venue_names );
				if ( false !== $venue_index ) {
					$eb_venue_id = $venues->venues[ $venue_index ]->id;
				}
			}
		}

		$mode = ( ! empty( $eb_venue_id ) ? 'update' : 'create' );

		if ( 'create' === $mode ) {
			$response = $this->request( $this->get_base_url( 'venues' ), $args );
		} else {
			$response = $this->request( $this->get_base_url( 'venues/' . $eb_venue_id ), $args );
			if ( ! empty( $response->error ) && 'ARGUMENTS_ERROR' === $response->error ) {
				delete_post_meta( $venue->ID, '_VenueEventBriteID', $eb_venue_id );
				$eb_venue_id = null;
				$response = $this->request( $this->get_base_url( 'venues' ), $args );
			}
		}

		if ( is_wp_error( $response ) || ! isset( $response->id ) || ! is_numeric( $response->id )  ) {
			return false;
		}

		update_post_meta( $post->ID, '_VenueEventBriteID', $response->id );
		update_post_meta( $venue->ID, '_VenueEventBriteID', $response->id );

		return $response->id;
	}

	public function sync_ticket( $post, $args = array() ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		if ( ! tribe_is_event( $post->ID ) ) {
			return false;
		}

		$eventbrite_id = get_post_meta( $post->ID, '_EventBriteId', true );

		if ( ! is_numeric( $eventbrite_id ) ) {
			return false;
		}

		$args = (object) wp_parse_args( $args, array(
			'name' => '',
			'description' => '',
			'start' => '',
			'end' => '',
			'cost' => 0,
			'currency' => 'USD',
			'qty' => 0,
			'type' => 0,
			'include_fee' => false,
		) );

		// Prepare and Sanitize
		if ( ! empty( $args->name ) ) {
			$args->name = $this->string_prepare( wp_kses( $args->name, array() ) );
		}


		if ( isset( $args->start ) ) {
			$args->start = intval( $args->start );
		}

		if ( isset( $args->end ) ) {
			$args->end = intval( $args->end );
		}

		if ( isset( $args->cost ) ) {
			$args->cost = absint( $args->cost );
		}

		if ( isset( $args->currency ) ) {
			$args->currency = wp_strip_all_tags( $args->currency, true );
		}

		if ( isset( $args->qty ) ) {
			$args->qty = absint( $args->qty );
		}

		if ( isset( $args->type ) ) {
			$args->type = absint( $args->type );
		}

		if ( isset( $args->include_fee ) ) {
			$args->include_fee = (bool) $args->include_fee;
		}

		$params = array(
			'ticket_class.name' => $this->string_prepare( $args->name ),
			'ticket_class.description' => $this->string_prepare( $args->description ),
			'ticket_class.quantity_total' => $args->qty,

			'ticket_class.sales_start' => date( self::$date_format, $args->start ),
			// 'ticket_class.sales_start_after' => null,

			'ticket_class.sales_end' => date( self::$date_format, $args->end ),

			// 'ticket_class.minimum_quantity' => null,
			// 'ticket_class.maximum_quantity' => null,

			'ticket_class.auto_hide' => false,
			// 'ticket_class.auto_hide_before' => null,
			// 'ticket_class.auth_hide_after' => null,
		);

		if ( empty( $args->description ) ) {
			$params['ticket_class.hide_description'] = true;
		}

		if ( 0 === $args->type && ! empty( $args->cost ) && 0 != $args->cost ) {
			$params['ticket_class.cost'] = "{$args->currency},{$args->cost}";
		} elseif ( 1 === $args->type ) {
			$params['ticket_class.donation'] = true;
		} elseif ( 2 === $args->type ) {
			$params['ticket_class.free'] = true;
		}

		if ( $args->include_fee ) {
			$params['ticket_class.include_fee'] = true;
		} else {
			$params['ticket_class.include_fee'] = false;
		}

		$response = $this->request( $this->get_base_url( 'events/' . $eventbrite_id . '/ticket_classes' ), $params );

		if ( ! empty( $response->error ) ) {
			switch ( $response->error ) {
				case 'COST_GREATER_THAN_FEE':
					$message_error = esc_attr__( 'When absorbing fees, the price must be greater than the fee, status of the Event is "Draft"', 'tribe-eventbrite' );
					break;

				case 'INSUFFICIENT_PACKAGE':
					$message_error = sprintf(
						esc_html__( 'You will need to edit your account settings at %1$s and update your package settings to complete this operation.', 'tribe-eventbrite' ),
						'<a href="https://m.tri.be/eventbrite-insufficient-package" target="_blank">eventbrite.com</a>'
					);
					break;

				default:
					$message_error = sprintf( __( 'There was an error when syncing to EventBrite, %1$s and provide the following information on the Thread: %2$s', 'tribe-eventbrite' ), sprintf( '<a href="https://theeventscalendar.com/support/forums/forum/events/eventbrite-tickets/" target="_blank">%s</a>', __( 'contact our Support', 'tribe-eventbrite' ) ), sprintf( '<br /><code>%s</code>', str_replace( array( "\r\n", "\r", "\n", "\t" ), '', esc_attr( print_r( $response, true ) ) ) ) );
					break;
			}
			tribe( 'eventbrite.main' )->throw_notice( $post, $message_error, $_POST );
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Using a Post Object/ID to download and Sync the Event Image from Eventbrite to WordPress
	 *
	 * @since  3.11
	 *
	 * @param  int/WP_Post  $post      Post Object/ID
	 * @param  boolean $overwrite Overwrites the current featured image if there is one in place
	 *
	 * @return bool             The status if the sync was succesfull
	 */
	public function sync_image( $post, $overwrite = false ) {
		$post = get_post( $post );
		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		if ( ! tribe_is_event( $post->ID ) ) {
			return false;
		}

		$event = self::get_event( $post );
		$thumbnail_id = get_post_meta( $post->ID, '_thumbnail_id', true );
		// wp_get_attachment_url

		// Check if we need to go further, will require a lot of Requests
		if ( empty( $event->logo ) && ! $thumbnail_id ) {
			return false;
		}

		$default_mode = (int) get_post_meta( $post->ID, '_eventbrite_image_sync_mode', true );

		/**
		 * Sync Mode will describe which of the images will take priority. Possible values:
		 *
		 *    -1 = Do not sync
		 *     0 = EventBrite Image
		 *     1 = WordPress Image
		 *
		 * The default behaviour is either to respect the Eventbrite Image Sync Mode setting or
		 * else to give the Eventbrite image priority if that has not been set.
		 *
		 * @param int $default_mode
		 */
		$sync_mode = apply_filters( 'tribe_eb_image_sync_mode', $default_mode, $post, $overwrite );

		if ( -1 === $sync_mode ) {
			return false;
		} elseif ( 1 === $sync_mode || empty( $event->logo ) ) {
			// Talk to EB Api to Prepare the Upload of the image
			$request = $this->request( $this->get_base_url( 'media/upload', null, array( 'type' => 'image-event-logo' ) ), 'get' );

			// We will need Mimetype and Filename of the Attachment
			$attachment = get_attached_file( $thumbnail_id );
			$file       = wp_check_filetype_and_ext( $attachment, basename( $attachment ) );

			// Create a Boundary for a Multipart Request
			$boundary = wp_generate_password( 24 );

			// Store the Payload on an Array
			$payload = array();

			// First, add the standard POST fields:
			foreach ( $request->upload_data as $name => $value ) {
				$payload[] = '--' . $boundary;
				$payload[] = 'Content-Disposition: form-data; name="' . $name . '"';
				$payload[] = ''; // Just linebreak
				$payload[] = $value;
			}

			// Deal with the File(s)
			$payload[] = '--' . $boundary;
			$payload[] = 'Content-Disposition: form-data; name="' . $request->file_parameter_name . '"; filename="' . sanitize_file_name( basename( $attachment ) ) . '"';
			$payload[] = 'Content-Type: "' . $file['type'] . '"';
			$payload[] = ''; // Just linebreak
			$payload[] = file_get_contents( $attachment );

			// Footer
			$payload[] = '--' . $boundary . '--';
			$payload[] = ''; // Just linebreak

			// Upload the request to S3
			$upload = wp_remote_post(
				$request->upload_url,
				array(
					'timeout' => 20,
					'body'    => implode( "\r\n", $payload ),
					'headers' => array(
						'Content-Type' => 'multipart/form-data; boundary=' . $boundary,
					),
				)
			);

			// Fetch the Uploaded File data from EB API
			$notify = $this->request( $this->get_base_url( 'media/upload' ), array( 'upload_token' => $request->upload_token ) );

			return $notify->id;
		} elseif ( 0 === $sync_mode || ! $thumbnail_id ) {
			$tmp = download_url( $event->logo->original->url );

			// Check for download errors
			if ( is_wp_error( $tmp ) ) {
				@unlink( $tmp );
				return false;
			}

			$file_array = array(
				'name'     => $event->id . '-' . $event->logo->id . '.jpg',
				'tmp_name' => $tmp,
			);

			$attachment_id = media_handle_sideload( $file_array, 0 );

			// Check for handle sideload errors.
			if ( is_wp_error( $attachment_id ) ) {
				@unlink( $tmp );
				return false;
			}
		} else {
			// Invalid Mode!
			return false;
		}

		return (bool) update_post_meta( $post->ID, '_thumbnail_id', $attachment_id );
	}

	public function sync_event( $post, $params = array() ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		if ( wp_is_post_revision( $post->ID ) ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				tribe( 'eventbrite.main' )->throw_notice(
					$post,
					__( 'This Event is a revision and cannot sync to Eventbrite.', 'tribe-eventbrite' ),
					$_POST
				);
			}
			return false;
		} else {
			delete_post_meta( $post->ID, Tribe__Events__Main::EVENTSERROROPT );
		}

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
			$timezone = $this->local_timezone();
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

			$start = date( self::$date_format, self::wp_strtotime( $start ) );
			$end   = date( self::$date_format, self::wp_strtotime( $end ) );
		}
		// ...and if UTC times *were* available, we still need to apply the correct formatting
		else {
			$start = date( self::$date_format, strtotime( $start ) );
			$end   = date( self::$date_format, strtotime( $end ) );
		}

		/**
		 * Whether to try and update the event on eventbrite.com to use the same featured image
		 * as set locally (ie, within WordPress).
		 *
		 * @var bool $synch    whether to synchronize
		 * @var int  $post_id  post ID of the event being updated
		 */
		$sync_image = apply_filters( 'tribe_eb_push_image', true, $post->ID );

		$params = wp_parse_args( $params, array(
			'tickets'         => array(),
			'status'          => 'draft',
			'show_tickets'    => 'yes',
			'sync_image'      => $sync_image,
			'image_sync_mode' => 0,
		) );

		// Prepare/Sanitize $params
		$params['status'] = esc_attr( strtolower( $params['status'] ) );

		if ( ! in_array( $params['show_tickets'], array( 'yes', 'no' ) ) ) {
			$params['show_tickets'] = 'yes';
		}

		// Any meta that is fully on our side updates here
		update_post_meta( $post->ID, '_EventShowTickets', $params['show_tickets'] );

		$current_privacy = get_post_meta( $post->ID, '_EventBritePrivacy', true );
		$is_listed       = ! empty( $current_privacy ) && 'not_listed' === $current_privacy ? 0 : 1;

		$args = array(
			'event.name.html'         => $this->string_prepare( get_the_title( $post ) ),
			'event.description.html'  => apply_filters( 'the_content', $post->post_content ),
			'event.start.utc'         => $start,
			'event.start.timezone'    => $timezone,
			'event.end.utc'           => $end,
			'event.end.timezone'      => $timezone,
			'event.currency'          => 'USD',
			'event.online_event'      => 0,
			'event.listed'            => (int) $is_listed,
			'event.shareable'         => 0,
			'event.invite_only'       => 0,
			// 'event.password'       => null,
			// 'event.capacity'       => null,
			'event.show_remaining'    => 1,
			// 'event.logo.id'        => null,
			// 'event.category_id'    => null,
			// 'event.subcategory_id' => null,
			// 'event.format_id'      => null,
			'event.organizer_id'      => $this->sync_organizer( $post ),
			'event.venue_id'          => $this->sync_venue( $post ),
		);


		if ( isset( $params['sync_image'] ) && $params['sync_image'] ) {
			$logo_id = $this->sync_image( $post );

			// Push to EB the new ID
			if ( is_numeric( $logo_id ) ) {
				$args['event.logo_id'] = $logo_id;
			}
		}

		$mode = ( ! empty( $eventbrite_id ) ? 'update' : 'create' );

		// If on Update mode we are less destructive to the EB arguments
		if ( 'update' === $mode ) {
			$eb_event = $this->get_event( false, $eventbrite_id );

			// EB should be the authoritative source for the following:
			$args['event.currency']       = isset( $eb_event->currency ) ? $eb_event->currency : $args['event.currency'];
			$args['event.listed']         = isset( $eb_event->listed ) ? $eb_event->listed : $args['event.listed'];
			$args['event.shareable']      = isset( $eb_event->shareable ) ? $eb_event->shareable : $args['event.shareable'];
			$args['event.invite_only']    = isset( $eb_event->invite_only ) ? $eb_event->invite_only : $args['event.invite_only'];
			$args['event.show_remaining'] = isset( $eb_event->show_remaining ) ? $eb_event->show_remaining : $args['event.show_remaining'];
		}

		if ( 'update' === $mode && $legacy_timezone_mode ) {
			$args['event.start.timezone'] = $eventbrite_timezone;
			$args['event.end.timezone']   = $eventbrite_timezone;
		}

		$args = apply_filters( 'tribe_eb_api_sync_event', $args, $mode, $eventbrite_id, $post, $params );

		if ( empty( $args['event.name.html'] ) ){
			tribe( 'eventbrite.main' )->throw_notice(
				$post,
				__( 'This Event requires a Title to sync to Eventbrite.', 'tribe-eventbrite' ),
				$_POST
			);
			return false;
		}

		if ( ! isset( $args['event.venue_id'] ) || false === $args['event.venue_id'] ) {
			if ( 'create' === $mode ) {
				tribe( 'eventbrite.main' )->throw_notice(
					$post,
					__( 'Eventbrite requires a Venue and it must have a valid Address.', 'tribe-eventbrite' ),
					$_POST
				);
				return false;
			}
			unset( $args['event.venue_id'] );
		}

		if ( ! isset( $args['event.organizer_id'] ) || false === $args['event.organizer_id'] ) {
			if ( 'create' === $mode ) {
				tribe( 'eventbrite.main' )->throw_notice(
					$post,
					__( 'This Event Requires a Organizer to sync to Eventbrite', 'tribe-eventbrite' ),
					$_POST
				);
				return false;
			}
			unset( $args['event.organizer_id'] );
		}

		if ( 'create' === $mode ) {
			$response = $this->request( $this->get_base_url( 'events' ), $args );
		} else {
			$response = $this->request( $this->get_base_url( 'events/' . $eventbrite_id ), $args );

			// For some reason the EB API gives this error if the event comes from V1 of the API
			if ( ! empty( $response->error ) && 'INTERNAL_ERROR' === $response->error && $eventbrite_id ) {
				$response->id = $eventbrite_id;
			}

			if ( ! empty( $response->error ) && 'ARGUMENTS_ERROR' === $response->error ) {
				delete_post_meta( $post->ID, '_EventBriteId', $eventbrite_id );
				$eventbrite_id = null;
				$response = $this->request( $this->get_base_url( 'events' ), $args );
			}
		}

		if ( ! empty( $response->error ) && 'NO_PACKAGE_SELECTED' === $response->error ) {
				tribe( 'eventbrite.main' )->throw_notice(
					$post,
					sprintf(
						esc_html__( 'Before you can create an event you will need to edit your account settings at %1$s and choose a package.', 'tribe-eventbrite' ),
						'<a href="https://m.tri.be/eventbrite-no-package" target="_blank">eventbrite.com</a>'
					),
					$_POST
				);
		}

		if ( ! empty( $response->error ) && 'NOT_AUTHORIZED' === $response->error ) {
			tribe( 'eventbrite.main' )->throw_notice( $post, $response->error_description, $_POST );
			return false;
		}

		// If the response ID is not valid just leave
		if ( empty( $response->id ) || ! is_numeric( $response->id ) ) {
			return false;
		}

		// Save the Response ID
		$this->link_post( $post->ID, $response->id );

		// Set the sync event checkbox.
		update_post_meta( $post->ID, '_EventRegister', 'yes' );

		// Purge cache from EB Cost
		set_transient( 'tribe_eventbrite_cost_' . $post->ID, $this->get_cost( $post ), tribe( 'eventbrite.main' )->get_cache_expiration() );

		if ( ! empty( $params['tickets'] ) ){
			foreach ( (array) $params['tickets'] as $ticket ) {
				$this->sync_ticket( $post->ID, $ticket );
			}
		}

		//Get Current Status
		$current_status = get_post_meta( $post->ID, '_EventBriteStatus', true );

		/**
		 * If no status try to get it from Eventbrite to save
		 * This works on initial save of event or on existing events
		 * New events will start as draft and then changed below
		 */
		if ( ! $current_status ) {
			$current_status = tribe_eb_event_status( $post->ID );
			update_post_meta( $post->ID, '_EventBriteStatus', $current_status );
		}

		//Only Change Status if it is Different From Saved to Prevent Erroneous Error Messages
		if ( $current_status != $params['status'] ) {
			if ( 'live' === $params['status'] || 'started' === $params['status'] ) {
				$status_response = $this->request( $this->get_base_url( 'events/' . $response->id . '/publish' ), array() );
			} elseif ( 'draft' === $params['status'] && 'live' === $current_status ) {
				$status_response = $this->request( $this->get_base_url( 'events/' . $response->id . '/unpublish' ), array() );
			}
			update_post_meta( $post->ID, '_EventBriteStatus', $params['status'] );
		}

		if ( ! empty( $status_response->error ) ) {
			switch ( $status_response->error ) {
				case 'CANNOT_PUBLISH':
				case 'PAYMENT_OPTIONS_PAYPAL_NO_EMAIL':
					$message = sprintf( __( '<strong>Event published successfully.</strong> To make the event <strong>Live</strong>, payment information is <em>required</em>.<br /><a href="%s" target="_blank">Learn how to do this</a> or <a href="%s" target="_blank">go to Eventbrite right now and update the event</a>.', 'tribe-eventbrite' ), esc_url( Tribe__Events__Main::$tecUrl . 'knowledgebase/updating-payment-information-for-an-event-on-eventbrite/' ), esc_url( 'https://www.eventbrite.com/edit?eid=' . Tribe__Events__Tickets__Eventbrite__Main::sanitize_absint( $response->id ) ) );
					break;

				default:
					$message = '<h4>' . esc_html( 'We were unable to sync your event to Eventbrite. Here\'s what happened:', 'tribe-eventbrite' ) . '</h4>' . $status_response->error_description;
					break;
			}

			tribe( 'eventbrite.main' )->throw_notice( $post, $message, array() );
		}

		return $response->id;
	}

	/**
	 * Retreive Information Methods
	 */
	public function get_event( $post = null, $eb = false ) {
		if ( true !== $eb ){
			if ( is_null( $post ) ){
				$post = get_the_ID();
			}

			$post = get_post( $post );

			if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
				return false;
			}

			$eventbrite_id = Tribe__Events__Tickets__Eventbrite__Main::sanitize_absint( get_post_meta( $post->ID, '_EventBriteId', true ) );
		} else {
			$eventbrite_id = Tribe__Events__Tickets__Eventbrite__Main::sanitize_absint( $post );
		}

		if ( ! is_numeric( $eventbrite_id ) ) {
			return false;
		}

		$url = $this->get_base_url( 'events/' . $eventbrite_id );

		$response = wp_cache_get( 'event-' . $eventbrite_id, 'tribe-eventbrite' );
		if ( false == $response ) {
			$response = $this->request( $url, 'get' );
			if ( ! is_wp_error( $response ) && ! empty( $response ) && empty( $response->error ) ) {
				wp_cache_set( 'event-' . $eventbrite_id, $response, 'tribe-eventbrite', self::$expire );
			}
		}

		if ( is_wp_error( $response ) ) {
			return false;
		}

		if ( ! empty( $response->error ) ) {
			return false;
		}

		return $response;
	}

	/**
	 * get Eventbrite ID from event ID
	 *
	 * @author bordoni
	 * @param int|bool $postId the ID of the current event / defaults to false
	 * @return mixed false on failure / the Eventbrite ID on success
	 */
	public function get_event_id( $post = null ) {
		if ( is_null( $post ) ) {
			$post = get_the_ID();
		}
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		return get_post_meta( $post->ID, '_EventBriteId', true );;
	}

	/**
	 * determines an event's status
	 *
	 * @since 1.0
	 * @author bordoni
	 * @param int|bool $postId the ID of the current event / defaults to false
	 * @return string the status of the event
	 */
	public function get_event_status( $post = null ) {
		if ( is_null( $post ) ) {
			$post = get_the_ID();
		}
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$event = $this->get_event( $post );

		if ( ! $event ) {
			return false;
		}

		return $event->status;
	}

	/**
	 * is the event live?
	 *
	 * @since 1.0
	 * @author bordoni
	 * @param int|bool $postId the ID of the current event / defaults to false
	 * @return bool
	 */
	public function is_live( $post = null ) {
		if ( is_null( $post ) ) {
			$post = get_the_ID();
		}
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$event = $this->get_event( $post );

		if ( ! $event ) {
			return false;
		}

		if ( empty( $event->ticket_classes ) || ! in_array( $event->status, $this->live_statuses, true ) ) {
			return false;
		}

		$now = Tribe__Events__Timezones::to_tz( strtotime( 'now' ), $event->end->timezone );

		if ( strtotime( $event->end->local ) < $now ) {
			return false;
		}

		return true;
	}

	/**
	 * get a country code from an event id
	 *
	 * @author bordoni
	 * @param  int $event_id the event id
	 * @return string $code the country code
	 */
	public function get_country_code( $event_id = null ) {
		$country = tribe_get_country( $event_id );

		if ( empty( $country ) ) {
			return;
		}

		$countries = Tribe__View_Helpers::constructCountries();
		$code = array_search( $country, $countries );

		return $code;
	}

	/**
	 * get a country code from an event id
	 *
	 * @author bordoni
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

	/**
	 * Get the ticket costs from Eventbrite
	 *
	 * @param $postId the TEC event to get the Eventbrite ticket costs from
	 * @param $only_if_live Only return the cost if the event is live
	 * @param $range Return only the Lowest and High prices
	 *
	 * @return string|array $prices the price or range of prices of the Eventbrite tickets
	 */
	public function get_cost( $post, $only_if_live = true, $range = true ) {
		$event = $this->get_event( $post );
		// we don't have an associated eventbrite event
		if ( ! $event ) {
			return false;
		}

		// the event isn't live, we shouldn't use the cost from EB
		if ( ! in_array( $event->status, $this->live_statuses ) && $only_if_live ) {
			return false;
		}

		// no tickets attached to the eventbrite event
		if ( empty( $event->ticket_classes ) ) {
			return false;
		}

		$tickets = $event->ticket_classes;
		$cost = '';
		$is_donation = true;
		$is_free = true;
		$prices = array();

		foreach ( $tickets as $key => $ticket ) {

			if ( ! empty( $ticket->hidden ) ) {
				continue;
			}

			if ( ! $ticket->free ) {
				$is_free = false;
			} else {
				$prices[0] = esc_attr__( 'Free', 'events-eventbrite' );
			}

			if ( ! $ticket->donation ) {
				$is_donation = false;
			}

			if ( ! empty( $ticket->cost ) ) {
				$now = current_time( 'timestamp' );

				// Check if sales_start is matters
				if ( ! empty( $ticket->sales_start ) ){
					$start = strtotime( $ticket->sales_start );
					if ( $now < $start ){
						continue;
					}
				}

				// Check if sales_end is matters
				if ( ! empty( $ticket->sales_end ) ){
					$end = strtotime( $ticket->sales_end );
					if ( $now > $end ){
						continue;
					}
				}

				// if we get here it's a valid price tag
				$prices[ $ticket->cost->value ] = $ticket->cost->display;
			}
		}

		if ( $is_donation ) {
			return esc_attr__( 'Donation', 'events-eventbrite' );
		}

		if ( $is_free ) {
			return esc_attr__( 'Free', 'events-eventbrite' );
		}

		// There's more than one ticket, grab the lowest and highest price
		if ( count( $prices ) > 1 && true === $range ) {
			$_prices_key = array_keys( $prices );
			$min = min( $_prices_key );
			$max = max( $_prices_key );

			$_prices[ $min ] = $prices[ $min ];
			$_prices[ $max ] = $prices[ $max ];
			$prices = $_prices;
		}

		if ( 1 === count( $prices ) ) {
			$prices = reset( $prices );
		}

		return $prices;
	}

	/**
	 * see if an ECP event is already linked to this event
	 *
	 * @since 1.0
	 * @author jgabois & Justin Endler
	 * @param  int $ebEventId the Eventbrite event ID
	 * @return bool
	 */
	public function is_event_imported( $event ) {
		if ( ! is_numeric( $event ) ) {
			return false;
		}

		$events = new WP_Query( array(
			'meta_key' => '_EventBriteId',
			'meta_value' => $event,
			'post_type' => Tribe__Events__Main::POSTTYPE,
		) );

		// Utilize the first match
		if ( $events->have_posts() ) {
			$events->next_post();
			return $events->post;
		}

		return false;
	}

	/**
	 * Check if an Eventbrite venue has already been imported, returning the venue post
	 * if it has or `false` if not.
	 *
	 * In strict mode - which is turned off by default - we require that the Eventbrite
	 * ID for the venue matches. Strict mode can be enabled with:
	 *
	 *     add_filter( 'tribe_eb_strict_venue_imports', '__return_true' );
	 *
	 * On the other hand, when strict mode is off if we fail to locate a venue with the
	 * precise Evenbrite ID we are looking for we go on to try and detect a venue with a
	 * matching name, initial address line, city, country and zip and use the first available
	 * match. This helps to avoid issues with what to all intents and purposes look like
	 * duplicate venues being pulled in.
	 *
	 * @since 1.0
	 *
	 * @param array $venue
	 * @param $details
	 *
	 * @return false|WP_Post `false` if the venue is not importer, the venue post object otherwise.
	 */
	public function is_venue_imported( $venue, $details = array() ) {
		if ( ! is_numeric( $venue ) ) {
			return false;
		}

		$venue_post_type = Tribe__Events__Main::VENUE_POST_TYPE;

		// Check to see if the very same venue has already been imported
		$venues = new WP_Query( array(
			'meta_key' => '_VenueEventBriteID',
			'meta_value' => $venue,
			'post_type' => $venue_post_type,
		) );

		// Utilize the first match
		if ( $venues->have_posts() ) {
			$venues->next_post();
			return $venues->post;
		}

		if ( empty( $details ) ) {
			return false;
		}

		// Check to see if what is essentially the same venue - but perhaps doesn't share the
		// same Eventbrite ID - is already in existence (this can help to prevent what are
		// effectively duplicate venues from building up in some circumstances)

		/** @var \wpdb $wpdb */
		global $wpdb;
		$venues_by_title = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID from {$wpdb->posts} WHERE post_title = %s AND post_type = %s",
				$details->name,
				$venue_post_type
			)
		);

		if ( empty( $venues_by_title ) ) {
			return false;
		}

		$venues_by_meta = new WP_Query( array(
			'fields' => 'ids',
			'post_type' => $venue_post_type,
			'meta_query' => array(
				array(
					'key' => '_VenueAddress',
					'value' => $details->address->address_1,
				),
				array(
					'key' => '_VenueCity',
					'value' => $details->address->city,
				),
				array(
					'key' => '_VenueCountry',
					'value' => $this->get_country_name( $details->address->country ),
				),
				array(
					'key' => '_VenueZip',
					'value' => $details->address->postal_code,
				),
			),
		) );

		$matches = array_intersect( $venues_by_title, $venues_by_meta->get_posts() );

		// Utilize the first match
		return ! empty( $matches ) ? get_post( $matches[0] ) : false;
	}

	/**
	 * see if an Eventbrite organizer exists in ECP
	 *
	 * @since 1.0
	 * @author jgabois & Justin Endler
	 * @param  int $ebOrganizerId the Eventbrite organizer ID
	 * @return mixed false on failure / the organizer ID on success
	 */
	public function is_organizer_imported( $organizer ) {
		if ( ! is_numeric( $organizer ) ) {
			return false;
		}

		$organizers = new WP_Query( array(
			'meta_key' => '_OrganizerEventBriteID',
			'meta_value' => $organizer,
			'post_type' => Tribe__Events__Main::ORGANIZER_POST_TYPE,
		) );

		if ( $organizers->have_posts() ) {
			$organizers->next_post();
			return $organizers->post;
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
	 * @see strtotime()
	 * @uses get_option() to retrieve the value of 'gmt_offset'.
	 * @param string $string A date/time string. See `strtotime` for valid formats.
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
			$offset = (float) get_option( 'gmt_offset' );
			$seconds = intval( $offset * HOUR_IN_SECONDS );
			$timestamp = strtotime( $string ) - $seconds;
			return $timestamp;
		}
	}

	/**
	 * Tries to return the local timezone as a timezone string format, even if it is
	 * configured as an offset.
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
	 * Replace the wp_texturize stuff
	 *
	 * @param  string $string string to be untexturized
	 * @return string         Returns the untexturized
	 */
	private static function string_prepare( $string, $html = array() ) {
		return wp_specialchars_decode( str_replace(
			array(
				_x( '&#8220;', 'opening curly double quote' ),
				_x( '&#8221;', 'closing curly double quote' ),
				_x( '&#8217;', 'apostrophe' ),
				_x( '&#8242;', 'prime' ),
				_x( '&#8243;', 'double prime' ),
				_x( '&#8216;', 'opening curly single quote' ),
				_x( '&#8217;', 'closing curly single quote' ),
				_x( '&#8211;', 'en dash' ),
				_x( '&#8212;', 'em dash' ),
			),
			array(
				'"',
				'"',
				'\'',
				'\'',
				'"',
				'\'',
				'\'',
				'-',
				'-',
			),
			$string
		) );
	}

	/**
	 * Deauthorizes the Eventbrite App by deleting the stored credentials.
	 */
	public function deauthorize() {
		Tribe__Settings_Manager::set_option( 'eventbrite-token', null );
		Tribe__Settings_Manager::set_option( 'eventbrite-user_id', null );
	}
}
// @codingStandardsIgnoreEnd