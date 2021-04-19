<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! empty( $billing_address ) && ! empty( $shipping_address ) ) {
	$width = '50%';
} else {
	$width = '100%';
}
$borderColor = isset( $atts['bordercolor'] ) && $atts['bordercolor'] ? 'border-color:' . html_entity_decode( $atts['bordercolor'], ENT_QUOTES, 'UTF-8' ) : 'border-color:inherit';
$textColor   = isset( $atts['textcolor'] ) && $atts['textcolor'] ? 'color:' . html_entity_decode( $atts['textcolor'], ENT_QUOTES, 'UTF-8' ) : 'color:inherit';

?>
<table style="width: 100%; 
<?php
echo esc_attr( $textColor );
echo esc_attr( ';' . $borderColor );
?>
">
	<tr>
		<?php if ( ! empty( $billing_address ) ) { ?>
			<td style="width: 
			<?php
			echo esc_attr( $width );
			echo esc_attr( ';' . $borderColor );
			?>
			 " valign="top">
				<table style="width: 100%; height: 18px; border-collapse: collapse;<?php echo esc_attr( $borderColor ); ?>" border="0">
					<tbody>
					<tr style="height: 18px;">
						<td style="height: 18px ;
						<?php
						echo esc_attr( $textColor );
						echo esc_attr( ';' . $borderColor );
						?>
						" valign="top" data-textcolor>
						<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>" data-bordercolor>
							<span style="font-size: 14px;">
							<?php echo _e( $billing_address, 'woocommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunctio, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</address>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		<?php } ?>
		<?php if ( ! empty( $shipping_address ) ) { ?>
			<td style="width: 
			<?php
			echo esc_attr( $width );
			echo esc_attr( ';' . $borderColor );
			?>
			 ;border-color: inherit;" valign="top">
				<table style="width: 100%; height: 18px;border-collapse: collapse;<?php echo esc_attr( $borderColor ); ?>" border="0">
					<tbody>
					<tr style="height: 18px;">
						<td style="height: 18px;
						<?php
						echo esc_attr( $textColor );
						echo esc_attr( ';' . $borderColor );
						?>
						" valign="top">
						<address style="padding: 12px;border-style:solid; border-width: 1px;<?php echo esc_attr( $borderColor ); ?>">
							<span style="font-size: 14px; color: inherit;">
							<?php echo _e( $shipping_address, 'woocommerce' ); // phpcs:ignore WordPress.Security.EscapeOutput.UnsafePrintingFunction, WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</span>
						</address>
						</td>
					</tr>
					</tbody>
				</table>
			</td>
		<?php } ?>
	</tr>
</table>