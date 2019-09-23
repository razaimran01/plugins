let tribe_events_community_tickets_admin = {
	event : {}
};

(function ( $, obj ) {
	"use strict";
	obj.init = function () {
		this.$site_fee_type = $( 'select[name="site_fee_type"]' );
		this.$enable_split_payments = $( 'input[name="enable_split_payments"]' );
		this.$form = this.$site_fee_type.closest( "form" );

		$( document ).on( "change", 'select[name="site_fee_type"]', this.event.change_site_fee );
		$( document ).on( "change", 'input[name="enable_split_payments"]', this.event.change_split_payments );

		this.$site_fee_type.trigger( "change" );
		this.$enable_split_payments.trigger( "change" );
	}, obj.change_site_fee = function ( fee_value ) {
		this.$form.removeClass( "site-fee-none site-fee-flat site-fee-percentage" );

		if ( "none" === fee_value ) {
			this.$form.addClass( "site-fee-none" );
		} else if ( "flat" === fee_value ) {
			this.$form.addClass( "site-fee-flat" );
		} else if ( "percentage" === fee_value ) {
			this.$form.addClass( "site-fee-percentage" );
		} else if ( "flat-and-percentage" === fee_value ) {
			this.$form.addClass( "site-fee-flat site-fee-percentage" );
		}
	}, obj.change_split_payments = function ( enabled ) {
		if ( enabled ) {
			this.$form.addClass( "split-payments-enabled" );
		} else {
			this.$form.removeClass( "split-payments-enabled" );
		}
	}, obj.event.change_site_fee = function () {
		obj.change_site_fee( $( this ).val() );
	}, obj.event.change_split_payments = function () {
		obj.change_split_payments( $( this ).is( ":checked" ) );
	}, $( function () {
		obj.init();
	} );

})( jQuery, tribe_events_community_tickets_admin );
