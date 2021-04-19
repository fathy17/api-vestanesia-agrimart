<?php

namespace YayMail\MailBuilder;

use YayMail\Page\Source\CustomPostType;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class WooTemplate {


	protected static $instance = null;
	private $templateAccount;
	private $templateSubscription;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		$this->templateAccount          = array( 'customer_new_account', 'customer_new_account_activation', 'customer_reset_password' );
		$this->templateSubscription     = array( 'cancelled_subscription', 'expired_subscription', 'suspended_subscription' );
		$this->templateYITHSubscription = array( 'ywsbs_subscription_admin_mail', 'ywsbs_customer_subscription_cancelled', 'ywsbs_customer_subscription_suspended', 'ywsbs_customer_subscription_expired', 'ywsbs_customer_subscription_before_expired', 'ywsbs_customer_subscription_paused', 'ywsbs_customer_subscription_resumed', 'ywsbs_customer_subscription_request_payment', 'ywsbs_customer_subscription_renew_reminder', 'ywsbs_customer_subscription_payment_done', 'ywsbs_customer_subscription_payment_failed' );
		add_filter( 'wc_get_template', array( $this, 'getTemplateMail' ), 10, 5 );
	}

	private function __construct() {}
	// define the woocommerce_new_order callback
	public function getTemplateMail( $located, $template_name, $args, $template_path, $default_path ) {
		$this_template  = false;
		$templateActive = file_exists( YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' ) ? YAYMAIL_PLUGIN_PATH . 'views/templates/single-mail-template.php' : false;
		$template       = isset( $args['email'] ) && isset( $args['email']->id ) && ! empty( $args['email']->id ) ? $args['email']->id : false;

		if ( $template ) {
			if ( CustomPostType::postIDByTemplate( $template ) ) {
				$postID = CustomPostType::postIDByTemplate( $template );
				if ( get_post_meta( $postID, '_yaymail_status', true ) && ! empty( get_post_meta( $postID, '_yaymail_html', true ) ) ) {
					if ( isset( $args['order'] ) ) { // template mail with order
						$this_template = $templateActive;
					} else {
						if ( in_array( $template, $this->templateAccount ) || in_array( $template, $this->templateSubscription ) || in_array( $template, $this->templateYITHSubscription ) ) { // template mail with account
							   $this_template = $templateActive;
						}
					}
				}
			}
		}
		$this_template = $this_template ? $this_template : $located;
		return $this_template;
	}
}
