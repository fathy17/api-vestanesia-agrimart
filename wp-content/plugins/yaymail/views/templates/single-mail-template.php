<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use YayMail\Page\Source\CustomPostType;
use YayMail\Ajax;
$flag_do_action = false;
if ( isset( $args['yith_wc_email'] ) && isset( $args['yith_wc_email']->id ) && ! empty( $args['yith_wc_email']->id ) ) {
	// Get Email ID in yith-woocommerce-multi-vendor-premium
	$template = $args['yith_wc_email']->id;
} else {
	$template = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;
	if ( 'emails/waitlist-mailout.php' == $template_name ) {
		$template = 'woocommerce_waitlist_mailout';
	}
	if ( 'emails/waitlist-left.php' == $template_name ) {
		$template = 'woocommerce_waitlist_left_email';
	}
	if ( 'emails/waitlist-joined.php' == $template_name ) {
		$template = 'woocommerce_waitlist_joined_email';
	}
	if ( 'emails/waitlist-new-signup.php' == $template_name ) {
		$template = 'woocommerce_waitlist_signup_email';
	}
}
$custom_shortcode = new YayMail\MailBuilder\Shortcodes( $template );
if ( CustomPostType::postIDByTemplate( $template ) ) {
	$postID = CustomPostType::postIDByTemplate( $template );
}

switch ( $template ) {
	case 'qwc_req_new_quote':
	case 'qwc_request_sent':
	case 'qwc_send_quote':
		$args['order'] = new WC_Order( $args['order']->order_id );
		break;
	default:
		break;
}
$checkIsSumoTemp = strpos( get_class( $args['email'] ), 'SUMOSubscriptions' );
$checkIsQWCTemp  = strpos( get_class( $args['email'] ), 'QWC' );
if ( ( false === $checkIsSumoTemp ) && ( false === $checkIsQWCTemp ) && isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) && isset( $args['order'] ) && $args['order']->get_id() ) {
	$flag_do_action = true;
	$custom_shortcode->setOrderId( $args['order']->get_id(), $args['sent_to_admin'], $args );
	if ( isset( $args['sent_to_admin'] ) ) {
		if ( 1 === $args['order']->get_id() && false === $args['sent_to_admin'] ) {
			$custom_shortcode->shortCodesOrderSample();
		} else {
			$custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'] );
		}
	} else {
		$custom_shortcode->shortCodesOrderDefined();
	}
} elseif ( $template ) {
	$flag_do_action = true;
	$arrData        = array( $custom_shortcode, $args, $template );
	do_action_ref_array( 'yaymail_addon_defined_shorcode', array( &$arrData ) );

	// if ( 'customer_new_account' === $args['email']->id || 'customer_new_account_activation' === $args['email']->id || 'customer_reset_password' === $args['email']->id ) {
	// $custom_shortcode->setOrderId( 0, $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( 'cancelled_subscription' === $args['email']->id || 'expired_subscription' === $args['email']->id || 'suspended_subscription' === $args['email']->id ) {
	// $custom_shortcode->setOrderId( $args['subscription']->data['parent_id'], $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( 'ywsbs_subscription_admin_mail' === $args['email']->id || 'ywsbs_customer_subscription_cancelled' === $args['email']->id || 'ywsbs_customer_subscription_suspended' === $args['email']->id || 'ywsbs_customer_subscription_expired' === $args['email']->id || 'ywsbs_customer_subscription_before_expired' === $args['email']->id || 'ywsbs_customer_subscription_paused' === $args['email']->id || 'ywsbs_customer_subscription_resumed' === $args['email']->id || 'ywsbs_customer_subscription_request_payment' === $args['email']->id || 'ywsbs_customer_subscription_renew_reminder' === $args['email']->id || 'ywsbs_customer_subscription_payment_done' === $args['email']->id || 'ywsbs_customer_subscription_payment_failed' === $args['email']->id ) {
	// $custom_shortcode->setOrderId( $args['subscription']->order->id, $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateYITHCommissions ) ) {
	// if ( isset( $args['commissions'] ) && isset( $args['commissions']->order_id ) ) {
	// $custom_shortcode->setOrderId( $args['commissions']->order_id, $args['sent_to_admin'], $args );
	// }
	// if ( isset( $args['commission'] ) && isset( $args['commission']->order_id ) ) {
	// $custom_shortcode->setOrderId( $args['commission']->order_id, $args['sent_to_admin'], $args );
	// }
	// if ( isset( $args['order_number'] ) ) {
	// $custom_shortcode->setOrderId( $args['order_number'], $args['sent_to_admin'], $args );
	// }
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateGermanizedForWC ) ) {
	// $custom_shortcode->setOrderId( $args['document']->get_order()->get_order()->id, $args['sent_to_admin'], $args );
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateYITHWishlist ) ) {
	// $custom_shortcode->shortCodesOrderDefined( $args['sent_to_admin'], $args, 'not_order' );
	// $html = do_shortcode( $html );
	// }
	// if ( in_array( $template, $templateWooBookings ) ) {
	// $custom_shortcode->setOrderId( $args['booking']->data['order_id'], false, $args );
	// $custom_shortcode->shortCodesOrderDefined( false, $args );
	// $html = do_shortcode( $html );
	// }
}

if ( $flag_do_action ) {
	$Ajax           = Ajax::getInstance();
	$htmlByElements = $Ajax->getHtmlByElements( $postID, $args );
	// Replace shortcode cannot do_shortcode
	$reg            = '/\[yaymail.*?\]/m';
	$htmlByElements = preg_replace( $reg, '', $htmlByElements );
	echo ( $htmlByElements ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

