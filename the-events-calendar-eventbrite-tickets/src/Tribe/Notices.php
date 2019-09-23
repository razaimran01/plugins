<?php


/**
 * Class Tribe__Events__Tickets__Eventbrite__Notices
 *
 * Renders Eventbrite notices.
 */
class Tribe__Events__Tickets__Eventbrite__Notices {

	/**
	 * @var Tribe__Events__Tickets__Eventbrite__Main
	 */
	protected $main;

	/**
	 * Tribe__Events__Tickets__Eventbrite__Notices constructor.
	 *
	 * @param Tribe__Events__Tickets__Eventbrite__Main|null $main
	 */
	public function __construct( Tribe__Events__Tickets__Eventbrite__Main $main = null ) {
		$this->main = $main;
	}

	public function draft_event() {
		$event = $this->main->editing_eb_event();

		if ( 'draft' === $event->status && ! empty( $event->ticket_classes ) ) {
			$content = __(
				"Eventbrite status is set to DRAFT. You can update this in the 'Eventbrite Information' section further down this page.",
				'tribe-eventbrite'
			);

			Tribe__Admin__Notices::instance()->render_paragraph( 'eventbrite-event-draft', $content );
		}

		return false;
	}

	public function no_tickets() {
		$event = $this->main->editing_eb_event();

		if ( empty( $event->ticket_classes ) && 'draft' !== $event->status ) {
			$content = __(
				'You did not create any tickets for your event.  You will not be able to publish this event on Eventbrite unless you first add a ticket at Eventbrite.com.',
				'tribe-eventbrite'
			);

			Tribe__Admin__Notices::instance()->render_paragraph( 'eventbrite-event-no-tickets', $content );
		}

		return false;
	}

	/**
	 * Renders the notices for a specific event.
	 *
	 * @return void
	 */
	public function render_event_notices() {
		if ( empty( $_GET['post'] ) ) {
			return false;
		}

		$event = get_post( absint( $_GET['post'] ) );

		if ( ! $event instanceof WP_Post ) {
			return false;
		}

		if ( ! tribe_is_event( $event->ID ) ) {
			return false;
		}

		$notices = $this->main->get_notices( $event );
		if ( empty( $notices ) ) {
			return false;
		}

		$tags = array(
			'a'      => array(
				'href'   => array(),
				'title'  => array(),
				'target' => array(),
				'rel'    => array(),
			),
			'ul'     => array(),
			'ol'     => array(),
			'li'     => array(),
			'br'     => array(),
			'em'     => array(),
			'strong' => array(),
			'b'      => array(),
			'p'      => array(),
		);

		// Include a template
		ob_start();
		include_once $this->main->plugin_dir . 'src/views/eventbrite/eb-admin-notices.php';
		$content = ob_get_clean();


		Tribe__Admin__Notices::instance()->render( 'eventbrite-sync-notices', $content, false );

		/**
		 * Allow users to filter the Metakey based on Event Object
		 *
		 * @param string  $metakey The Meta name
		 * @param WP_Post $event   The Event Object
		 */
		$error_metakey = apply_filters( 'tribe_eventbrite_notices_key', '_tribe-eventbrite-notices', $event );
		delete_post_meta( $event->ID, $error_metakey );
	}

	public function missing_core() {
		$url     = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';
		$title   = __( 'The Events Calendar', 'tribe-events-community' );
		$message = sprintf( __( 'To begin using The Events Calendar: Eventbrite Tickets, please install the latest version of <a href="%s" class="thickbox" title="%s">The Events Calendar</a>.',
			'tribe-events-community' ), esc_url( $url ), esc_attr( $title ) );

		Tribe__Admin__Notices::instance()->render_paragraph(
			'missing-core',
			esc_attr__( 'Success! You have authorized your Eventbrite Application Key.', 'tribe-eventbrite' )
		);
	}

	/**
	 * 4.5 Update Message
	 *
	 * @since 4.5.1
	 *
	 */
	public function get_ea_connection_message() {

		$add_ea_msg = __( 'Thank you for installing Eventbrite Tickets! Be sure to <a href="%s" title="%s">connect your Eventbrite account</a> so that you can start creating and importing events.', 'tribe-eventbrite' );
		$token      = Tribe__Settings_Manager::get_option( 'eventbrite-app_key', null );
		if ( ! empty( $token ) ) {
			$add_ea_msg = __( 'Thank you for updating Eventbrite Tickets! Be sure to <a href="%s" title="%s">connect your Eventbrite account</a> so that your events stay linked to their Eventbrite.com listings.', 'tribe-eventbrite' );
		}

		$url     = esc_url( admin_url( Tribe__Settings::$parent_page . '&page=tribe-common&tab=addons&post_type=tribe_events' ) );
		$title   = __( 'Eventbrite Tickets', 'tribe-eventbrite' );
		$message = sprintf(
			'<p>' . $add_ea_msg . '</p>',
			esc_url( $url ),
			esc_attr( $title )
		);

		return $message;
	}

	/**
	 * 4.5 Update Notice to encourage connecting to EA
	 *
	 * @since 4.5
	 *
	 */
	public function render_ea_connection_notices() {

		// if we have a security token or we just authorized EA then do not show notice
		$eb_token = tribe_get_option( 'eb_security_key' );
		if ( ! empty( $eb_token ) || 'new' === tribe_get_request_var( 'ea-eb-token', false ) ) {
			return;
		}

		$message = $this->get_ea_connection_message();

		Tribe__Admin__Notices::instance()->render( 'eventbrite-no-ea-connection', $message, false );
	}

	// @codingStandardsIgnoreStart
	/**
	 * Authorization Success
	 *
	 * @deprecated
	 *
	 * @return bool
	 */
	public function auth_success() {
		_deprecated_function( __METHOD__, '4.5' );

		$api = tribe( 'eventbrite.api' );

		if ( ! $api->is_ready() ) {
			return false;
		}

		Tribe__Admin__Notices::instance()->render_paragraph(
			'eventbrite-auth-success',
			esc_attr__( 'Success! You have authorized your Eventbrite Application Key.', 'tribe-eventbrite' )
		);
	}

	/**
	 * Deauthorized Success
	 *
	 * @deprecated
	 *
	 * @return bool
	 */
	public function deauth_success() {
		_deprecated_function( __METHOD__, '4.5' );

		if ( ! $this->main->deauth_success() ) {
			return false;
		}

		Tribe__Admin__Notices::instance()->render_paragraph(
			'eventbrite-deauth-success',
			esc_attr__( 'Success! You have deauthorized your Eventbrite Application Key.', 'tribe-eventbrite' )
		);

	}

	/**
	 * Invalid Token
	 *
	 * @deprecated
	 *
	 * @return bool
	 */
	public function invalid_token() {
		_deprecated_function( __METHOD__, '4.5' );

		$url = Tribe__Settings::instance()->get_url( array( 'tab' => 'addons' ) );

		$content = sprintf( __( 'Your Eventbrite token is not valid, check %s page to see how to create a new one.',
			'events-eventbrite' ), '<a href="' . $url . '">' . esc_html__( 'Add-ons API', 'events-eventbrite' ) . '</a>' );

		Tribe__Admin__Notices::instance()->render_paragraph( 'eventbrite-invalid-token', $content );
	}
	// @codingStandardsIgnoreEnd
}