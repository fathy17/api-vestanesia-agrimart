<?php
use YayMail\Page\Source\CustomPostType;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( false != $subscription ) :
	$productId       = get_post_meta( $subscription, 'product_id', true );
	$productName     = get_post_meta( $subscription, 'product_name', true );
	$quantity        = (float) get_post_meta( $subscription, 'quantity', true );
	$lineTotal       = (float) get_post_meta( $subscription, 'line_total', true );
	$orderCurrency   = get_post_meta( $subscription, 'order_currency', true );
	$lineSubTotal    = (float) get_post_meta( $subscription, 'line_subtotal', true );
	$lineTax         = false == get_post_meta( $subscription, 'line_tax', true ) ? (float) get_post_meta( $subscription, 'line_tax', true ) : 0;
	$postID          = CustomPostType::postIDByTemplate( $this->template );
	$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
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
			<a style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( get_permalink( $productId ) ); ?>"><?php echo wp_kses_post( $productName ); ?></a><?php echo ' x ' . esc_html( $quantity ); ?>
		</td>

		<td scope="col"
			style="text-align:left;"><?php echo wp_kses_post( wc_price( $lineTotal, array( 'currency' => $orderCurrency ) ) ); ?></td>
	</tr>

	</tbody>
	<tfoot>
	<?php if ( 0 !== $lineTax ) : ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( wc_price( $lineTax, array( 'currency' => $orderCurrency ) ) ); ?></td>
		</tr>
	<?php endif ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $lineTotal + $lineTax, array( 'currency' => $orderCurrency ) ) ); ?></td>
	</tr>

	<?php
	$subscriptions_shippings = get_post_meta( $subscription, 'order_shipping', true );
	if ( $subscriptions_shippings ) :
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
			<td>
			<?php
				// translators:placeholder shipping name.
				echo wp_kses_post( wc_price( $subscriptions_shippings, array( 'currency' => $orderCurrency ) ) );
			?>
				</td>
		</tr>
		<?php
		if ( ! empty( get_post_meta( $subscription, 'order_shipping_tax', true ) ) ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
				<td colspan="2"><?php echo wp_kses_post( wc_price( get_post_meta( $subscription, 'order_shipping_tax', true ), array( 'currency' => $orderCurrency ) ) ); ?></td>
			</tr>
			<?php
		endif;
	endif;
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
		<td colspan="2"><?php echo wp_kses_post( wc_price( get_post_meta( $subscription, 'subscription_total', true ), array( 'currency' => $orderCurrency ) ) ); ?></td>
	</tr>
	</tfoot>
</table>

<?php endif; ?>
