<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$emailTextLinkColor = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';

if ( $tracking_items ) : ?>
	<table class="yaymail_builder_table_items_border yaymail_builder_table_tracking_item" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;" width="100%">

		<thead>
			<tr>
				<th class="tracking-provider" colspan="<?php echo esc_html( 'WC_Shipment_Tracking_Actions' == $setClassAvtive ? 1 : 2 ); ?>" scope="col" class="td" style="text-align: left;"><?php esc_html_e( 'Provider', 'yaymail' ); ?></th>
				<th class="tracking-number" scope="col" class="td" style="text-align: left;"><?php esc_html_e( 'Tracking Number', 'yaymail' ); ?></th>
				<th class="date-shipped" scope="col" class="td" style="text-align: left;"><?php esc_html_e( 'Date', 'yaymail' ); ?></th>
				<th class="order-actions" scope="col" class="td" style="text-align: left;">&nbsp;</th>
			</tr>
		</thead>

		<tbody style="flex-direction:inherit;">
		<?php
		foreach ( $tracking_items as $tracking_item ) {
			?>
				<tr class="tracking order_item" style="flex-direction:inherit;">
					
					<?php if ( 'WC_Shipment_Tracking_Actions' == $setClassAvtive ) { ?>
						<td class="tracking-provider" data-title="<?php esc_html_e( 'Provider', 'yaymail' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
							<?php echo esc_html( null != $tracking_item['custom_tracking_provider'] ? $tracking_item['custom_tracking_provider'] : $tracking_item['tracking_provider'] ); ?>
						</td>
						<?php
					} else {
						global $wpdb;

						$tracking_provider = isset( $tracking_item['tracking_provider'] ) ? $tracking_item['tracking_provider'] : $tracking_item['custom_tracking_provider'];
						if ( $tracking_provider ) {
							$tracking_provider = apply_filters( 'convert_provider_name_to_slug', $tracking_provider );

							$results = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woo_shippment_provider WHERE ts_slug = %s", $tracking_provider ) );

							$provider_name = apply_filters( 'get_ast_provider_name', $tracking_provider, $results );
						} else {
							$provider_name = $tracking_item['custom_tracking_provider'];
						}

						?>
						<td class="tracking-provider" data-title="<?php esc_html_e( 'Provider', 'yaymail' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
							<img style="vertical-align: middle;" src="<?php echo esc_html( apply_filters( 'get_shipping_provdider_src', $results ) ); ?>">
						</td>
						<td class="tracking-provider" data-title="<?php esc_html_e( 'Provider', 'yaymail' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
						<?php echo esc_html( apply_filters( 'ast_provider_title', esc_html( $provider_name ) ) ); ?>
						</td>
					<?php } ?> 

					<td class="tracking-number" data-title="<?php esc_html_e( 'Tracking Number', 'yaymail' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
						<?php if ( isset( $tracking_item['tracking_number'] ) ) { ?>
							<?php echo esc_html( $tracking_item['tracking_number'] ); ?>
						<?php } ?>
					</td>
					<td class="date-shipped" data-title="<?php esc_html_e( 'Status', 'yaymail' ); ?>" style="text-align: left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
						<?php if ( isset( $tracking_item['date_shipped'] ) ) { ?>
							<time datetime="<?php echo esc_attr( gmdate( 'Y-m-d', $tracking_item['date_shipped'] ) ); ?>" title="<?php echo esc_attr( gmdate( 'Y-m-d', $tracking_item['date_shipped'] ) ); ?>"><?php echo esc_html( date_i18n( get_option( 'date_format' ), $tracking_item['date_shipped'] ) ); ?></time>
						<?php } ?>
					</td>
					
					<td class="order-actions" style="text-align: center; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding: 12px;">
						<?php if ( isset( $tracking_item['formatted_tracking_link'] ) ) { ?>
							<a style="color: <?php echo esc_attr( $emailTextLinkColor ); ?>" href="<?php echo esc_url( $tracking_item['formatted_tracking_link'] ); ?>" target="_blank"><?php esc_html_e( 'Track', 'yaymail' ); ?></a>
						<?php } ?>
					</td>
					
				</tr>
				<?php
		}
		?>
		</tbody>
	</table>
	<?php
	endif;
