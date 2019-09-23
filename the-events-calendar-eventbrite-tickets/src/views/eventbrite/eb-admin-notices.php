<?php
/**
 * EventBrite notices on the Post Edit page
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/eventbrite/eb-admin-notices.php
 *
 * @package Tribe__Events__Tickets__Eventbrite__Main
 * @since  3.11
 * @version 4.4.7
 * @author Modern Tribe Inc.
 */

?>
<ul>
	<?php foreach ( $notices as $key => $message ) { ?>
		<li><?php echo wp_kses( $message, $tags ); ?></li>
	<?php } ?>
</ul>
