<?php

defined( 'ABSPATH' ) || exit;
?>

<?php
$before = '<a style="color: inherit" class="yaymail_builder_link" href="">';
$after  = '</a>';
echo wp_kses_post( $before . sprintf( __( '[Subscription #1]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', new WC_DateTime(), wc_format_datetime( new WC_DateTime() ) ) );
