<?php

/**
 * displays the existing Eventbrite event in the event meta box
 * expects _EventBriteId is present when editing an event
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/eventbrite/eventbrite-events-table.php
 *
 * @package Tribe__Events__MainEventBrite
 * @since  3.0
 * @version 4.5.3
 * @author Modern Tribe Inc.
 */
?>
<tr>
	<td colspan="2" class="snp_sectionheader">
		<h4><?php esc_html_e( 'Eventbrite Information', 'tribe-eventbrite' ); ?></h4>
	</td>
</tr>
<tr id="eventbrite-id-table">
	<td width="125">
		<?php esc_html_e( 'Eventbrite Event ID:', 'tribe-eventbrite' ); ?>
	</td>
	<td>
		<a target="_blank" href="https://www.eventbrite.com/edit?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?></a>
	</td>
</tr>
<tr>
	<td>
		<?php esc_html_e( 'Eventbrite Event Status:', 'tribe-eventbrite' ); ?>
	</td>
	<td>
		<?php if ( count( $event->ticket_classes ) > 0 ){ ?>
			<?php
			if ( 'canceled' !== $event->status ) {
				$current_status = in_array( $event->status, tribe( 'eventbrite.sync.utilities' )->live_statuses ) ? 'live' : $event->status;
			?>
			<select name="EventBriteStatus" tabindex="<?php $tribe_ecp->tabIndex(); ?>" class="tribe-dropdown">
				<option value='draft'<?php selected( $current_status, 'draft' ) ?>><?php echo esc_html( _x( 'Draft', 'Eventbrite status', 'tribe-eventbrite' ) ); ?></option>
				<option value='live'<?php selected( $current_status, 'live' ) ?>><?php echo esc_html( _x( 'Live', 'Eventbrite status', 'tribe-eventbrite' ) ); ?></option>
				<option value='canceled'<?php selected( $current_status, 'canceled' ) ?>><?php echo esc_html( _x( 'Canceled', 'Eventbrite status', 'tribe-eventbrite' ) ); ?></option>
			</select>
			<?php } else { ?>
				<b><?php echo esc_attr( ucfirst( $event->status ) ); ?></b>
				<p class="tec_eb_status_cancel_notice"><?php esc_html_e( 'At this time, the event can only be made live again through the Quick Links in the My Events tab of your account dashboard.', 'tribe-eventbrite' ); ?> <a href="https://www.eventbrite.com/myevent?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_html_e( 'Manage Event', 'tribe-eventbrite' ); ?> &raquo;</a></p>
			<?php } ?>
		<?php } else { ?>
			<p><?php esc_html_e( 'This event was created without a ticket. You need to create a ticket before you can change this event\'s status.', 'tribe-eventbrite' ); ?> <a href="https://www.eventbrite.com/myevent?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $_EventBriteId ) ); ?>&ref=etckt"><?php esc_html_e( 'Manage Event', 'tribe-eventbrite' ); ?> &raquo;</a></p>
		<?php } ?>
	</td>
</tr>
<tr>
	<td><?php esc_html_e( 'Eventbrite Event Privacy', 'tribe-eventbrite' ) ?>:</td>
	<td>
		<?php

		$event_privacy = '';

		if ( isset( $privacy_meta ) && ! empty( $privacy_meta ) ) {
			$event_privacy = get_post_meta( get_the_ID(), '_EventBritePrivacy', true );
		} elseif ( isset( $event->listed ) ) {
			$event_privacy = $event->listed ? 'listed' : 'not_listed';
		}

		?>
		<select name="EventBritePrivacy" tabindex="<?php $tribe_ecp->tabIndex(); ?>" class="tribe-dropdown">
			<option value="listed" <?php selected( $event_privacy, 'listed' ) ?>><?php echo esc_html( _x( 'Public', 'Eventbrite event privacy', 'tribe-eventbrite' ) ); ?></option>
			<option value="not_listed" <?php selected( $event_privacy, 'not_listed' ) ?>><?php echo esc_html( _x( 'Private', 'Eventbrite event privacy', 'tribe-eventbrite' ) ); ?></option>
		</select>
		<br>
		<?php esc_html_e( 'Public events are listed publicly on Eventbrite and search engines. Private events are not.', 'tribe-eventbrite' ); ?><br />
	</td>
</tr>

