<?php
// @codingStandardsIgnoreStart


/**
 * Eventbrite API Compatibility Class to Prevent Fatal Errors when calling Deprecated API Class
 */
class Tribe__Events__Tickets__Compatibility_Eventbrite__API {

	public static $why_deprecated = 'Eventbrite API has been moved to Event Aggregator and all relevant methods moved to eventbrite.sync.x providers.';

	public static function instance() {

		return tribe( 'eventbrite.api' );
	}

	public function __construct() {

	}

	public function is_ready() {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_base_url( $endpoint = '', $token = null, $args = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_auth_url( $args = array(), $endpoint = 'authorize' ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function request( $url, $args = array(), $method = 'post' ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function authorize( $code = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function link_post( $post_id, $eventbrite_id ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function user_events( $term ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function sync_organizer( $post ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function sync_venue( $post ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function sync_ticket( $post, $args = array() ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function sync_image( $post, $overwrite = false ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function sync_event( $post, $params = array() ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_event( $post = null, $eb = false ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_event_id( $post = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_event_status( $post = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function is_live( $post = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_country_code( $event_id = null ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_country_name( $code ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function get_cost( $post, $only_if_live = true, $range = true ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function is_event_imported( $event ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function is_venue_imported( $venue, $details = array() ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function is_organizer_imported( $organizer ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public static function wp_strtotime( $string ) {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function local_timezone() {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}

	public function deauthorize() {
		_deprecated_function( __METHOD__, '4.5', self::$why_deprecated );

		return false;
	}
}
// @codingStandardsIgnoreEnd