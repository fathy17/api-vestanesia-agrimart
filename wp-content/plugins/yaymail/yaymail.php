<?php
/**
 * Plugin Name: YayMail - WooCommerce Email Customizer
 * Plugin URI: https://yaycommerce.com/yaymail-woocommerce-email-customizer/
 * Description: Create awesome transactional emails with a drag and drop email builder
 * Version: 1.9.6
 * Author: YayCommerce
 * Author URI: https://yaycommerce.com
 * Text Domain: yaymail
 * WC requires at least: 3.0.0
 * WC tested up to: 5.1.0
 * Domain Path: /i18n/languages/
 */

namespace YayMail;

defined( 'ABSPATH' ) || exit;
define( 'YAYMAIL_PREFIX', 'yaymail' );
define( 'YAYMAIL_VERSION', '1.9.6' );
define( 'YAYMAIL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'YAYMAIL_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'YAYMAIL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__;
		$base_dir = __DIR__ . '/includes';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class_name = substr( $class, $len );

		$file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);
if ( ! function_exists( 'install_yaymail_admin_notice' ) ) {
	function install_yaymail_admin_notice() {
		?>
			<div class="error">
				<p><?php echo 'YayMail ' . __( 'is enabled but not effective. It requires WooCommerce in order to work.', 'yaymail' ); ?></p>
			</div>
		<?php
	}
}
function init() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'YayMail\\install_yaymail_admin_notice' );
	}
	Plugin::getInstance();
	I18n::getInstance();
	Page\Settings::getInstance();
	MailBuilder\WooTemplate::getInstance();
}
add_action( 'plugins_loaded', 'YayMail\\init' );

register_activation_hook( __FILE__, array( 'YayMail\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'YayMail\\Plugin', 'deactivate' ) );
