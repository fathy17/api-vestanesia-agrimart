<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin  = ( isset( $sent_to_admin ) ? true : false );
$beforeText     = esc_html__( 'Hi John', 'woocommerce' ) . wp_kses_post( ',<br><br>' );
$headerContent  = '';
$headerContent  = '<span style="color: inherit;font-size: 14px;" class="yaymail_subscription_header">';
$headerContent .= $beforeText;
if ( 'ywsbs_customer_subscription_expired' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 has expired.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_before_expired' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 is going to expire.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_suspended' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 has been suspended.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_cancelled' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 has been cancelled.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_paused' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 has been paused.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_resumed' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 has been resumed.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_renew_reminder' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #1 is going to renew.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_request_payment' == $this->template ) {
	$headerContent .= esc_html__( 'Your recent subscription renewal order on ', 'woocommerce' ) . wp_kses_post( get_option( 'blogname' ) ) . esc_html__( ' is late for payment.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_payment_failed' == $this->template ) {
	$headerContent .= esc_html__( 'The payment to renew your subscription failed. Please, verify your available funds for the card specified during subscription and/or verify that your card is not expired.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_payment_done' == $this->template ) {
	$headerContent .= esc_html__( 'Your subscription order is now being processed. Your order details are shown below for your reference: ', 'woocommerce' ) . wp_kses_post( get_option( 'blogname' ) );
}
if ( 'ywsbs_subscription_admin_mail' == $this->template ) {
	$headerContent .= esc_html__( 'The status of subscription #1 has changed.', 'woocommerce' );
}
$headerContent .= '</span>';
echo wp_kses_post( $headerContent );

