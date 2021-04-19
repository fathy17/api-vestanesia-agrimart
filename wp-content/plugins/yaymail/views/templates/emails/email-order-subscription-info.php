<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;

if ( empty( $subscriptions ) ) {
	return;
}
$sent_to_admin         = ( isset( $sent_to_admin ) ? true : false );
$plain_text            = ( isset( $plain_text ) ? $plain_text : '' );
$email                 = ( isset( $email ) ? $email : '' );
$has_automatic_renewal = false;
$is_parent_order       = wcs_order_contains_subscription( $order, 'parent' );
$postID                = CustomPostType::postIDByTemplate( $this->template );
$text_link_color       = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
?>


<!-- Table Items has Border -->
<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;border-color: inherit;color: inherit;flex-direction:inherit;" width="100%">
	<thead>
		<tr style="word-break: normal">
		<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'ID', 'subscription ID table heading', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Start date', 'table heading', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End date', 'table heading', 'yaymail' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Recurring total', 'table heading', 'yaymail' ); ?></th>
		</tr>
	</thead>

	<tbody style="flex-direction:inherit;">
		<?php foreach ( $subscriptions as $subscription ) : ?>
			<?php $has_automatic_renewal = $has_automatic_renewal || ! $subscription->is_manual(); ?>
			<tr class="order_item" style="flex-direction:inherit;">
				<td class="td" width="1%" style="text-align:left; vertical-align:middle;">
					<?php /* translators: #%s: search term */ ?>
					<a style="color:<?php echo esc_attr( $text_link_color ); ?>" href="<?php echo esc_url( ( $is_admin_email ) ? wcs_get_edit_post_link( $subscription->get_id() ) : $subscription->get_view_order_url() ); ?>"><?php echo sprintf( esc_html_x( '#%s', 'subscription number in email table. (eg: #106)', 'woocommerce-subscriptions' ), esc_html( $subscription->get_order_number() ) ); ?></a>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'start_date', 'site' ) ) ); ?>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( ( 0 < $subscription->get_time( 'end' ) ) ? date_i18n( wc_date_format(), $subscription->get_time( 'end', 'site' ) ) : _x( 'When cancelled', 'Used as end date for an indefinite subscription', 'woocommerce-subscriptions' ) ); ?>
				</td>
				<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
			<?php if ( $is_parent_order && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
						<br>
						<small><?php printf( esc_html__( 'Next payment: ', 'woocommerce-subscriptions' ) . esc_html( date_i18n( wc_date_format(), $subscription->get_time( 'next_payment', 'site' ) ) ) ); ?></small>
			<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
<?php
if ( $has_automatic_renewal && ! $is_admin_email && $subscription->get_time( 'next_payment' ) > 0 ) {
	if ( count( $subscriptions ) === 1 ) {
		$subscription   = reset( $subscriptions );
		$my_account_url = $subscription->get_view_order_url();
	} else {
		$my_account_url = wc_get_endpoint_url( 'subscriptions', '', wc_get_page_permalink( 'myaccount' ) );
	}

	printf(
		'<small>%s</small>',
		wp_kses_post(
			sprintf(
				 /* translators: %1$s: search term */
				 /* translators: %2$s: search term */
				_n(
				// Translators: Placeholders are opening and closing My Account link tags.
					'This subscription is set to renew automatically using your payment method on file. You can manage or cancel this subscription from your %1$smy account page%2$s.',
					'These subscriptions are set to renew automatically using your payment method on file. You can manage or cancel your subscriptions from your %1$smy account page%2$s.',
					count( $subscriptions ),
					'woocommerce-subscriptions'
				),
				'<a href="' . $my_account_url . '">',
				'</a>'
			)
		)
	);
}
?>
</div>
