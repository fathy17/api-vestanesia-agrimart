<?php

namespace YayMail\Helper;

defined( 'ABSPATH' ) || exit;
class Helper {


	public static function checkNonce() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
			wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
		}
	}

	public static function sanitize_array( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'self::sanitize_array', $var );
		} else {
			return is_scalar( $var ) ? wp_kses_allowed_html( $var ) : $var;
		}
	}

	public static function unsanitize_array( $var ) {
		if ( is_array( $var ) ) {
			return array_map( 'self::unsanitize_array', $var );
		} else {
			return html_entity_decode( $var, ENT_QUOTES, 'UTF-8' );
		}
	}

	// Fix bugs for only Customer Invoice (core code of Woocommerce)
	public static function getCustomerInvoiceSubject( $objEmail ) {
		if ( $objEmail->object && $objEmail->object->has_status( array( 'completed', 'processing' ) ) ) {
			$subject = $objEmail->get_option( 'subject_paid', $objEmail->get_default_subject( true ) );
			return apply_filters( 'woocommerce_email_subject_customer_invoice_paid', $objEmail->format_string( $subject ), $objEmail->object, $objEmail );
		}

		$subject = $objEmail->get_option( 'subject', $objEmail->get_default_subject() );
		return apply_filters( 'woocommerce_email_subject_customer_invoice', $objEmail->format_string( $subject ), $objEmail->object, $objEmail );
	}

	// Get Subject for email WC_Email_New_Booking (Woo Bookings plugin)
	public static function getNewBookingSubject( $objEmail ) {
		$subject = $objEmail->get_option( 'subject', $objEmail->subject );
		return apply_filters( 'woocommerce_email_subject_' . $objEmail->id, $objEmail->format_string( $subject ), $objEmail->object );
	}

	// Check Key Exist
	public static function checkKeyExist( $array, $key, $valueDefault ) {
		if ( isset( $array->$key ) ) {
			return $key;
		} else {
			return $valueDefault;
		}
	}
}
