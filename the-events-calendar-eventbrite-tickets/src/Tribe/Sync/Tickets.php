<?php


/**
 * EventBrite Ticket Sync
 *
 * @since 4.5
 *
 */
class Tribe__Events__Tickets__Eventbrite__Sync__Tickets {

	public function sync_ticket( $post, $args = array() ) {
		$post = get_post( $post );

		if ( ! is_object( $post ) || ! $post instanceof WP_Post ) {
			return array();
		}

		$args = (object) wp_parse_args( $args, array(
			'name'        => '',
			'description' => '',
			'start'       => '',
			'end'         => '',
			'cost'        => 0,
			'currency'    => 'USD',
			'qty'         => 0,
			'type'        => 0,
			'include_fee' => false,
		) );

		// Prepare and Sanitize
		if ( ! empty( $args->name ) ) {
			$args->name = tribe( 'eventbrite.sync.utilities' )->string_prepare( wp_kses( $args->name, array() ) );
		}

		if ( isset( $args->start ) ) {
			$args->start = intval( $args->start );
		}

		if ( isset( $args->end ) ) {
			$args->end = intval( $args->end );
		}

		if ( isset( $args->cost ) ) {
			$args->cost = absint( $args->cost );
		}

		if ( isset( $args->currency ) ) {
			$args->currency = wp_strip_all_tags( $args->currency, true );
		}

		if ( isset( $args->qty ) ) {
			$args->qty = absint( $args->qty );
		}

		if ( isset( $args->type ) ) {
			$args->type = absint( $args->type );
		}

		if ( isset( $args->include_fee ) ) {
			$args->include_fee = (bool) $args->include_fee;
		}

		$params = array(
			'ticket_class.name'           => tribe( 'eventbrite.sync.utilities' )->string_prepare( $args->name ),
			'ticket_class.description'    => tribe( 'eventbrite.sync.utilities' )->string_prepare( $args->description ),
			'ticket_class.quantity_total' => $args->qty,
			'ticket_class.sales_start'    => date( tribe( 'eventbrite.sync.utilities' )->date_format, $args->start ),
			'ticket_class.sales_end'      => date( tribe( 'eventbrite.sync.utilities' )->date_format, $args->end ),
			'ticket_class.auto_hide'      => false,
		);

		if ( empty( $args->description ) ) {
			$params['ticket_class.hide_description'] = true;
		}

		if ( 0 === $args->type && ! empty( $args->cost ) && 0 != $args->cost ) {
			$params['ticket_class.cost'] = "{$args->currency},{$args->cost}";
		} elseif ( 1 === $args->type ) {
			$params['ticket_class.donation'] = true;
		} elseif ( 2 === $args->type ) {
			$params['ticket_class.free'] = true;
		}

		if ( $args->include_fee ) {
			$params['ticket_class.include_fee'] = true;
		} else {
			$params['ticket_class.include_fee'] = false;
		}

		return $params;
	}
}
