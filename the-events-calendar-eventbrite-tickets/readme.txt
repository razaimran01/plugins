=== The Events Calendar: Eventbrite Tickets ===

Contributors: ModernTribe, barry.hughes, bordoni, borkweb, brianjessee, brook-tribe, faction23, geoffgraham, ggwicz, jazbek, jbrinley, joshlimecuda, leahkoerper, lucatume, mastromktg, mat-lipe, mdbitz, neillmcshea, nicosantos, peterchester, reid.peifer, roblagatta, ryancurban, shane.pearlman, thatdudebutch,  zbtirrell
Tags: widget, events, simple, tooltips, grid, month, list, calendar, event, venue, eventbrite, registration, tickets, ticketing, eventbright, api, dates, date, plugin, posts, sidebar, template, theme, time, google maps, google, maps, conference, workshop, concert, meeting, seminar, summit, forum, shortcode, The Events Calendar, The Events Calendar PRO
Donate link: http://m.tri.be/29
Requires at least: 4.7
Tested up to: 5.1
Stable tag: 4.6.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Eventbrite Tickets extends The Events Calendar with all the basic Eventbrite controls without ever leaving WordPress.

== Description ==

Eventbrite Tickets connects the power of The Events Calendar to your account on Eventbrite.com. Send WordPress events to Eventbrite, import existing Eventbrite events, display tickets, and more.

= The Events Calendar: Eventbrite Tickets =

* Sell tickets from your event's page via Eventbrite
* Create tickets in your WordPress dashboard
* Import Eventbrite events manually or automatically

If you make a new account with Eventbrite, please use our referral code: <a href='http://www.eventbrite.com/r/etp'>http://www.eventbrite.com/r/etp</a>.

For those who want an introduction to how Eventbrite Tickets or the core The Events Calendar works, check out our <a href="http://m.tri.be/39">new user primers.</a>

== Installation ==

= Install =

Just follow these steps:

1. From the dashboard of your site, navigate to Plugins --> Add New.
2. Select the Upload option and hit "Choose File."
3. When the popup appears select the the-events-calendar-eventbrite-tickets.x.x.zip file from your desktop. (The 'x.x' will change depending on the current version number).
4. Follow the on-screen instructions and wait as the upload completes.
5. When it's finished, activate the plugin via the prompt. A message will show confirming activation was successful.
6. For access to new updates, make sure you have added your valid License Key under Events --> Settings --> Licenses.

= Activate =

After downloading and installing the plugin you will need to get your website connected to your Eventbrite account. Since Eventbrite Tickets 4.5 the connection is quick and easy:
1. Navigate to your WP Admin —> Events —> Settings —> APIs tab and click the Connect to Eventbrite button.
2. You will be redirected to your Eventbrite.com account.
3. After logging in, you will be asked to allow our ea.theeventscalendar.com application to access your account.
4. Once you click Allow, your site will be connected and you’ll be redirected to your imports page where you can import your first event from Eventbrite.

And that’s it! From there, you’re ready to begin creating events.

= Requirements =

* PHP 5.2.4 or greater (recommended: PHP 5.4 or greater)
* WordPress 3.9 or above
* jQuery 1.11.x
* The Events Calendar 3.11 or above

== Documentation ==

= Template Tags =

/**
 * @param int post id (optional if used in the loop)
 * @return int the number of tickets for an event
 */
tribe_eb_get_ticket_count( $postId = null )

/**
 * Returns the event id for the post
 *
 * @param int post id (optional if used in the loop)
 * @return int event id, false if no event is associated with post
 */
tribe_eb_get_id( $postId = null)

/**
 * Determine if an event is live
 *
 * @param int post id (optional if used in the loop)
 * @return boolean
 */
tribe_eb_is_live_event( $postId = null)

/**
 * Outputs the Eventbrite post template.  The post in question must be registered with Eventbrite
 * and must have at least one ticket type associated with the event.
 *
 * @param int post id (optional if used in the loop)
 * @uses views/eventbrite-post-template.php for the HTML display
 * @return void
 */
tribe_eb_event( $postId = null )

/**
 * Returns the Eventbrite attendee data for display
 *
 */
