<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin    = ( isset( $sent_to_admin ) ? true : false );
$subscriptionDate = gmdate( 'Y-m-d', get_post_meta( $subscription, 'start_date', true ) );

?>

<?php
if ( $sent_to_admin ) {
	$before = '<a style="color: inherit;" class="yaymail_builder_link" href="' . esc_url( get_edit_post_link( $subscription ) ) . '">';
	$after  = '</a>';
	echo wp_kses_post( $before . sprintf( __( '[Subscription #', 'woocommerce' ) . $subscription . ']' . $after . ' (<time datetime="%s">%s</time>)', $subscriptionDate, wc_format_datetime( $order->get_date_created() ) ) );
} else {
	$before = '';
	$after  = '';
	echo wp_kses_post( $before . sprintf( __( 'Subscription #', 'woocommerce' ) . $subscription . $after ) );
}
