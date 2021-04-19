<?php

defined( 'ABSPATH' ) || exit;
$sent_to_admin    = ( isset( $sent_to_admin ) ? true : false );
$plain_text       = ( isset( $plain_text ) ? $plain_text : '' );
$email            = ( isset( $email ) ? $email : '' );
$subscriptionType = ( isset( $subscriptionType ) ? $subscriptionType : '' );
$dateCurrent      = gmdate( 'F j Y' );
?>


<!-- Table Items has Border -->
<table class="yaymail_builder_table_items_border yaymail_builder_table_subcription" cellspacing="0" cellpadding="6" border="1" style="width: 100% !important;border-color: inherit;color: inherit;flex-direction:inherit;" width="100%">
	<thead>
		<tr style="word-break: normal">
		<?php if ( 'info' == $subscriptionType ) { ?>
			<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'ID', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Start date', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End date', 'table heading', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Recurring total', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
		<?php } else { ?>
			<th class="td" scope="col" style="text-align:left;"><?php esc_html_e( 'Subscription', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Price', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Last Order Date', 'table heading', 'woocommerce-subscriptions' ); ?></th>
			<?php if ( 'cancelled' == $subscriptionType ) { ?>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End of Prepaid Term', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
			<?php } ?>
			<?php if ( 'expired' == $subscriptionType ) { ?>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'End Date', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
			<?php } ?>
			<?php if ( 'suspended' == $subscriptionType ) { ?>
			<th class="td" scope="col" style="text-align:left;"><?php echo esc_html_x( 'Date Suspended', 'table headings in notification email', 'woocommerce-subscriptions' ); ?></th>
			<?php } ?>
		<?php } ?>
		</tr>
	</thead>

	<tbody style="flex-direction:inherit;">
		<tr class="order_item" style="flex-direction:inherit;">
		<?php if ( 'info' == $subscriptionType ) { ?>
			<td class="td" width="1%" style="text-align:left; vertical-align:middle;">
				<a style="color:#96588a;" href="#"><?php echo esc_html( '#1' ); ?></a>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( $dateCurrent ); ?>    
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( $dateCurrent ); ?>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
				<span class="woocommerce-Price-amount amount"> <?php echo esc_html( '£18.00' ); ?></span><?php echo esc_html( '/month' ); ?></td>
			</td>
		<?php } else { ?>
			<td class="td" width="1%" style="text-align:left; vertical-align:middle;">
				<a style="color:#96588a;" href="#"><?php echo esc_html( '#1' ); ?></a>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
				<span class="woocommerce-Price-amount amount"> <?php echo esc_html( '£18.00' ); ?></span><?php echo esc_html( '/month' ); ?></td>
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( $dateCurrent ); ?>    
			</td>
			<td class="td" style="text-align:left; vertical-align:middle;">
			<?php echo esc_html( $dateCurrent ); ?>
			</td>
		<?php } ?>
		</tr>
	</tbody>
</table>


		