tribe_eb_event_list_attendees($eb_event_id, $ebuser_name, $eb_user_password)

== Screenshots ==

1. Admin interface for adding your first ticket to an Eventbrite event
2. Advanced Eventbrite admin options after saving as draft
3. Eventbrite's ticket widget on frontend

== Frequently Asked Questions ==

= Where do I go to file a bug or ask a question? =

Please visit the forum for questions or comments: http://m.tri.be/3a

== Contributors ==

The plugin is produced by <a href="http://m.tri.be/2s">Modern Tribe Inc</a>.

= Current Contributors =

<a href="https://profiles.wordpress.org/barryhughes">Barry Hughes</a>
<a href="https://profiles.wordpress.org/brianjessee">Brian Jessee</a>
<a href="https://profiles.wordpress.org/brook-tribe">Brook Harding</a>
<a href="https://profiles.wordpress.org/cliffpaulick">Clifford Paulick</a>
<a href="https://profiles.wordpress.org/MZAWeb">Daniel Dvorkin</a>
<a href="https://profiles.wordpress.org/geoffgraham">Geoff Graham</a>
<a href="https://profiles.wordpress.org/ggwicz">George Gecewicz</a>
<a href="https://profiles.wordpress.org/bordoni">Gustavo Bordoni</a>
<a href="https://profiles.wordpress.org/jazbek">Jessica Yazbek</a>
<a href="https://profiles.wordpress.org/joshlimecuda">Josh Mallard</a>
<a href="https://profiles.wordpress.org/leahkoerper">Leah Koerper</a>
<a href="https://profiles.wordpress.org/lucatume">Luca Tumedei</a>
<a href="https://profiles.wordpress.org/borkweb">Matthew Batchelder</a>
<a href="https://profiles.wordpress.org/neillmcshea">Neill McShea</a>
<a href="https://profiles.wordpress.org/mastromktg">Nick Mastromattei</a>
<a href="https://profiles.wordpress.org/nicosantos”>Nico Santo</a>
<a href="https://profiles.wordpress.org/peterchester">Peter Chester</a>
<a href="https://profiles.wordpress.org/roblagatta">Rob La Gatta</a>
<a href="https://profiles.wordpress.org/reid.peifer">Reid Peifer</a>
<a href="https://profiles.wordpress.org/shane.pearlman">Shane Pearlman</a>
<a href="https://profiles.wordpress.org/thatdudebutch">Wayne Stratton</a>
<a href="https://profiles.wordpress.org/zbtirrell">Zachary Tirrell</a>

= Past Contributors =

<a href="https://profiles.wordpress.org/caseypatrickdriscoll">Casey Driscoll</a>
<a href="https://profiles.wordpress.org/ckpicker">Casey Picker</a>
<a href="https://profiles.wordpress.org/dancameron">Dan Cameron</a>
<a href="https://profiles.wordpress.org/jkudish">Joachim Kudish</a>
<a href="https://profiles.wordpress.org/jgadbois">John Gadbois</a>
<a href="https://profiles.wordpress.org/jonahcoyote">Jonah West</a>
<a href="https://profiles.wordpress.org/jbrinley">Jonathan Brinley</a>
<a href="https://profiles.wordpress.org/justinendler/">Justin Endler</a>
<a href="https://profiles.wordpress.org/kellykathryn">Kelly Groves</a>
<a href="https://profiles.wordpress.org/kelseydamas">Kelsey Damas</a>
<a href="https://profiles.wordpress.org/kyleunzicker">Kyle Unzicker</a>
<a href="https://profiles.wordpress.org/mdbitz">Matthew Denton</a>
<a href="https://profiles.wordpress.org/mattwiebe">Matt Wiebe</a>
<a href="https://profiles.wordpress.org/mat-lipe">Mat Lipe</a>
<a href="https://profiles.wordpress.org/nickciske">Nick Ciske</a>
<a href="https://profiles.wordpress.org/paulhughes01">Paul Hughes</a>
<a href="https://profiles.wordpress.org/ryancurban">Ryan Urban</a>
<a href="https://profiles.wordpress.org/faction23">Samuel Estok</a>
<a href="https://profiles.wordpress.org/codearachnid">Timothy Wood</a>

