<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$text_align         = is_rtl() ? 'right' : 'left';
$yaymail_settings   = get_option( 'yaymail_settings' );
$orderImagePostions = isset( $yaymail_settings['image_position'] ) && ! empty( $yaymail_settings['image_position'] ) ? $yaymail_settings['image_position'] : 'Top';
$orderImage         = isset( $yaymail_settings['product_image'] ) && '0' != $yaymail_settings['product_image'] ? $yaymail_settings['product_image'] : '0';

foreach ( $items as $item_id => $item ) :
	if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						$product = $item->get_product();
		?>
		<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;word-wrap:break-word;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>">
		<?php

		if ( 'Bottom' == $orderImagePostions && '1' == $orderImage ) {
			echo ( '<div class="yaymail-product-text" style="padding: 5px 0;">' );
			// Product name
			echo wp_kses_post( '<span class="yaymail-product-name" data-sku=' . $product->get_sku() . '>' . $item->get_name() . '</span>' );

			// SKU
			if ( $args['show_sku'] && is_object( $product ) && $product->get_sku() ) {
				echo wp_kses_post( '<span class="yaymail-product-sku"> (#' . $product->get_sku() . ')</span>' );
			}
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $args['plain_text'] );

				// Display item meta data.
				wc_display_item_meta( $item );

				echo ( '</div>' );
			// Show title/image etc
			if ( $args['show_image'] && is_object( $product ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<div class="yaymail-product-image" style="margin-bottom: 5px"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( str_replace( 'px', '', $args['image_size'][1] ) ) . '" width="' . esc_attr( str_replace( 'px', '', $args['image_size'][0] ) ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item ) );
			}
		} else {
			// Show title/image etc
			if ( $args['show_image'] && is_object( $product ) ) {
				echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', '<div class="yaymail-product-image" style="margin-bottom: 5px"><img src="' . ( $product->get_image_id() ? current( wp_get_attachment_image_src( $product->get_image_id(), $args['image_size'][2] ) ) : wc_placeholder_img_src() ) . '" alt="' . esc_attr__( 'Product image', 'woocommerce' ) . '" height="' . esc_attr( str_replace( 'px', '', $args['image_size'][1] ) ) . '" width="' . esc_attr( str_replace( 'px', '', $args['image_size'][0] ) ) . '" style="vertical-align:middle; margin-' . ( is_rtl() ? 'left' : 'right' ) . ': 10px;" /></div>', $item ) );
			}
			echo ( '<div style="padding: 5px 0;">' );
			// Product name
			echo wp_kses_post( '<span class="yaymail-product-name" data-sku=' . $product->get_sku() . '>' . $item->get_name() . '</span>' );

			// SKU
			if ( $args['show_sku'] && is_object( $product ) && $product->get_sku() ) {
				echo wp_kses_post( '<span class="yaymail-product-sku"> (#' . $product->get_sku() . ')</span>' );
			}
				// allow other plugins to add additional product information here
				do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $args['plain_text'] );

				// Display item meta data.
				wc_display_item_meta( $item );

				echo ( '</div>' );
		}


		// Display item download links.
		if ( $args['show_download_links'] ) {
			wc_display_item_downloads( $item );
		}

		// allow other plugins to add additional product information here
		do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $args['plain_text'] );
		?>

			</td>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>">
		<?php echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item ) ); ?>
			</td>
			<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>; word-break: break-all;">
		<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
			</td>
		</tr>
		<?php
	}

	// Show purchase note
	$purchase_note = $product->get_purchase_note();
	if ( isset( $args['show_purchase_note'] )
		&& is_object( $product )
		&& ! empty( $purchase_note )
	) {
		?>

		<tr>
			<td colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>;vertical-align: middle;padding: 12px;font-size: 14px;border-width: 1px;border-style: solid;<?php echo esc_attr( isset( $default_args['border_color'] ) ? $default_args['border_color'] : '' ); ?>">
		<?php echo esc_html( wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ) ); ?>
			</td>
		</tr>
		 
	<?php } ?>

<?php endforeach; ?>
