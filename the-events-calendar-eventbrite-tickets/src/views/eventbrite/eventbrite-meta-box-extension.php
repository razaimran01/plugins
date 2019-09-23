<?php

/**
 * displays the existing Eventbrite event meta box in the editor
 *
 * Override this template in your own theme by creating a file at:
 *
 *     [your-theme]/tribe-events/eventbrite/eventbrite-meta-box-extension.php
 *
 * @package Tribe__Events__MainEventBrite
 * @since  3.0
 * @version 4.5.3
 * @author Modern Tribe Inc.
 */

/**
 * Add Message to Authorize EB and Hide Meta Fields
 */
$eb_token = tribe_get_option( 'eb_security_key' );
if ( empty( $eb_token ) ) {
	?>
	<tr>
		<td colspan="2" class="tribe_sectionheader">
			<?php do_action( 'tribe_eventbrite_before_integration_header' ); ?>
			<h4><?php esc_html_e( 'Tickets', 'tribe-eventbrite' ); ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="tribe-eb-notice"><?php echo tribe( 'eventbrite.main' )->notices->get_ea_connection_message(); ?></div>
		</td>
	</tr>
	<?php
	return;
}
?>
<script charset="utf-8">
jQuery(document).ready(function($){

	// hide/show EventBrite fields
	$('.EBForm').hide();

	if ( $("#EventBriteToggleOn:checked").length ) {
		$(".EBForm").show();
	}

	$("#EventBriteToggleOn").click(function(){
		$(".EBForm").slideDown('slow');
	});
	$("#EventBriteToggleOff").click(function(){
		$(".EBForm").slideUp(200);
	});

	var paymentType = $("input[name='EventBriteIsDonation']:checked");

	if ( $('.EBForm:visible').length > 0 && paymentType.val() == 0 ) {
		$('.eb-tec-payment-options').show(0);
	} else if ( paymentType.val() == 1 ) {
		$('.eb-tec-payment-options').show(0);
	} else {
		$('.eb-tec-payment-options').hide(0);
	}

	var togglePaymentOptions = function(){
		if ( $("#EventBriteToggleOn:checked").length === 0 ){
			return;
		}

		var paymentType = $("input[name='EventBriteIsDonation']:checked");
		if ( paymentType.val() == 0 ) {
			$('.eb-tec-payment-options').show(0);
			$('#EventBriteEventCost').parents('tr').show(0);
		} else if ( paymentType.val() == 1 ) {
			$('#EventBriteEventCost').parents('tr').hide(0);
			$('.eb-tec-payment-options').show(0);
		} else {
			$('#EventBriteEventCost').parents('tr').hide(0);
			$('.eb-tec-payment-options').hide(0);
		}
	}

	togglePaymentOptions();

	$("input[name='EventBriteIsDonation']").change( togglePaymentOptions );

	// hide/show additional payment option fields
	var ebTecAcceptPaymentInputs = $('#eb-tec-payment-options-checkboxes input');
	if( ebTecAcceptPaymentInputs.is(':checked') ){
		$(".tec-eb-offline-pay-options").show();
	} else {
		$(".tec-eb-offline-pay-options").hide();
	}
	function ebTecShowHideAdditionalPaymentOptions(event) {
		if ( event && $('.EBForm:visible').length > 0 ) {
			var divIndex = ebTecAcceptPaymentInputs.index(this);
			var notSelectedIndex = ebTecAcceptPaymentInputs.index( $('#eb-tec-payment-options-checkboxes input:radio:not(:checked)') );
			if(this.checked) {
				$('.eb-tec-payment-instructions:eq('+divIndex+')').slideDown(200);
				$(".tec-eb-offline-pay-options").show();
			} else {
			 $('.eb-tec-payment-instructions:eq('+divIndex+')').slideUp(200);
			}
		$('#eb-tec-payment-options-checkboxes input:radio:not(:checked)').each(function(index) {
		   var notSelectedIndex = ebTecAcceptPaymentInputs.index($(this));
		   if(notSelectedIndex >= 0)
			 $('.eb-tec-payment-instructions:eq('+notSelectedIndex+')').slideUp(200)
		});
		} else {
			$.each('#eb-tec-payment-options-checkboxes ~ #eb-tec-payment-options div', function() {
				var thisInput = $(this).find('input');
				if(thisInput.val() != null) {
					thisInput.closest('div').slideDown(200);
					$(".tec-eb-offline-pay-options").show();
				}
			});
		}
	$('.eb-tec-payment-details td').css('display', $('#eb-tec-payment-options-checkboxes input:checked').not('#EventBritePayment_accept_online-none').size() > 0 ? 'table-cell' : 'none');
	}

	ebTecAcceptPaymentInputs.bind('focus click', ebTecShowHideAdditionalPaymentOptions);
	ebTecAcceptPaymentInputs.focus();
	$('#title').focus();

	// Define error checking routine on submit
	$("form[name='post']").submit(function() {
			var EventStartDate = $("#EventStartDate").val();

			var currentDate = new Date();
			var EventDate = new Date();
			if( $("input[name='EventRegister']:checked").val() == 'yes' &&  (typeof( EventStartDate ) == 'undefined' || !EventStartDate.length || EventDate.toDateString() < currentDate.toDateString())) {
				alert("<?php esc_attr_e( 'Eventbrite only allows events to be saved that start in the future.', 'tribe-eventbrite' ) ?>");

				$('#EventStartDate').focus();
				return false;
			}

	});

	$("form[name='post']").submit(function() {
		var ticket_name = $("input[name='EventBriteTicketName']").val();
		if( $("#EventBriteToggleOn").attr('checked') == true && typeof( ticket_name ) != 'undefined' ) {
			var ticket_price = $("input[name='EventBriteEventCost']").val();
			var ticket_quantity = $("input[name='EventBriteTicketQuantity']").val();
			var is_donation = $("input[name='EventBriteIsDonation']:checked").val();
			if( typeof( ticket_name ) == 'undefined' || !ticket_name.length ) {
				alert("<?php esc_attr_e( 'Please provide a ticket name for the Eventbrite ticket.', 'tribe-eventbrite' ); ?>");
				$("input[name='EventBriteTicketName']").focus();
				return false;
			}
			if( !ticket_price.length && !is_donation) {
				alert("<?php esc_attr_e( 'You must set a price for the ticket', 'tribe-eventbrite' ); ?>" + ticket_name);
				$("input[name='EventBriteEventCost']").focus();
				return false;
			}
			if( (parseInt(ticket_quantity) == 0 || isNaN(parseInt(ticket_quantity) ) ) ) {
				alert("<?php esc_attr_e( 'Ticket quantity is not a number', 'tribe-eventbrite' ); ?>");
				$("input[name='EventBriteTicketQuantity']").focus();
				return false;
			}
			if( $('input[name="EventBritePayment_accept_paypal"]').is(':checked') ) {
				var emailField = $('input[name="EventBritePayment_paypal_email"]');
				if( !emailField.val().length ) {
					alert("<?php esc_attr_e( 'A Paypal email address must be provided.', 'tribe-eventbrite' ); ?>");
					emailField.focus();
					return false;
				}
			}
			return true;
		}
	});

	var $event_details = $( document.getElementById( 'tribe_events_event_details' ) );

	// Runs right before the datepicker div is rendered.
	$event_details.on( 'tribe.ui-datepicker-div-beforeshow', function( e, object ) {

		$dpDiv = $( object.dpDiv );

		// if eb datepicker set min and max date
		if ( $( object.input ).hasClass( 'etp-datepicker' ) ) {
			var startDate = $( document.getElementById( 'EventStartDate' ) ).val();

			if ( startDate ) {
				object.input.datepicker( 'option', 'minDate', new Date() );
				object.input.datepicker( 'option', 'maxDate', startDate );
			}
		}
	});

	// If the parent event's start date is adjusted, set the default Start Ticket Sale Date to that value.
	$( document.getElementById( 'EventStartDate' ) ).on( 'change', function( e ) {
		var parent_event_start_date = $(this).val();
		if ( parent_event_start_date ) {
			$( 'input[name="EventBriteTicketEndDate"]' ).val( parent_event_start_date );
		}
	});

}); // end document ready
</script>