= Translations =

Modern Tribe’s premium plugins are translated by volunteers at <a href=“http://m.tri.be/194h”>translations.theeventscalendar.com</a>. There you can find a list of available languages, download translation files, or help update the translations. Thank you to everyone who helps to maintain our translations!

== Add-Ons ==

But wait: there's more! We've got a whole stable of plugins available to help you be awesome at what you do. Check out a full list of the products below, and over at the <a href="http://m.tri.be/3c">Modern Tribe website.</a>

Our Free Plugins:

* <a href="https://wordpress.org/plugins/the-events-calendar/" target="_blank">The Events Calendar</a>
* <a href="http://m.tri.be/18vx" target="_blank">Event Tickets</a>
* <a href="http://wordpress.org/extend/plugins/advanced-post-manager/?ref=tec-readme" target="_blank">Advanced Post Manager</a>
* <a href="http://wordpress.org/plugins/blog-copier/?ref=tec-readme" target="_blank">Blog Copier</a>
* <a href="http://wordpress.org/plugins/image-rotation-repair/?ref=tec-readme" target="_blank">Image Rotation Widget</a>
* <a href="http://wordpress.org/plugins/widget-builder/?ref=tec-readme" target="_blank">Widget Builder</a>

Our Premium Plugins:

* <a href="http://m.tri.be/2c" target="_blank">Events Calendar PRO</a>
* <a href="http://m.tri.be/18vy" target="_blank">Event Tickets Plus</a>
* <a href="http://m.tri.be/2g" target="_blank">The Events Calendar: Community Events</a>
* <a href="http://m.tri.be/18vw" target="_blank">The Events Calendar: Community Tickets</a>
* <a href="http://m.tri.be/2h" target="_blank">The Events Calendar: Facebook Events</a>
* <a href="http://m.tri.be/18h9" target="_blank">The Events Calendar: iCal Importer</a>
* <a href="http://m.tri.be/fa" target="_blank">The Events Calendar: Filter Bar</a>

== Changelog ==

= [4.6.2] 2019-03-04 =

* Fix - Saving event on WordPress will no longer overwrite the Currency on Eventbrite [121484]
* Fix - 0 new strings added, 24 updated, 0 fuzzied, and 0 obsoleted

= [4.6.1] 2019-02-14 =

* Feature - Introduced new "(do not override)" default post status for Eventbrite imports. This preserves events' original statuses from Eventbrite.com upon import (e.g., "draft" events will not be automatically set to "publish" upon import) [112346]
* Fix - Ensure the "Use image on Eventbrite.com" checkbox in the Classic Editor behaves consistently [116973]
* Fix - Ensure that featured images adhere to the rules of the Update Authority setting in Events > Settings > Imports [116973]
* Language - 0 new strings added, 60 updated, 0 fuzzied, and 0 obsoleted

= [4.6] 2019-02-05 =

* Feature - Add check and enforce PHP 5.6 as the minimum version [116282]
* Feature - Add system to check plugin versions to inform you to update and prevent site breaking errors [116841]
* Tweak - Added filters: `tribe_not_php_version_names`
* Deprecated - The constant `REQUIRED_TEC_VERSION`, `init_addon()` and `register_active_plugin()` method has been deprecated in `Tribe__Events__Tickets__Eventbrite__Main` in favor of Plugin Dependency Checking system
* Deprecated - The `tribe_events_eventbrite_start()`, `Tribe_Eventbrite_Load()`, `eventbrite_setup_textdomain()`, and `tribe_events_eventbrite_activate()` in favor of new Dependency Checking System
* Language - 7 new strings added, 52 updated, 0 fuzzied, and 0 obsoleted

= [4.5.7] 2019-01-21 =

* Fix - Eventbrite tickets not appearing on the event frontend [119548]
* Fix - Ensure that looking up an event's Eventbrite.com ID via get_post_meta() does not interfere with other plugins' and themes' post meta lookups (huge thanks to @samsmith89 and @DevinWalker on GitHub for reporting this issue!) [117288]
* Tweak - Ensure the "is migrating" transient and admin notice don't persist indefinitely [118624]
* Language - 0 new strings added, 46 updated, 0 fuzzied, and 0 obsoleted

