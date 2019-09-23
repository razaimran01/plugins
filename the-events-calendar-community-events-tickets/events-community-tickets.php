<?php
/*
Plugin Name: The Events Calendar: Community Events Tickets
Plugin URI:  http://m.tri.be/1ace
Description: Community Events Tickets is an add-on providing additional a way for community organizers to offer paid tickets for community events.
Version: 4.7.0
Author: Modern Tribe, Inc.
Author URI: http://m.tri.be/21
Text Domain: tribe-events-community-tickets
Domain Path: /lang/
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

/*
Copyright 2011-2012 by Modern Tribe Inc and the contributors

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

define( 'EVENTS_COMMUNITY_TICKETS_DIR', dirname( __FILE__ ) );
define( 'EVENTS_COMMUNITY_TICKETS_FILE', __FILE__ );

// Load the required php min version functions
require_once dirname( EVENTS_COMMUNITY_TICKETS_FILE ) . '/src/functions/php-min-version.php';
require_once EVENTS_COMMUNITY_TICKETS_DIR . '/vendor/autoload.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatals
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'tribe-events-community-tickets', EVENTS_COMMUNITY_TICKETS_FILE );

	/**
	 * Include the plugin name into the correct place
	 *
	 * @since  4.6
	 *
	 * @param  array $names current list of names
	 *
	 * @return array
	 */
	function tribe_events_community_tickets_not_php_version_plugin_name( $names ) {
		$names['tribe-events-community-tickets'] = esc_html__( 'Community Events Tickets', 'tribe-events-community-tickets' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tribe_events_community_tickets_not_php_version_plugin_name' );
	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}
	return false;
}

/**
 * Attempt to Register Plugin
 *
 * @since 4.6
 */
function tribe_register_community_tickets() {

	//remove action if we run this hook through common
	remove_action( 'plugins_loaded', 'tribe_register_community_tickets', 50 );

	// if we do not have a dependency checker then shut down
	if ( ! class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {

		add_action( 'admin_notices', 'tribe_show_community_tickets_fail_message' );
		add_action( 'network_admin_notices', 'tribe_show_community_tickets_fail_message' );

		//prevent loading of PRO
		remove_action( 'tribe_common_loaded', 'tribe_events_community_tickets_init' );

		return;
	}

	tribe_init_community_tickets_autoloading();

	new Tribe__Events__Community__Tickets__Plugin_Register();

}
add_action( 'tribe_common_loaded', 'tribe_register_community_tickets', 5 );
// add action if Event Tickets or the Events Calendar is not active
add_action( 'plugins_loaded', 'tribe_register_community_tickets', 50 );

/**
 * Instantiate class and set up WordPress actions on Common Loaded
 *
 * @since 4.6
 */
add_action( 'tribe_common_loaded', 'tribe_events_community_tickets_init' );
function tribe_events_community_tickets_init() {
	$classes_exist	= class_exists( 'Tribe__Events__Main' ) && class_exists( 'Tribe__Events__Community__Tickets__Main' );
	$plugin_check 	= tribe_check_plugin( 'Tribe__Events__Community__Tickets__Main' );
	$version_ok    	= $classes_exist && $plugin_check;

	if ( class_exists( 'Tribe__Main' ) && ! is_admin() && ! class_exists( 'Tribe__Events__Community__Tickets__PUE__Helper' ) ) {
		tribe_main_pue_helper();
	}

	if ( ! $version_ok ) {
		// if we have the plugin register the dependency check will handle the messages
		if ( class_exists( 'Tribe__Abstract_Plugin_Register' ) ) {
			new Tribe__Events__Community__Tickets__PUE( __FILE__ );

			return;
		}

		add_action( 'admin_notices', 'tribe_show_community_tickets_fail_message' );
		add_action( 'network_admin_notices', 'tribe_show_community_tickets_fail_message' );

		return;
	}

	// Setup initial instance.
	Tribe__Events__Community__Tickets__Main::instance();
}

/**
 * Requires the autoloader class from the main plugin class and sets up
 * autoloading.
 *
 * @since 4.6
 */
function tribe_init_community_tickets_autoloading() {
	if ( ! class_exists( 'Tribe__Autoloader' ) ) {
		return;
	}

	$autoloader = Tribe__Autoloader::instance();

	$autoloader->register_prefix( 'Tribe__Events__Community__Tickets__', dirname( __FILE__ ) . '/src/Tribe', 'events-community-tickets' );

	// deprecated classes are registered in a class to path fashion
	foreach ( glob( dirname( __FILE__ ) . '/src/deprecated/*.php' ) as $file ) {
		$class_name = str_replace( '.php', '', basename( $file ) );
		$autoloader->register_class( $class_name, $file );
	}

	$autoloader->register_autoloader();
}

/**
 * Shows message if the plugin can't load due to TEC not being available.
 *
 * @since 4.6
 */
function tribe_show_community_tickets_fail_message() {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	$mopath = trailingslashit( basename( dirname( __FILE__ ) ) ) . 'lang/';
	$domain = 'tribe-events-community-tickets';

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
		esc_html__( 'To begin using The Events Calendar: Community Events Tickets, please install the latest version of', 'tribe-events-community-tickets' ),
		esc_url( $url ),
		esc_html__( 'The Events Calendar', 'tribe-events-community-tickets' ),
		esc_html__( 'The Events Calendar', 'tribe-events-community-tickets' )
		) .
	'</p></div>';
}
