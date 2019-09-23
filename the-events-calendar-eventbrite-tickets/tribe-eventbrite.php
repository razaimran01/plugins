<?php
/*
 Plugin Name: The Events Calendar: Eventbrite Tickets
 Description: Eventbrite Tickets connects the power of The Events Calendar to your account on Eventbrite.com. Send WordPress events to Eventbrite, import existing Eventbrite events, display tickets, and more.
 Version: 4.6.2
 Author: Modern Tribe, Inc.
 Author URI: http://m.tri.be/27
 Text Domain: tribe-eventbrite
 License: GPLv2 or later
*/

/*
Copyright 2009-2012 by Modern Tribe Inc and the contributors

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

define( 'EVENTBRITE_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'EVENTBRITE_PLUGIN_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( EVENTBRITE_PLUGIN_FILE ) . '/src/functions/php-min-version.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'tribe-eventbrite', EVENTBRITE_PLUGIN_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.6
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_events_eventbrite_not_php_version_plugin_name( $names ) {
		$names['tribe-eventbrite'] = esc_html__( 'Events Eventbrite Tickets', 'tribe-eventbrite' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_events_eventbrite_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}
	return false;
}

/**
 * Attempt to Register Plugin
 *
 * @since 4.6
 *
 */
function tribe_register_eventbrite() {

	//remove action if we run this hook through common
	remove_action( 'plugins_loaded', 'tribe_register_eventbrite', 50 );

	// if we do not have a dependency checker then shut down
	if ( ! class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

		add_action( 'admin_notices', 'tribe_eventbrite_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_eventbrite_show_fail_message' );

		//prevent loading of PRO
		remove_action( 'tribe_common_loaded', 'tribe_events_tickets_eventbrite_init' );

		return;
	}

	tribe_init_eventbrite_autoloading();

	new Tribe__Events__Tickets__Eventbrite__Plugin_Register();

}
add_action( 'tribe_common_loaded', 'tribe_register_eventbrite', 5 );
// add action if Event Tickets or the Events Calendar is not active
add_action( 'plugins_loaded', 'tribe_register_eventbrite', 50 );

/**
 * Instantiate class and set up WordPress actions on Common Loaded
 *
 * @since 4.6
 *
 */
add_action( 'tribe_common_loaded', 'tribe_events_tickets_eventbrite_init' );
function tribe_events_tickets_eventbrite_init() {

	$classes_exist = class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Tickets__Eventbrite__Main' );
	$version_ok = $classes_exist && tribe_check_plugin( 'Tribe__Events__Tickets__Eventbrite__Main' );

	if ( class_exists( 'Tribe__Main' ) && ! is_admin() && ! class_exists( 'Tribe__Events__Tickets__Eventbrite__PUE__Helper' ) ) {
		tribe_main_pue_helper();
	}

	if ( ! $version_ok ) {

		// if we have the plugin register the dependency check will handle the messages
		if ( class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

			new Tribe__Events__Tickets__Eventbrite__PUE( __FILE__ );

			return;
		}

		add_action( 'admin_notices', 'tribe_eventbrite_show_fail_message' );
		add_action( 'network_admin_notices', 'tribe_eventbrite_show_fail_message' );

		return;
	}

	include_once( 'src/functions/xml-to-array.php' );
	include_once( 'src/functions/template-tags.php' );
	include_once( 'src/functions/deprecated-template-tags.php' );

	tribe_singleton( 'eventbrite.main', new Tribe__Events__Tickets__Eventbrite__Main() );
	tribe_singleton( 'eventbrite.pue', new Tribe__Events__Tickets__Eventbrite__PUE( EVENTBRITE_PLUGIN_FILE ) );

	tribe( 'eventbrite.main' )->on_load();

	//add_action( 'plugins_loaded', array( tribe( 'eventbrite.main' ), 'on_load' ) );

}

/**
 * Sets up autoloading for the plugin
 */
function tribe_init_eventbrite_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix( 'Tribe__Events__Tickets__Eventbrite__', EVENTBRITE_PLUGIN_DIR . '/src/Tribe', 'tribe-eventbrite' );

	// deprecated classes are registered in a class to path fashion
	foreach ( glob( EVENTBRITE_PLUGIN_DIR . '/src/deprecated/*.php' ) as $file ) {
		$class_name = str_replace( '.php', '', basename( $file ) );
		$autoloader->register_class( $class_name, $file );
	}
	$autoloader->register_autoloader();
}

/**
 * Shows message if the plugin can't load due to TEC not being installed.
 */
function tribe_eventbrite_show_fail_message() {

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$domain = 'tribe-eventbrite';

	// If we don't have Common classes load the old fashioned way
	if ( ! class_exists( 'Tribe__Main' ) ) {
		load_plugin_textdomain( $domain, false, $mopath );
	} else {
		// This will load `wp-content/languages/plugins` files first
		Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

	$url = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';
	echo '<div class="error"><p>'
	. sprintf(
		'%1s <a href="%2s" class="thickbox" title="%3s">%4s</a>.',
		esc_html__( 'To begin using The Events Calendar: Eventbrite Tickets, please install the latest version of', 'tribe-eventbrite' ),
		esc_url( $url ),
		esc_html__( 'The Events Calendar', 'tribe-eventbrite' ),
		esc_html__( 'The Events Calendar', 'tribe-eventbrite' )
		) .
	'</p></div>';

}

/**
 * Start Eventbrite
 *
 * @deprecated 4.6
 *
 */
function tribe_events_eventbrite_start() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	return;

}

/**
 * Bootstrap the plugin
 *
 * @deprecated 4.6
 *
 */
function Tribe_Eventbrite_Load() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	tribe_events_eventbrite_start();
}

/**
 * Setup Text Domain
 *
 * @deprecated 4.6
 *
 * @since 4.5.1
 *
 */
function eventbrite_setup_textdomain() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$domain = 'tribe-eventbrite';

	// If we don't have Common classes load the old fashioned way
	if ( ! class_exists( 'Tribe__Main' ) ) {
		load_plugin_textdomain( $domain, false, $mopath );
	} else {
		// This will load `wp-content/languages/plugins` files first
		Tribe__Main::instance()->load_text_domain( $domain, $mopath );
	}

}

/**
 * Eventbrite Activation Hook
 *
 * @deprecated 4.6
 *
 */
function tribe_events_eventbrite_activate() {
	_deprecated_function( __FUNCTION__, '4.6', '' );

	require_once EVENTBRITE_PLUGIN_DIR . '/src/Tribe/Main.php';
	require_once EVENTBRITE_PLUGIN_DIR . '/src/Tribe/PUE.php';

	tribe_events_eventbrite_start();
}