= [4.5.6] 2018-11-13 =

* Fix - Prevent the "Use image on Eventbrite.com?" checkbox from showing up on non-Event post types [116215]
* Tweak - Update Eventbrite branding images to use their new logo and wordmarks [115661]

= [4.5.5] 2018-10-22 =

* Fix - Ensure we use the correct Eventbrite ID when retrieving the event cost to display it. Props to Heather, Monika, and others for flagging this [111316]
* Fix - Make the Venue data optional when trying to create or update events on Eventbrite [115868]
* Language - 5 new strings added, 117 updated, 1 fuzzied, and 7 obsoleted

= [4.5.4] 2018-10-03 =

* Fix - Ensure that featured images from Eventbrite events are imported even without an Event Aggregator license key [112328]
* Tweak - fire the `tribe_events_eventbrite_event_data_not_found` action and add a log entry when no data is found for an Eventbrite event [114014]
* Tweak - added the `tribe_events_eventbrite_iframe_display` filter to allow overriding Eventbrite tickets iFrame display checks on events [114014]

= [4.5.3] 2018-09-12 =

* Fix - Updated featured image syncing functionality and controls to better integrate with Event Aggregator's Update Authority settings [108387]
* Fix - Implented more robust event privacy control settings in the event admin [111477]
* Tweak - Improve the setting of default "Ticket Start Sale Date" and "Ticket End Sale Date" values for better compatibility with the Eventbrite API [77069]

= [4.5.2] 2018-08-01 =

* Fix - In certain cases when updating an event in the admin stops the tickets from showing on the front end, thanks to Scott for reporting [110791]

= [4.5.1] 2018-06-20 =

* Add - A message before the Eventbrite metabox and hide the fields if there the app has not been authorized to import [86346]
* Tweak - The EB 4.5 migration process to better detect events for migration and insure all fields are migrated [106623]
* Tweak - Only enable Live, Draft, and Canceled status for Eventbrite events [106950]
* Tweak - Make the default selection of no for registering an event with Eventbrite [106503]
* Fix - Setup the text domain to enable translation of activation errors [104749]
* Fix - Discount link under Eventbrite Shortcuts
* Language - 16 new strings added, 119 updated, 0 fuzzied, and 5 obsoleted

= [4.5] 2018-06-04 =

* Add - Syncing of Eventbrite events updates to WordPress [81822]
* Add - Move the Eventbrite imports to Event Aggregator service [81822]
* Add - Migration from old Eventbrite Imports to new Event Aggregator standard []
* Add - Bulk imports from Eventbrite Profile [82749]
* Add - Prompt on update or install to Eventbrite 4.5 to authorize Event Aggregator EB App  [100224]
* Tweak - Move Eventbrite settings from Legacy Import to Settings Import Tab [94388]
* Tweak - Styling of the Eventbrite datepicker to match style of event datepicker [99173]
* Tweak - EB tickets metabox to hide admin fields if you are not the owner of the event in Eventbrite [94697]
* Deprecated - Eventbrite authorization interface with replace in Event Aggregator [97239]
* Deprecated - Eventbrite API class including calls to tribe( ‘eventbrite.api’ ) use tribe( ‘eventbrite.sync.event’ ) or tribe( ‘eventbrite.sync.utilities ) instead [106949]

= [4.4.9] 2018-01-10 =

* Fix - Fixed broken datepicker fields in the Eventbrite ticket-creation metabox [92871]

= [4.4.8] 2017-09-20 =

* Tweak - Adjustments to support Eventbrite packages [88020]
* Compatibility - Minimum supported version of WordPress is now 4.5

= [4.4.7.1] 2017-07-30 =

* Fix - Fixed a bug where Eventbrite ticket forms failed to display even when the event status was "live" (with thanks and props to Nicholas on the forums for flagging this and highlighting a solution) [84019]

= [4.4.7] 2017-07-26 =