<?php if ( ! empty( $event->ticket_classes ) ) { ?>
	<?php
	$ebTecMultipleCosts = false;
	$ebTecLastPrice = isset( $event->ticket_classes[0]->price ) ? $event->ticket_classes[0]->price : null;
	?>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<h4><?php esc_html_e( 'Associated Tickets:', 'tribe-eventbrite' ); ?></h4>
			<p><?php esc_html_e( 'The following Eventbrite tickets are associated to this event', 'tribe-eventbrite' ) ?></p>
		</td>
	</tr>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<table class="EB-table">
				<tr>
					<td width="120px"><?php esc_attr_e( 'Ticket', 'tribe-eventbrite' ); ?></td>
					<td width="85px"><?php esc_attr_e( 'Cost', 'tribe-eventbrite' ) ?></td>
					<td width="40px"><?php esc_attr_e( 'Sold', 'tribe-eventbrite' ); ?></td>
					<td width="40px"><?php esc_attr_e( 'Available', 'tribe-eventbrite' ); ?></td>
					<td width="100px"><?php esc_attr_e( 'End Sales', 'tribe-eventbrite' ); ?></td>
				</tr>
			<?php foreach ( $event->ticket_classes as $ticket ) {
				if ( ! $ebTecMultipleCosts && isset( $ticket->price ) ) {
					$ebTecMultipleCosts = ( $ticket->price == $ebTecLastPrice ) ? false : true;
					$ebTecLastPrice = $ticket->price;
				}
				?>
				<tr>
					<td><a href="<?php echo esc_url( $event->url ); ?>" target="_blank"><?php echo esc_attr( $ticket->name ); ?></a></td>
					<td>
					<?php
					if ( $ticket->free ) {
						esc_attr_e( 'Free', 'events-eventbrite' );
					} elseif ( $ticket->donation ){
						esc_attr_e( 'Donation', 'events-eventbrite' );
					} else {
						echo esc_attr( $ticket->cost->display );
					}
					?>
					</td>
					<td><?php echo esc_html( isset( $ticket->quantity_sold ) ? $ticket->quantity_sold : '&nbsp;' ); ?></td>
					<td><?php echo esc_html( isset( $ticket->quantity_total ) ? $ticket->quantity_total : $event->capacity ); ?></td>
					<td><?php echo esc_html( isset( $ticket->sales_end ) ? date( 'Y-m-d', strtotime( $ticket->sales_end ) ) : date( 'Y-m-d', strtotime( $event->start->utc ) ) ); ?></td>
				</tr>
			<?php } ?>
				<tr>
					<td>
						<a id="edit-ticket" href="<?php echo esc_url( 'https://www.eventbrite.com/myevent?eid=' . $event->id . '/#viewtickets' ) ?>"><?php esc_attr_e( 'Edit existing tickets', 'tribe-eventbrite' ) ?></a><br>
						<a id="new-ticket" href="<?php echo esc_url( 'https://www.eventbrite.com/edit?eid=' . $event->id ) ?>">+ <?php esc_attr_e( 'Create a new ticket', 'tribe-eventbrite' ) ?></a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php } elseif ( ! isset( $event->status ) || 'draft' != $event->status ) { ?>
	<tr>
		<td colspan="2" class="snp_sectionheader">
			<h4><?php esc_attr_e( 'There are no tickets associated with this event!', 'tribe-eventbrite' ); ?></h4>
		 <div style='color:red'><?php esc_attr_e( 'You cannot publish this event in Eventbrite unless you first add a ticket on Eventbrite.com.', 'tribe-eventbrite' ); ?></div>
		</td>
	</tr>
<?php } ?>
<tr>
	<td colspan="2" class="snp_sectionheader">
		<h4><?php esc_attr_e( 'Eventbrite Shortcuts:', 'tribe-eventbrite' ); ?></h4>
	</td>
</tr>
<tr>
	<td colspan="2">
		<ul class='event_links'>
			<li><a href="https://www.eventbrite.com/myevent?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_attr_e( 'Manage my Event', 'tribe-eventbrite' ); ?></a></li>
			<li><a href="https://www.eventbrite.com/myevent/<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>/multi_event_discounts/"><?php esc_attr_e( 'Manage Discounts', 'tribe-eventbrite' ); ?></a></li>
			<li><a href="https://www.eventbrite.com/attendees-list?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_attr_e( 'Manage Attendees', 'tribe-eventbrite' ); ?></a></li>
			<li><a href="https://www.eventbrite.com/attendees-email?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_attr_e( 'Email Attendees', 'tribe-eventbrite' ); ?></a></li>
			<li><a href="https://www.eventbrite.com/attendees-badges?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_attr_e( 'Print Badges', 'tribe-eventbrite' ); ?></a></li>
			<li><a href="https://www.eventbrite.com/attendees-list?eid=<?php echo esc_attr( tribe( 'eventbrite.sync.utilities' )->sanitize_absint( $event->id ) ); ?>&ref=etckt"><?php esc_attr_e( 'Print Check-In List', 'tribe-eventbrite' ); ?></a></li>
		</ul>
	</td>
</tr>