<?php do_action( 'tribe_eventbrite_meta_box_top' ); ?>
<tr>
	<td colspan="2" class="tribe_sectionheader">
		<?php do_action( 'tribe_eventbrite_before_integration_header' ); ?>
		<h4><?php esc_html_e( 'Tickets', 'tribe-eventbrite' );?>
	</td>
</tr>

<?php if ( isset( $event_deleted ) && $event_deleted ) : ?>
	<div id='eventBriteDraft' class='error'>
	<p><?php esc_html_e( 'This event has been deleted from Eventbrite. It is now unregistered from Eventbrite.', 'tribe-eventbrite' ); ?></p>
	</div>
<?php endif; ?>

<?php if ( $_EventBriteId && empty( $event->is_owner ) ) : ?>
	<tr>
		<td colspan="2">
			<div class="tribe-eb-msg">
			<?php
				echo sprintf(
					'%1$s %2$s%3$s%4$s',
						esc_html__( 'This event was imported from Eventbrite.', 'tribe-eventbrite' ),
						'<a href="' . esc_url( $event->url ) . '" title="' . esc_attr( get_the_title() ) . '" target="_blank">',
						esc_html__( 'View Event', 'tribe-eventbrite' ),
						'</a>'
					);
			?>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<div class="tribe-eb-notice"><?php esc_html_e( 'You are not the owner of this event listing on Eventbrite.com.', 'tribe-eventbrite' ); ?></div>
			<div class="tribe-eb-notice"><?php esc_html_e( 'Content changes made to the event here will not impact the listing on Eventbrite.com.', 'tribe-eventbrite' ); ?></div>
			<input type='hidden' name='eventbrite_import_not_owner' value='1' />
		</td>
	</tr>
	<tr>
		<td><?php esc_html_e( 'Display tickets on event page?', 'tribe-eventbrite' ); ?></td>
		<td>
			<input id='EventBriteShowOn' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventShowTickets' value='yes' <?php checked( $_EventShowTickets, 'yes' ); ?> />&nbsp;<b><?php esc_attr_e( 'Yes', 'tribe-eventbrite' ); ?></b>
			<input id='EventBriteShowOff' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventShowTickets' value='no' <?php checked( $_EventShowTickets, 'no' ); ?>/>&nbsp;<b><?php esc_attr_e( 'No', 'tribe-eventbrite' ); ?></b>
		</td>
	</tr>
	<?php
	return;
