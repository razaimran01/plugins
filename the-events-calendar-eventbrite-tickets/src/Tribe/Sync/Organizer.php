<?php


/**
 * EventBrite Organizer Sync
 *
 * @since 4.5
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Organizer {

	/**
	 * Get Organizer Data for Event and format it for EA
	 *
	 * @since 4.5
	 *
	 * @param WP_Post $post an WP_Post Object
	 *
	 * @return array|bool venue data or false
	 */
	public function get_organizer_data( $post ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return false;
		}

		$args = array(
			'global_id'                  => '',
			//'organizer.eb_organizer_id'  => '',
			'organizer.name'             => '',
			'organizer.description.html' => '',
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
			$global_id       = tribe( 'eventbrite.sync.utilities' )->get_global_id( $organizer->ID, '_OrganizerEventBriteID', 'organizer' );
			$origin          = tribe_get_event_meta( $organizer->ID, '_OrganizerOrigin', true );
			$name            = tribe( 'eventbrite.sync.utilities' )->string_prepare( get_the_title( $organizer->ID ) );
			$content         = apply_filters( 'the_content', $organizer->post_content );
			$email           = tribe_get_organizer_email( $post->ID );
			$phone           = tribe_get_organizer_phone( $post->ID );

			// if an organizer is imported from Eventbrite do not send the permalink as the website when Pro is active
			if ( tribe( 'eventbrite.sync.utilities' )->filter_imported_origin() !== $origin && class_exists( 'Tribe__Events__Pro__Main' ) ) {
				$website = esc_url_raw( get_permalink( $organizer->ID ) );
			} else {
				$website = tribe_get_organizer_website_url( $post->ID );
			}
		} else {
			// if there's no organizer associated to this event, let's make the author the organizer
			if ( ! empty( $post->post_author ) ) {
				$user = new WP_User( $post->post_author );
				if ( $user->exists() ) {
					$global_id       = get_user_meta( $user->ID, '_tribe_aggregator_global_id', true );
					$eb_organizer_id = get_user_meta( $user->ID, '_OrganizerEventBriteID', true );
					$name            = tribe( 'eventbrite.sync.utilities' )->string_prepare( $user->display_name );
					$email           = $user->user_email;
					$website         = $user->user_url;
					$content         = tribe( 'eventbrite.sync.utilities' )->string_prepare( $user->description );
				}
			}
		}

		if ( empty( $name ) || ! $name ) {
			$name = apply_filters( 'tribe-eventbrite-no_organizer_name', __( 'Unnamed Organizer', 'tribe-eventbrite' ) );
		}

		if ( ! empty( $global_id ) ) {
			$args['global_id'] = esc_attr( $global_id );
		}

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

		return $args;
	}

}
