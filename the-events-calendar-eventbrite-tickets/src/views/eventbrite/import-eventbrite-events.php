<?php

/**
 * Import events from Eventbrite events in the admin form
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/eventbrite/import-eventbrite-events.php
 *
 * @version 4.4.7
 * @package Tribe__Events__MainEventBrite
 * @since  3.0
 * @author Modern Tribe Inc.
 */

?>
<?php if ( ! empty( $_REQUEST['error'] ) ) : ?>
	<div class='error'>
		<p><?php esc_html_e( $_REQUEST['error'] ); ?></p>
	</div>
<?php endif; ?>
<div class="wrap">
	<!--<div class="icon32 icon32-posts-spevents"><br></div>-->
	<h2><?php esc_html_e( 'Import Eventbrite Events', 'tribe-eventbrite' ); ?></h2>
	<div>
		<p><?php esc_html_e( 'Import the event details from any public Eventbrite event.', 'tribe-eventbrite' ); ?></p>
		<p><?php esc_html_e( 'Your Eventbrite event id can be found by going to your event page at Eventbrite.com.  Once there, examine your browser\'s address bar to see something like this - https://www.eventbrite.com/event/212356789.  The number after \'/event/\' is your event id.', 'tribe-eventbrite' ); ?></p>
	</div>
	<form method="get" action="post.php">
		<input name="post_type" type="hidden" value="tribe_events"/>
		<table class="form-table">
			<tbody>
				<tr valign="top">

					<th scope="row"><label for="eventbrite_id"><?php esc_html_e( 'Select an Existing Event', 'tribe-eventbrite' ) ?></label></th>
					<td>
						<input name="eventbrite_selected_id" data-placeholder='<?php esc_attr_e( 'Select from your existing Eventbrite events', 'tribe-eventbrite' ) ?>' type="hidden" id="select-eventbrite-existing" class="tribe-eb-select2" />
					</td>
				</tr>

				<tr valign="top">

					<th scope="row"><label for="eventbrite_id"><?php esc_html_e( 'Eventbrite Event ID', 'tribe-eventbrite' ) ?></label></th>
					<td>
						<input name="eventbrite_id" type="text" id="eventbrite_id" value="" class="regular-text"/>
						<p style="display:none;"><small><?php esc_html_e( 'This field will be used instead of the selected event above.', 'tribe-eventbrite' ); ?></small></p>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="eventbrite_tz_correct"><?php esc_html_e( 'How are timezones handled?', 'tribe-eventbrite' ) ?></label></th>
					<td>
						<?php
						/**
						 * Prior to release/120 this row included a control letting users decide to convert the imported
						 * event's time to the site (WP) timezone.
						 *
						 * With the introduction of better timezone support in core, this ability has been removed in
						 * relation to all further events that are imported. With that in mind, this row has been modified
						 * slightly to ease the transition in behaviour and hopefully reduce confusion ("Where did the
						 * checkbox go?!").
						 *
						 * @todo consider removal of this entire row in a future release cycle
						 */
						$open_help_link = '<a href="' . Tribe__Settings::instance()->get_url() . '" target="_blank">';

						$message = esc_html__( 'The event will be imported complete with the same timezone as defined on eventbrite.com. You can make use of The Events Calendar&#146;s %1$s%2$s%3$s to change how the actual time is displayed.', 'tribe-eventbrite' );
						echo sprintf( $message, $open_help_link, 'timezone settings', '</a>' );
						?>
					</td>
				</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Go"></p>
		<?php wp_nonce_field( 'import_eventbrite', 'import_eventbrite' ) ?>
	</form>
</div>