* New - New filter to specify in which Eventbrite API statuses an imported event's ticket form should never show: tribe_events_eventbrite_hide_iframe_statuses. [78485]
* New - New filter for customizing what Eventbrite API statuses are considered "Live" statuses: tribe_events_eventbrite_live_statuses. [78485]
* Tweak - Ensure that *all* available Eventbrite API statuses are usable for imported events, instead of just "Live" and "Draft". [78485]
* Tweak - Ensure that the full-sized featured image of an imported event is used, instead of a 450x200 version which the API defaults to. [73440]
* Tweak - Add a filter to disable the region not being synced with Eventbrite for venues outside of the U.S. [73823]
* Tweak - Add a message that recurring events are not supported in the Event Series metabox of Pro [73826]
* Tweak - Optimized message displayed when errors are returned from Eventbrite API (for clarity) [80861]
* Tweak - Do not include organizer permalink in description if organizer is imported from Eventbrite and when Pro is active [67802]
* Fix - Include organizer description on import from Eventbrite and sync description first with other attributes [67802]

= [4.4.6] 2017-07-13 =

* Fix - Stopped using deprecated function Tribe__Events__Cost_Utils::instance() [80859]
* Tweak - Increased height of ticket iframe to lessen the likelihood of a scrollbar appearing [73820]
* Tweak - Show message on Import page dropdown when API Key is not configured [75363]
* Tweak - Default to not showing the ticket form when there are no tickets (overrideable) [81027]

= [4.4.5] 2017-06-14 =

* Fix - Add a space when concatenating address line 1 and 2 from EventBrite [63069]

= [4.4.4] 2017-05-17 =

* Tweak - Further adjustments made to our plugin licensing system [78506]
* Tweak - Fixed typo in synchronization options [70157]

= [4.4.3] 2017-05-04 =

* Tweak - adjustments made to our plugin licensing system

= [4.4.2] 2017-04-19 =

* Fix - Made the Register Event checkbox stay selected after initial update (thank you @Rebecca for the report in our forums) [75936]

= [4.4.1] 2017-03-23 =

* Tweak - Efficiency improvements to minimize impact of retrieving event costs from Eventbrite [74869]

= [4.4] 2017-01-09 =

* Fix - avoid the duplication of Venues imported from EventBrite due to country information API change
* Tweak - added a "deauthorization" button to the API keys settings screen [35730]
* Tweak - cleaner wp-admin error-checking when "venue is missing" [41324]
* Fix -  fixed and issue with importing the Eventbrite Event ID using the importer [71328]

= [4.3.3] 2016-12-20 =

* Tweak - Updated the template override instructions in a number of templates [68229]

= [4.3.2] 2016-12-08 =

* Fix - Resolved issue with bad website data added to an organizer when syncing to Eventbrite without PRO active. [69533]

= [4.3.1] 2016-10-20 =

* Tweak - Added plugin dir constant.
* Tweak - Deprecated camelCase args in the Main class.
* Tweak - Registered plugin as active with Tribe Common. [66657]

= [4.3] 2016-10-13 =

* Tweak - Updated to be 4.3 compatible

= [4.2.1] 2016-08-17 =

* Fix - Prevent Erroneous Error Messages from trying to change event status when it was not being changed. (Thank you for the forum post @Jessie in our support forum)[64915]

= [4.2] 2016-06-08 =

* Tweak - Language files in the `wp-content/languages/plugins` path will be loaded before attempting to load internal language files (Thank you to user @aafhhl for bringing this to our attention!)
* Tweak - Move plugin CSS to PostCSS
* Fix - Resolved issue where venues not being registered with Eventbrite due to missing latitude and longitude data

= [4.1.3] 2016-05-19 =

* Tweak - Improve reporting of errors when connections to eventbrite.com fail

= [4.1.2] 2016-04-28 =

* Fix - Resolved an issue that prevented an event's venues from being imported into Eventbrite on sync. We were missing the latitude and longitude data that was needed but it's there now and moving venues from WordPress to Eventbrite as it should.

= [4.1.1] 2016-03-30 =

