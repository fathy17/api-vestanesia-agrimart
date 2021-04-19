<?php

defined( 'ABSPATH' ) || exit;

?>

<!-- Table Items has Border -->
<?php
if ( isset( $downloads ) || 'sampleOrder' === $checkOrder ) {
	?>
<h2 style="color: inherit; margin: 13px 0px;" class="woocommerce-order-downloads__title"><?php esc_html_e( 'Downloads', 'woocommerce' ); ?></h2>
	<?php
}
?>
