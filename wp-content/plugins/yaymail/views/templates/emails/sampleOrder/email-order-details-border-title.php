<?php

defined( 'ABSPATH' ) || exit;
use YayMail\Page\Source\CustomPostType;
$postID          = CustomPostType::postIDByTemplate( $this->template );
$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
$titleColor      = isset( $atts['titlecolor'] ) && $atts['titlecolor'] ? 'color:' . html_entity_decode( $atts['titlecolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';
?>

<?php
$before = '<a style="font-weight: normal;' . esc_attr( $titleColor ) . '" class="yaymail_builder_link" href="">';
$after  = '</a>';
/* translators: %s: Order ID. */
echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', 1, new WC_DateTime(), wc_format_datetime( new WC_DateTime() ) ) );

