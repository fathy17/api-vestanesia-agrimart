<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;

$sent_to_admin   = ( isset( $sent_to_admin ) ? true : false );
$plain_text      = ( isset( $plain_text ) ? $plain_text : '' );
$email           = ( isset( $email ) ? $email : '' );
$postID          = CustomPostType::postIDByTemplate( $this->template );
$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
?>


<!-- Table Items has Border -->
<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;border-color: inherit;color: inherit;flex-direction:inherit;" width="100%">
	<thead>
		<tr style="word-break: normal">
			<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'Subscription', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Price', 'table headings in notification email', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Last Order Date', 'table heading', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Date Suspended', 'table headings in notification email', 'yaymail' ); ?></th>
		</tr>
	</thead>

	<tbody style="flex-direction:inherit;">
		<tr class="order_item" style="flex-direction:inherit;">
		<td class="td" width="1%" style="text-align:left; vertical-align:middle;">
				<a style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( wcs_get_edit_post_link( $subscription->get_id() ) ); ?>">#<?php echo esc_html( $subscription->get_order_number() ); ?></a>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
				<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
				<?php
				$last_order_time_created = $subscription->get_time( 'last_order_date_created', 'site' );
				if ( ! empty( $last_order_time_created ) ) {
					echo esc_html( date_i18n( wc_date_format(), $last_order_time_created ) );
				} else {
					esc_html_e( '-', 'yaymail' );
				}
				?>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
				<?php echo esc_html( date_i18n( wc_date_format(), time() ) ); ?>
			</td>
		</tr>
	</tbody>
</table>
