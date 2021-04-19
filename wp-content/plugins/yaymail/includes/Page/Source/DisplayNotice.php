<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin = ( isset( $sent_to_admin ) ? true : false );
if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
	?>
		<li><?php esc_html_e( 'Woocommerce Subscriptions (PRO)', 'yaymail' ); ?></li>
	<?php
}

if ( is_plugin_active( 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php' ) ) {
	?>
		<li><?php esc_html_e( 'Woocommerce Shipment Tracking (PRO)', 'yaymail' ); ?></li>
	<?php
}

// if ( is_plugin_active( 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php' ) ) {
// <li>Woocommerce Custom Order Status (PRO)</li>
// }

if ( is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' ) ) {
	?>
		<li><?php esc_html_e( 'Woocommerce Order Status Manager (PRO)', 'yaymail' ); ?></li>
	<?php
}

if ( is_plugin_active( 'woo-advanced-shipment-tracking/woo-advanced-shipment-tracking.php' )
	|| is_plugin_active( 'yith-woocommerce-subscription-premium/init.php' )
	|| is_plugin_active( 'yith-woocommerce-subscription/init.php' )
	) {
	?>
		<li><?php esc_html_e( 'Woocommerce Custom Email Tempaltes (PRO)', 'yaymail' ); ?></li>
	<?php
}

if ( is_plugin_active( 'woocommerce-admin-custom-order-fields/woocommerce-admin-custom-order-fields.php' ) ) {
	?>
		<li><?php esc_html_e( 'Woocommerce Admin Custom Order fields (PRO)', 'yaymail' ); ?></li>
	<?php
}