endif;
?>

	<tr>
		<td>
			<?php if ( ! $_EventBriteId ) { ?>
				<?php esc_html_e( 'Register this event with eventbrite.com?', 'tribe-eventbrite' );?>
			<?php }else{ ?>
				<?php esc_html_e( 'Leave this event associated with eventbrite.com?', 'tribe-eventbrite' );?>
			<?php } ?>
		</td>
		<td>
			<input id='EventBriteToggleOn' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventRegister' value='yes' <?php checked( $_EventRegister, 'yes' ); ?> />&nbsp;<b><?php esc_attr_e( 'Yes', 'tribe-eventbrite' ); ?></b>
			<input id='EventBriteToggleOff' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventRegister' value='no' <?php checked( $_EventRegister, 'no' ); ?>/>&nbsp;<b><?php esc_attr_e( 'No', 'tribe-eventbrite' ); ?></b>
		</td>
	</tr>
	<?php if ( $_EventBriteId ){?>
	<tr>
		<td><?php esc_html_e( 'Display tickets on event page?', 'tribe-eventbrite' );?></td>
		<td>
			<input id='EventBriteShowOn' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventShowTickets' value='yes' <?php checked( $_EventShowTickets, 'yes' ); ?> />&nbsp;<b><?php esc_attr_e( 'Yes', 'tribe-eventbrite' ); ?></b>
			<input id='EventBriteShowOff' tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='radio' name='EventShowTickets' value='no' <?php checked( $_EventShowTickets, 'no' ); ?>/>&nbsp;<b><?php esc_attr_e( 'No', 'tribe-eventbrite' ); ?></b>
		</td>
	</tr>
 <?php } ?>
	<?php if ( ! $_EventBriteId ) :?>
	<?php if ( function_exists( 'tribe_is_recurring_event' ) ): ?>
	   <tr><td class="tribe-eb-warning" colspan='2'><?php esc_html_e( 'Note: The Eventbrite API does not yet support recurring events, so all instances of recurring events will be associated with a single Eventbrite event.', 'tribe-eventbrite' ) ?></td></tr>
	 <?php endif; ?>
	<?php if ( function_exists( 'tribe_is_recurring_event' ) ): ?>
	   <tr><td class="tribe-eb-warning" colspan='2'><?php esc_html_e( 'Note: Eventbrite requires you enter an organizer. If you neglect to enter an organizer, your display name will be passed as the organizer name to Eventbrite.', 'tribe-eventbrite' ) ?></td></tr>
	 <?php endif; ?>

	<tr class="EBForm">
		<td colspan="2" class="snp_sectionheader">
			<h4>
				<?php esc_html_e( 'Set up your first ticket', 'tribe-eventbrite' ); ?>
				<small style="text-transform:none; display:block; margin-top:8px; font-weight:normal;">
					<?php esc_html_e(
						'To create multiple tickets per event, submit this form, then follow the link to Eventbrite.',
						'tribe-eventbrite'
					); ?>
				</small>
			</h4>
		</td>
	</tr>
	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Name', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<input
				tabindex="<?php $tribe_ecp->tabIndex(); ?>"
				type="text"
				name="EventBriteTicketName"
				size="14"
				value="<?php echo esc_attr( $_EventBriteTicketName ); ?>"
			/>
		</td>
	</tr>
	<tr class="EBForm">
		<td></td>
		<td class="snp_message"><small><?php esc_html_e( 'Examples: Member, Non-member, Student, Early Bird', 'tribe-eventbrite' ); ?></small></td>
	</tr>
	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Description', 'tribe-eventbrite' ); ?>:
		</td>
		<td>
			<textarea class="description_input" tabindex="<?php $tribe_ecp->tabIndex(); ?>" name="EventBriteTicketDescription" 	rows="2" cols="55"><?php echo esc_attr( $_EventBriteTicketDescription ); ?></textarea>
		</td>
	</tr>
	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Date to Start Ticket Sales', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="text" name="EventBriteTicketStartDate" value='<?php echo esc_attr( $_EventBriteTicketStartDate ); ?>' class='tribe-datepicker etp-datepicker'/>
			@
			<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketStartHours' class="tribe-dropdown">
				<?php echo Tribe__View_Helpers::getHourOptions( '', true ); ?>
			</select>
			<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketStartMinutes' class="tribe-dropdown">
				<?php echo Tribe__View_Helpers::getMinuteOptions( '00:00:00' ); ?>
			</select>

			<?php if ( ! strstr( get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ), 'H' ) ) : ?>
				<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketStartMeridian' class="tribe-dropdown">
					<?php echo Tribe__View_Helpers::getMeridianOptions( '', true ); ?>
				</select>
			<?php endif; ?>
		</td>
	</tr>
	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Date to End Ticket Sales', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="text" name="EventBriteTicketEndDate" value='<?php echo esc_attr( $_EventBriteTicketEndDate ); ?>' class='tribe-datepicker etp-datepicker'/>
			@
			<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketEndHours' class="tribe-dropdown">
				<?php echo Tribe__View_Helpers::getHourOptions( '', false ); ?>
			</select>
			<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketEndMinutes' class="tribe-dropdown">
			  <?php echo Tribe__View_Helpers::getMinuteOptions( '00:00:00' ); ?>
			</select>

			<?php if ( ! strstr( get_option( 'time_format', Tribe__Date_Utils::TIMEFORMAT ), 'H' ) ) : ?>
				<select tabindex='<?php $tribe_ecp->tabIndex(); ?>' name='EventBriteTicketEndMeridian' class="tribe-dropdown">
					<?php echo Tribe__View_Helpers::getMeridianOptions( '', false ); ?>
				</select>
			<?php endif; ?>
		</td>
	</tr>

	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Type', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<span class="tec-radio-option" ><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="radio" name="EventBriteIsDonation" value="0" <?php checked( ! isset( $_EventBriteIsDonation ) || 0 === (int) $_EventBriteIsDonation ) ?> /><?php esc_attr_e( ' Set Price', 'tribe-eventbrite' ); ?></span>
			<br/>
			<span class="tec-radio-option" ><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="radio" name="EventBriteIsDonation" value="1" <?php checked( 1 === (int) $_EventBriteIsDonation ) ?> /><?php esc_attr_e( ' Donation Based', 'tribe-eventbrite' ); ?></span>
			<br/>
			<span class="tec-radio-option" ><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="radio" name="EventBriteIsDonation" value="2" <?php checked( 2 === (int) $_EventBriteIsDonation ) ?> /><?php esc_attr_e( ' Free', 'tribe-eventbrite' ); ?></span>
		</td>
	</tr>
	<tr class="EBForm">
		<td><?php esc_html_e( 'Cost', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span></td>
		<td><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='text' id='EventBriteEventCost' name='EventBriteEventCost' size='6' value='<?php echo esc_attr( ! empty( $_EventBriteEventCost ) ? $_EventBriteEventCost : '' ); ?>' /></td>
	</tr>
	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Quantity', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type='text' name='EventBriteTicketQuantity' size='14' value='<?php echo esc_attr( $_EventBriteTicketQuantity ); ?>' />
		</td>
	<tr  class="EBForm  eb-tec-payment-options">
		<td>
			<?php esc_html_e( 'Fee', 'tribe-eventbrite' ); ?>:<span class="tec-required">✳</span>
		</td>
		<td>
			<span class="tec-radio-option" ><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="radio" class="radio" name="EventBriteIncludeFee" value="0" <?php checked( empty( $_EventBriteIncludeFee ) || 0 === $_EventBriteIncludeFee ) ?> /> <?php esc_attr_e( 'Add Service Fee on top of price', 'tribe-eventbrite' ); ?></span>
			<br/>
			<span class="tec-radio-option"><input tabindex="<?php $tribe_ecp->tabIndex(); ?>" type="radio" class="radio" name="EventBriteIncludeFee" value="1" <?php checked( 1 === $_EventBriteIncludeFee ) ?> /><?php esc_attr_e( ' Include Service fee in price', 'tribe-eventbrite' ); ?></span>
		</td>
	</tr>
	<tr  class="EBForm">
		<td colspan="2" class="snp_sectionheader">
		<h4><?php esc_html_e( 'Publish this post to create the Event with Eventbrite.com', 'tribe-eventbrite' );?></h4>
		<div><p><?php _e( 'When you <strong>publish</strong> this post, an event will be created for you at Eventbrite. Before you publish, you can choose whether this event will save as a draft or live event using the Eventbrite status below.', 'tribe-eventbrite' ) ?></p></div>
		</td>
	</tr>

	<tr class="EBForm">
		<td>
			<?php esc_html_e( 'Eventbrite Event Status', 'tribe-eventbrite' ); ?>:
		</td>
		<td>
		<?php if ( $tribe_ecp ) : ?>
			<select name="EventBriteStatus" tabindex="<?php $tribe_ecp->tabIndex(); ?>" class="tribe-dropdown">
		<?php else : ?>
				<select name="EventBriteStatus" class="tribe-dropdown">
		<?php endif; ?>
				<option value='draft'><?php echo esc_html( _x( 'Draft', 'Eventbrite status', 'tribe-eventbrite' ) ); ?></option>
				<option value='live'><?php echo esc_html( _x( 'Live', 'Eventbrite status', 'tribe-eventbrite' ) ); ?></option>
			</select>
		</td>
	</tr>

	<tr class="EBForm">
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

	<?php else : // have eventbrite id ?>
		<?php include( 'eventbrite-events-table.php' ); ?>
	<?php endif; // !$_EventBriteId
