<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin      = ( isset( $sent_to_admin ) ? true : false );
$nextDayActive      = date_i18n( wc_date_format(), get_post_meta( $subscription, 'payment_due_date', true ) );
$orderIds           = get_post_meta( $subscription, 'order_ids', true );
$nextActivity       = ywsbs_get_suspension_time() ? __( 'suspended', 'yith-woocommerce-subscription' ) : __( 'cancelled', 'yith-woocommerce-subscription' );
$next_activity_date = false != get_post_meta( $subscription, 'payment_due_date', true ) ? (int) get_post_meta( $subscription, 'payment_due_date', true ) + ywsbs_get_overdue_time() : 0;
$subscriptionStatus = get_post_meta( $subscription, 'status', true );
$beforeText         = esc_html__( 'Hi ', 'woocommerce' ) . esc_html( $order->data['billing']['first_name'] ) . wp_kses_post( ',<br><br>' );
$headerContent      = '';
$headerContent      = '<span style="color: inherit;font-size: 14px;" class="yaymail_subscription_header">';
$headerContent     .= $beforeText;
if ( 'ywsbs_customer_subscription_expired' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' has expired.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_before_expired' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' is going to expire.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_suspended' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' has been suspended.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_cancelled' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' has been cancelled.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_paused' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' has been paused.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_resumed' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' has been resumed.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_renew_reminder' == $this->template ) {
	$headerContent .= esc_html__( 'Subscription #', 'woocommerce' ) . $subscription . esc_html__( ' is going to renew on ', 'woocommerce' ) . $nextDayActive;
}
if ( 'ywsbs_customer_subscription_request_payment' == $this->template ) {
	$headerContent .= esc_html__( 'Your recent subscription renewal order on ', 'woocommerce' ) . wp_kses_post( get_option( 'blogname' ) ) . esc_html__( ' is late for payment.', 'woocommerce' ) . wp_kses_post( '<br>' );
	// translators: 1. date of event, 2 is the subscription id, 3 next subscription status.
	$headerContent .= sprintf( wp_kses_post( __( 'If you do not pay it by <strong>%1$s</strong>, your subscription #%2$d will be <strong>%3$s</strong>.', 'yith-woocommerce-subscription' ) ), esc_html( date_i18n( wc_date_format(), $next_activity_date ) ), esc_html( $subscription ), esc_html( $nextActivity ) );
}
if ( 'ywsbs_customer_subscription_payment_failed' == $this->template ) {
	$headerContent .= esc_html__( 'The payment to renew your subscription failed. Please, verify your available funds for the card specified during subscription and/or verify that your card is not expired.', 'woocommerce' );
}
if ( 'ywsbs_customer_subscription_payment_done' == $this->template ) {
	if ( is_array( $orderIds ) && count( $orderIds ) > 1 ) {
		$headerContent .= esc_html__( 'Your subscription renewal order is now being processed. Your order details are shown below for your reference: ', 'woocommerce' ) . wp_kses_post( get_option( 'blogname' ) );
	} else {
		$headerContent .= esc_html__( 'Your subscription order is now being processed. Your order details are shown below for your reference: ', 'woocommerce' ) . wp_kses_post( get_option( 'blogname' ) );
	}
}
if ( 'ywsbs_subscription_admin_mail' == $this->template ) {
	// translators: placeholder 1 subscription id 2 new status.
	$headerContent .= sprintf( wp_kses_post( _x( 'The status of subscription #%1$d has changed to <strong>%2$s</strong>', 'placeholder 1 subscription id, 2 new status', 'yith-woocommerce-subscription' ) ), esc_html( $subscription ), esc_html( $subscriptionStatus ) );
}
$headerContent .= '</span>';
echo wp_kses_post( $headerContent );

