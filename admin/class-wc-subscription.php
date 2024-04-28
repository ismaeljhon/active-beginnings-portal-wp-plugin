<?php
class Comworks_WC_Subscription {
    public function __construct() {
        add_filter( 'woocommerce_subscription_period_interval_strings', array( $this, 'extend_subscription_period_intervals' ) );
    }

    function extend_subscription_period_intervals( $intervals ) {
    	$intervals[10] = sprintf( __( 'every %s', 'my-text-domain' ), WC_Subscriptions::append_numeral_suffix( 10 ) );
    	return $intervals;
    }
}

$wc_subscription = new Comworks_WC_Subscription();