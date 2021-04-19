<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YayMail\Page\Source\CustomPostType;

$template         = $args['email']->id;
$custom_shortcode = new YayMail\MailBuilder\Shortcodes( $template );

if ( CustomPostType::postIDByTemplate( $template ) ) {
	$postID = CustomPostType::postIDByTemplate( $template );
	$html   = get_post_meta( $postID, '_yaymail_html', true );
} else {
	$html = '';
}
if ( isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) && isset( $args['order'] ) && $args['order']->get_id() ) {
	$custom_shortcode->setOrderId( $args['order']->get_id(), $args['sent_to_admin'] );
	if ( isset( $args['sent_to_admin'] ) ) {
		$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'] );
	} else {
		$custom_shortcode->shortCodesOrderDefined();
	}
	$html = do_shortcode( $html );
} elseif ( isset( $args['email'] ) && isset( $args['email']->id ) ) {
	if ( 'customer_new_account' === $args['email']->id || 'customer_new_account_activation' === $args['email']->id || 'customer_reset_password' === $args['email']->id ) {
		$custom_shortcode->setOrderId( 0, $args['sent_to_admin'] );
		$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
		$html = do_shortcode( $html );
	}
	if ( 'cancelled_subscription' === $args['email']->id || 'expired_subscription' === $args['email']->id || 'suspended_subscription' === $args['email']->id ) {
		$custom_shortcode->setOrderId( $args['subscription']->data['parent_id'], $args['sent_to_admin'] );
		$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
		$html = do_shortcode( $html );
	}
	if ( 'ywsbs_subscription_admin_mail' === $args['email']->id || 'ywsbs_customer_subscription_cancelled' === $args['email']->id || 'ywsbs_customer_subscription_suspended' === $args['email']->id || 'ywsbs_customer_subscription_expired' === $args['email']->id || 'ywsbs_customer_subscription_before_expired' === $args['email']->id || 'ywsbs_customer_subscription_paused' === $args['email']->id || 'ywsbs_customer_subscription_resumed' === $args['email']->id || 'ywsbs_customer_subscription_request_payment' === $args['email']->id || 'ywsbs_customer_subscription_renew_reminder' === $args['email']->id || 'ywsbs_customer_subscription_payment_done' === $args['email']->id || 'ywsbs_customer_subscription_payment_failed' === $args['email']->id ) {
		$custom_shortcode->setOrderId( $args['subscription']->order->id, $args['sent_to_admin'] );
		$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
		$html = do_shortcode( $html );
	}
}

// Replace shortcode cannot do_shortcode
$reg  = '/\[yaymail.*?\]/m';
$html = preg_replace( $reg, '', $html );

echo html_entity_decode( $html, ENT_QUOTES, 'UTF-8' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