* Fix - Resolved issue where HTML entities were double-escaped when publishing the title and other non-wysiwyg fields
* Fix - Fixed issue where the Eventbrite-specified country failed to import properly (props to handhugs from our forums for reporting this issue!

= [4.1] 2016-03-15 =

* Feature - Search your Account Events when Importing from Eventbrite to WordPress (Thanks to @leadreadlivellc for the idea and for sharing it in our forums!)
* Feature - WordPress images now will be uploaded to Eventbrite
* Tweak - Now Eventbrite imported events won't lose all HTML from the titles (Thanks to the Eventbrite team for working with us on this one!)
* Fix - Resolved issue where Eventbrite costs sometimes overrode costs when running alongside other ticketing add-ons and vice versa
* Fix - Fixed an issue where errors during event submission caused Eventbrite fields to show as empty
* Fix - Fixed a bug that caused event synchronization with Eventbrite to fail

= [4.0.1] 2016-02-17 =

* Tweak - Add a nice invalidation process for OAuth tokens, prevents weird bugs for invalid tokens
* Fix - API method to check if the Event is live, will take Timezones in consideration on all instances

= [4.0] 2015-12-02 =

* Feature - add filters making it possible to prevent image synchronization (Thanks to capacitycanada for the help!)
* Fix - No more Empty notices when activating the plugin

= [3.12.1] 2015-09-22 =

* Fix - Resolve a problem where ticket sale dates could not always be set to valid dates (our thanks to @d20games for reporting this)
* Fix - Avoid rounding the number of minutes for an event start/end time when it is not an increment of 5

= [3.12] 2015-09-08 =

* Feature - Modified timezone handling to take advantage of new capabilities within The Events Calendar
* Tweak - Modify the error messages to be presented in a more verbose way
* Tweak - Improvements on the sanitization on data coming from Eventbrite API
* Tweak - Use only include_fee true/false now that Eventbrite has deprecated the use of split_fee (Cheers to Alain for the report!)
* Tweak - Make Eventbrite the authoritative source for currency, listed status, shareable status, invite only status, and whether or not to show remaining tickets (Thank you Jessie!)
* Tweak - Modify the way we import the Eventbrite description for better HTML results on WordPress-side Events (Thanks Carlos for the help!)
* Fix - Improve the way datepickers handle start and end dates boundaries
* Fix - Fixed a bug where the tickets iFrame was not respecting the Eventbrite privacy settings (Props to Michael for this!)
* Fix - Fixed a price bug where some inactive numbers where been displayed (Thank you Ben for the report!)
* Fix - Fixed the conditionals for Eventbrite import page, prevents redirect to list of blog posts
* Fix - Fixed errors when permalinks are set to default

= [3.11.1] 2015-07-23 =

* Bug - Resolved an issue where a change to the Eventbrite API caused tickets with a cost greater than 0 to error out

= [3.11] 2015-07-22 =

* Security - Added escaping to a number of previously un-escaped values
* Feature - Image sync when importing Event from Eventbrite
* Tweak - Deprecated Tribe_Events_EventBrite_Template in favor of Tribe__Events__Tickets__Eventbrite__Template (Props to northwest for the idea!)
* Tweak - Currency on the front-end views are now respecting the Eventbrite currency (Cheers to adibreuer for the help!)
* Tweak - Added clarification text to the OAuth field in settings (Thank you northwest for the heads up!)
* Tweak - Support URLs in the Eventbrite ID field when importing events (Thanks to Michael for the inspiration!)
* Tweak - Conformed code to updated coding standards
* Bug - Authorization redirecting to the correct page when user doesn't have the right permissions (Props to Jeremy for the report!)
* Bug - Resolved an issue where the Manage Attendees link was not pointing to the correct page
* Bug - Fixed an issue causing currency symbols to be stripped from display in some circumstances (Thanks to prydonian for the heads up!)
* Bug - Fixed a bug where the optional timezone conversion didn't function appropriately (Thank you Jennifer for reporting this!)

= 3.10.2 - 2015-07-09 =

* Security - Fixing XSS vulnerability on the Eventbrite import page

= 3.10.1 - 2015-06-25 =

* Fix - Updated Eventbrite API calls to be compatible with their recent updates around expansions
* Fix - Make the Timezone implementation more reliable for Ticket Sales dates
* Tweak - Improved the message when the Payments for an event is not correctly configured

= 3.10 - 2015-06-16 =

* Tweak - Plugin code has been refactored to new standards: that did result in a new file structure and many renamed classes. Old class names will be deprecated in future releases and, while still working as expected, you can keep track of any deprecated classes yours or third party plugins are calling using the Log Deprecated Notices plugin (https://wordpress.org/plugins/log-deprecated-notices/)
* Tweak - Incorporated subtle changes to bring all add-ons in line with core/PRO naming conventions
* Tweak - Added messaging to help indicate to users when an event that is linked to eventbrite.com is "owned" by another user
* Tweak - Added some changelog formatting enhancements after seeing keepachangelog.com :)
* Feature - Added a setting to make timezone conversion upon import configurable on a per-event basis (thanks to Jennifer on the forums for the first report, and the team at Eventbrite for their support moving this forward!)

= 3.9.6 — 2015-05-21 =

* Fixed a bug where sites running on 32bit servers couldn’t import events (thanks to Rich on the forums for the first report!)

= 3.9.5 =

* Improved the visibility for the Authorization URL information
* Fixed problems for imported events where the ticket Form would not display (hat tips to p88dadmin and Neil for reporting this in our forums!)
* Fixed instances where multiple prices were incorrectly displayed in the cost field
* Implemented better error handling of some exceptions

= 3.9.4 =

* Updated some of the Eventbrite API-related code from the previous release to ensure compatibility with older versions of PHP
* Implemented some other minor bug fixes

= 3.9.3 =

* Overhauled the plugin codebase so it now uses Eventbrite API v3.0

= 3.9.2 =
* Hardened URL output to protect against XSS attacks.

= 3.9.1 =

* Fixed a series of bugs within various plugin template tags causing fatal errors (our thanks to crhallen in the forums for highlighting these issues!)
* Fixed a bug where the ticket prices displayed from Eventbrite could be incorrect (big thanks to saibotny on the forums for the report!)
* Moved storage of cached ticket pricing to transients

= 3.9 =

* Fixed a bug where the price of donation based tickets would show up blank (thanks to stevenmillstein for the original report!)
* Fixed a bug that loaded the Eventbrite ticket form over HTTP within the context of an HTTPS request (thanks to kjoboyle on the forums for the first report!)

= 3.8.1 =

* Fixed a bug where the time and timezone of events hosted by Eventbrite could be inadvertently changed (our thanks to Frederick W Chapman for highlighting this)

= 3.8 =

* Fixed some PHP strict standards notices
* Added support for UTC offset-based timezones

= 3.7 =

* Fixed some translation strings textdomains for correct translations
* Fixed a bug where the same venue could be re-imported repeatedly, resulting in unnecessary duplicates
* Improved handling of events imported from a different timezone when using a city-based WordPress timezone (thanks to Cloud Genius for highlighting this!)

= 3.6 =

* Removed the Google Checkout payment option when publishing events to Eventbrite
* Incorporated updated French translation files, courtesy of Alaric Breithof

= 3.5 =

* Added a feature where the tribe_get_cost() function in events templates will now display ticket cost from Eventbrite (thanks to randalldon at the forums for reporting this!)
* Added a filter 'tribe_events_eb_request' that can be used to filter any request params before they are sent to Eventbrite (thanks to Cloud Genius on the forums for the report!)
* Added Eventbrite event privacy status to the event editor , along with a link to change the Event privacy on Eventbrite
* Updated the API so that Events will now only be created at Eventbrite if the event is published in TEC
* Improved error messaging when errors occur
* Incorporated updated Romanian translation files, courtesy of Cosmin Vaman
* Incorporated updated Spanish translation files, courtesy of Lorenzo Sastre Muntaner

= 3.4 =

* Donation-based tickets will now show the word "Donation" under the cost column on the event edit screen
* Improved general compliance with PHP strict standards
* Incorporated updated French translation files, courtesy of Pierre Trochet

= 3.3 =

* Featured images on events posts will now be included in the Custom Header field under the "Design" tab on Eventbrite
* The first image in the Custom Header field under the "Design" tab on Eventbrite will now be set as the featured image on events that are imported from Eventbrite

= 3.2.1 =

* Fixed issue with Eventbrite tickets not showing up on single event pages
* Fixed incorrect available ticket count in the admin editor

= 3.2 =

* Switched to JSON format for Eventbrite API requests to ensure formatting passes through properly
* Ensured line breaks are preserved when sending events to Eventbrite (thanks to user timelesstime for reporting this on the forums!)
* Ensured apostrophes in organizer names do not break Eventbrite API requests (thanks to rocketpop for the original report!)
* Added a notice for when an event update or creation is rejected by Eventbrite for no specified reason (thanks to Jared for bringing this to our attention!)
* Ensured our Eventbrite requests won't break when invalid HTML is passed through the editor
* Fixed cost field sometimes being marked as required for free events
* Eventbrite Tickets will now allow events to be saved to Eventbrite without a venue!


= 3.1 =

* Updated Eventbrite API class to use newly required Olson format for the timezone
* Improved some error messages
* Updated translations: Brazilian Portuguese (new), Romanian (new)
* Various minor bug and security fixes

= 3.0.1 =

* Performance improvements to the plugin update engine

= 3.0 =

Updated version number to 3.0.x for plugin version consistency

= 1.0.7 =

* Fix plugin update system on multisite installations

= 1.0.6 =

*Small features, UX and Content tweaks:*

* Total plugin audit/code review for bugs & incomplete functionality.
* Code modifications to ensure compatibility with The Events Calendar/Events Calendar PRO 3.0.
* Due to a change in the Eventbrite API, Eventbrite events can no longer be deleted on the WordPress side; you can now cancel them on WordPress and must go to Eventbrite.com to truly delete.
* Better handling of cross-time zone imports.
* Ticket sale date range is now limited to times before the event takes place.
* Clarified a couple error/warning messages.
* Incorporated new French translation files, courtesy of Frederic-Xavier DuBois.
* Incorporated new Polish translation files, courtesy of Marek Kosina.
* Incorporated new Swedish translation files, courtesy of Andreas Bodin.

*Bug fixes:*

* Plugin now activates when installed on a site running PHP 5.4 or newer.
* Addressed unstylized text indicating events are in draft format, which impacted certain users.
* PayPal email field now triggers as expected.
* Imported events no longer hide tickets on the WP frontend for certain users.
* Tickets no longer appear listed under Related Events when running on the Events 3.0 codebase.
* Offline payment methods no longer show until an online payment method is selected.
* Removed a dead link from eventbrite-events-table.php.
* Addressed an issue where the admin CSS file wasn't loading properly on certain installations.

= 1.0.5 =

*Small features, UX and Content tweaks:*

(none in this release)

*Bug fixes:*

* Various minor bug fixes.

= 1.0.4 =

*Small features, UX and Content tweaks:*

(none in this release)

*Bug fixes:*

* Fixed an ambiguous error message that appeared when the site failed to connect with Eventbrite.

= 1.0.3 =

*Small features, UX and Content Tweaks:*

* Ticket box is now automatically displayed on all Eventbrite events (it previously defaulted to hidden).
* Added new notification that appears upon initial activation, directing users to the new user primer.
* Ticket-specific field for Eventbrite Tickets is no longer mandatory.
* Incorporated new Dutch language files, courtesy of Jurgen Michiels.
* Incorporated new Finnish language files, courtesy of Petri Kajander.
* Incorporated new Italian language files, courtesy of Marco Infussi.

*Bug Fixes:*

* Plugin now works with PHP 5.4 and above.
* Dual cost fields (one for Events, the other for Eventbrite) no longer conflict when both are being used on the same event.
* Fixed a bug where, for some users, editing an existing event yielded a slew of Eventbrite-generated notices.

= 1.0.2 =

*Bug Fixes:*

* Removed unclear/confusing message warning message regarding the need for plugin consistency and added clearer warnings with appropriate links when plugins or add-ons are out date.

= 1.0.1 =

*Small features, UX and Content Tweaks:*

* Incorporated new Spanish translation files, courtesy of Hector at Signo Creativo.
* Added new "Events" admin bar menu with Eventbrite-specific options.

*Bug Fixes:*

* Removed "No Venues/Organizers Found For This User" error when not trying to send a venue/organizer to Eventbrite.
* Added warning message when attempting to begin ticket sales for an Eventbrite event anytime in the past.
* Added proper error messaging when attempting to send country- or state-less events to Eventbrite.

= 1.0 =

Initial release
