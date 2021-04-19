<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
$sent_to_admin         = ( isset( $sent_to_admin ) ? true : false );
$plain_text            = ( isset( $plain_text ) ? $plain_text : '' );
$email                 = ( isset( $email ) ? $email : '' );
$postID                = CustomPostType::postIDByTemplate( $this->template );
$order_item_title      = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
$product_title         = false != $order_item_title ? $order_item_title['product_title'] : 'Product';
$quantity_title        = false != $order_item_title ? $order_item_title['quantity_title'] : 'Quantity';
$price_title           = false != $order_item_title ? $order_item_title['price_title'] : 'Price';
$subtoltal_title       = false != $order_item_title ? $order_item_title['subtoltal_title'] : 'Subtotal:';
$payment_method_title  = false != $order_item_title ? $order_item_title['payment_method_title'] : 'Payment method:';
$total_title           = false != $order_item_title ? $order_item_title['total_title'] : 'Total:';
$get_order_item_totals = array(
	'cart_subtotal'  => $subtoltal_title,
	'payment_method' => $payment_method_title,
	'order_total'    => $total_title,
);

$get_order_item_totals_class = array(
	'cart_subtotal'  => 'yaymail_item_subtoltal_title',
	'payment_method' => 'yaymail_item_payment_method_title',
	'order_total'    => 'yaymail_item_total_title',
);

$borderColor = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor   = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';

?>


<!-- Table Items has Border -->
<!-- <table class="yaymail_builder_table_items_border" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;color: inherit;flex-direction:inherit;" width="100%">
	<thead> -->
		<tr style="word-break: normal">
			<th class="td yaymail_item_product_title" scope="col" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>">
				<?php esc_html_e( $product_title, 'woocommerce' ); ?>
			</th>
			<th class="td yaymail_item_quantity_title" scope="col" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>">
				<?php esc_html_e( $quantity_title, 'woocommerce' ); ?>
			</th>
			<th class="td yaymail_item_price_title" scope="col" style="width: 30%;text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>">
				<?php esc_html_e( $price_title, 'woocommerce' ); ?>
			</th>
		</tr>
	<!-- </thead> -->

	<!-- <tbody style="flex-direction:inherit;"> -->
		<?php
		echo wp_kses_post(
			$this->ordetItemTables(
				$order,
				array(
					'show_sku'      => $sent_to_admin,
					'show_image'    => false,
					'image_size'    => array( 32, 32 ),
					'plain_text'    => $plain_text,
					'sent_to_admin' => $sent_to_admin,
					'border_color'  => $borderColor,
					'text_color'    => $textColor,
				)
			)
		);

		// customer processing order
		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product       = $item->get_product();
			$purchase_note = $product->get_purchase_note();
		}

		if ( 'customer_processing_order' === $this->template && ! empty( $purchase_note ) ) {
			?>

			<tr>
				<td class="td" scope="row" colspan="3" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?>">
					<?php esc_html_e( $purchase_note, 'woocommerce' ); ?>
				</td>
			</tr>

		<?php } ?>
	<!-- </tbody>

	<tfoot> -->
		<?php
		$totalItem = $order->get_order_item_totals();
		$i         = 0;
		foreach ( $totalItem as $key => $total ) {
			$i++;
			?>

		<tr>
			<th class="td 
			<?php
			if ( array_key_exists( $key, $get_order_item_totals_class ) ) {
				echo esc_html( $get_order_item_totals_class[ $key ] );}
			?>
			" scope="row" colspan="2" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?> <?php echo esc_attr( ( 1 === $i ) ? ';border-top-width: 4px;' : '' ); ?>">
				<?php
				if ( array_key_exists( $key, $get_order_item_totals ) ) {
					echo esc_html_e( $get_order_item_totals[ $key ], 'woocommerce' );
				} else {
					echo esc_html_e( $total['label'], 'woocommerce' );
				}
				?>
			</th>
			<td class="td" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?> <?php echo esc_attr( ( 1 === $i ) ? ';border-top-width: 4px;' : '' ); ?>">
			<?php echo wp_kses_post( $total['value'] ); ?>
			</td>
		</tr>

			<?php
		}

		if ( ! empty( $order->get_customer_note() ) ) {
			$note = $order->get_customer_note();
			?>

			<tr>
				<th class="td" scope="row" colspan="2" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?> <?php echo esc_attr( ( 1 === $i ) ? ';border-top-width: 4px;' : '' ); ?>">
			<?php esc_html_e( 'Note:', 'woocommerce' ); ?>
				</th>
				<td class="td" style="text-align:left;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( $borderColor ); ?> <?php echo esc_attr( ( 1 === $i ) ? ';border-top-width: 4px;' : '' ); ?>">
			<?php echo esc_html( $note ); ?>
				</td>
			</tr>

		<?php } ?>
	<!-- </tfoot>
</table> -->
