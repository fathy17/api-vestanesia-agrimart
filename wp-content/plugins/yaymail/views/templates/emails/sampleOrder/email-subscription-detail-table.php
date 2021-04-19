<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table class="yaymail_builder_table_items_border yaymail_builder_table_item_subscription" cellspacing="0" cellpadding="6" style="width: 100%;" border="1">
	<thead>
	<tr>
		<th scope="col"
			style="text-align:left;"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
		<th scope="col"
			style="text-align:left;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td scope="col" style="text-align:left;">
			<a href="#"> <?php esc_html_e( 'Happy YayCommerce', 'yaymail' ); ?> </a> <?php esc_html_e( ' x 1', 'yaymail' ); ?>
		</td>

		<td scope="col"
			style="text-align:left;"><?php esc_html_e( '£18.00', 'yaymail' ); ?></td>
	</tr>

	</tbody>
	<tfoot>
		<tr>
			<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php esc_html_e( '£0', 'yaymail' ); ?></td>
		</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php esc_html_e( '£18.00', 'yaymail' ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php esc_html_e( '£0', 'yaymail' ); ?></td>
	</tr>

	<tr>
		<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
		<td colspan="2"><?php esc_html_e( '£0', 'yaymail' ); ?></td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
		<td colspan="2"><?php esc_html_e( '£18.00', 'yaymail' ); ?></td>
	</tr>
	</tfoot>
</table>

