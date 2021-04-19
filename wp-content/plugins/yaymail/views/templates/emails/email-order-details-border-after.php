<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin = ( isset( $sent_to_admin ) ? true : false );
$email         = ( isset( $email ) ? $email : '' );
$plain_text    = ( isset( $plain_text ) ? $plain_text : '' );

// do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email);
