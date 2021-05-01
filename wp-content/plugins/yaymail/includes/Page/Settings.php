<?php

namespace YayMail\Page;

use YayMail\Ajax;
use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\DefaultElement;
use YayMail\Templates\Templates;

defined( 'ABSPATH' ) || exit;
/**
 * Settings Page
 */
class Settings {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private $pageId = null;
	private $templateAccount;
	private $emails = null;
	public function doHooks() {
		$this->templateAccount = array( 'customer_new_account', 'customer_new_account_activation', 'customer_reset_password' );

		// Register Custom Post Type use Email Builder
		add_action( 'init', array( $this, 'registerCustomPostType' ) );

		// Register Menu
		add_action( 'admin_menu', array( $this, 'settingsMenu' ) );

		// Register Style & Script use for Menu Backend
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );

		add_filter( 'plugin_action_links_' . YAYMAIL_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );

		// free version
		$optionNotice = get_option( 'yaymail_notice' );
		if ( time() >= (int) $optionNotice ) {
			add_action( 'admin_notices', array( $this, 'renderNotice' ) );
		}

		// Ajax display notive
		add_action( 'wp_ajax_yaymail_notice', array( $this, 'yaymailNotice' ) );

		// Add Woocommerce email setting columns
		add_filter( 'woocommerce_email_setting_columns', array( $this, 'yaymail_email_setting_columns' ) );
		add_action( 'woocommerce_email_setting_column_template', array( $this, 'column_template' ) );

		// Excute Ajax
		Ajax::getInstance();
	}
	public function __construct() {}

	public function yaymail_email_setting_columns( $array ) {
		if ( isset( $array['actions'] ) ) {
			unset( $array['actions'] );
			return array_merge(
				$array,
				array(
					'template' => '',
					'actions'  => '',
				)
			);
		}
		return $array;
	}
	public function column_template( $email ) {
		echo '<td class="wc-email-settings-table-template">
				<a class="button alignright" target="_blank" href="' . esc_attr( admin_url( 'admin.php?page=yaymail-settings' ) ) . '&template=' . esc_attr( $email->id ) . '">' . esc_html( __( 'Customize with YayMail', 'yaymail' ) ) . '</a></td>';
	}

	public function renderNotice() {
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' )
			 || is_plugin_active( 'woocommerce-shipment-tracking/woocommerce-shipment-tracking.php' )
			 || is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' )
			 || is_plugin_active( 'woocommerce-admin-custom-order-fields/woocommerce-admin-custom-order-fields.php' )
			 || is_plugin_active( 'woo-advanced-shipment-tracking/woo-advanced-shipment-tracking.php' )
			 || is_plugin_active( 'yith-woocommerce-subscription-premium/init.php' )
			 || is_plugin_active( 'yith-woocommerce-subscription/init.php' )
			) {
			?>

				<div id="yaymail-notice" class="notice-info notice is-dismissible">
					<h4 style="color: #000"><?php esc_html_e( 'Please upgrade to YayMail Pro to customize emails with:', 'yaymail' ); ?></h4>
					<ul style="list-style: inside;">
						<?php
							ob_start();
							$path = YAYMAIL_PLUGIN_PATH . '/includes/Page/Source/DisplayNotice.php';
							include $path;
						?>
					</ul>
					<p style="padding-left:0">
						<a href="https://yaycommerce.com/yaymail-woocommerce-email-customizer/ " target="_blank" data="upgradenow" class="button button-primary" style="margin-right: 5px"><?php esc_html_e( 'Upgrade Now', 'yaymail' ); ?></a>
						<a href="javascript:;" data="later" class="button yaymail-nothank" style="margin-right: 5px"><?php esc_html_e( 'No, thanks', 'yaymail' ); ?></a>
					</p>
				</div>
			<?php
		}
	}


	public function yaymailNotice() {
		if ( isset( $_POST ) ) {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : null;

			if ( ! wp_verify_nonce( $nonce, 'yaymail_nonce' ) ) {
				wp_send_json_error( array( 'status' => 'Wrong nonce validate!' ) );
				exit();
			}
			update_option( 'yaymail_notice', time() + 7 * 60 * 60 * 24 ); // After 7 days show
			wp_send_json_success();
		}
		wp_send_json_error( array( 'message' => 'Update fail!' ) );
	}

	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=yaymail-settings' ) . '" aria-label="' . esc_attr__( 'View WooCommerce Email Builder', 'yaymail' ) . '">' . esc_html__( 'Settings', 'yaymail' ) . '</a>',
		);
		$links[]      = '<a target="_blank" href="https://yaycommerce.com/yaymail-woocommerce-email-customizer/" style="color: #43B854; font-weight: bold">' . __( 'Go Pro', 'yaymail' ) . '</a>';
		return array_merge( $action_links, $links );
	}

	public function registerCustomPostType() {
		$labels       = array(
			'name'               => __( 'Email Template', 'yaymail' ),
			'singular_name'      => __( 'Email Template', 'yaymail' ),
			'add_new'            => __( 'Add New Email Template', 'yaymail' ),
			'add_new_item'       => __( 'Add a new Email Template', 'yaymail' ),
			'edit_item'          => __( 'Edit Email Template', 'yaymail' ),
			'new_item'           => __( 'New Email Template', 'yaymail' ),
			'view_item'          => __( 'View Email Template', 'yaymail' ),
			'search_items'       => __( 'Search Email Template', 'yaymail' ),
			'not_found'          => __( 'No Email Template found', 'yaymail' ),
			'not_found_in_trash' => __( 'No Email Template currently trashed', 'yaymail' ),
			'parent_item_colon'  => '',
		);
		$capabilities = array();
		$args         = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => false,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'yaymail_template',
			'capabilities'       => $capabilities,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'author', 'thumbnail' ),
		);
		register_post_type( 'yaymail_template', $args );
	}
	public function settingsMenu() {
		add_submenu_page( 'woocommerce', __( 'Email Builder Settings', 'yaymail' ), __( 'Email Customizer', 'yaymail' ), 'manage_options', $this->getPageId(), array( $this, 'settingsPage' ) );
	}

	public function nitWebPluginRegisterButtons( $buttons ) {
		$buttons[] = 'table';
		$buttons[] = 'searchreplace';
		$buttons[] = 'visualblocks';
		$buttons[] = 'code';
		$buttons[] = 'insertdatetime';
		$buttons[] = 'autolink';
		$buttons[] = 'contextmenu';
		$buttons[] = 'advlist';
		return $buttons;
	}

	public function njtWebPluginRegisterPlugin( $plugin_array ) {
		$plugin_array['table']          = YAYMAIL_PLUGIN_URL . 'assets/tinymce/table/plugin.min.js';
		$plugin_array['searchreplace']  = YAYMAIL_PLUGIN_URL . 'assets/tinymce/searchreplace/plugin.min.js';
		$plugin_array['visualblocks']   = YAYMAIL_PLUGIN_URL . 'assets/tinymce/visualblocks/plugin.min.js';
		$plugin_array['code']           = YAYMAIL_PLUGIN_URL . 'assets/tinymce/code/plugin.min.js';
		$plugin_array['insertdatetime'] = YAYMAIL_PLUGIN_URL . 'assets/tinymce/insertdatetime/plugin.min.js';
		$plugin_array['autolink']       = YAYMAIL_PLUGIN_URL . 'assets/tinymce/autolink/plugin.min.js';
		$plugin_array['contextmenu']    = YAYMAIL_PLUGIN_URL . 'assets/tinymce/contextmenu/plugin.min.js';
		$plugin_array['advlist']        = YAYMAIL_PLUGIN_URL . 'assets/tinymce/advlist/plugin.min.js';
		return $plugin_array;
	}

	public function settingsPage() {
		// When load this page will not show adminbar
		?>
		<style type="text/css">
			#wpcontent, #footer {opacity: 0}
			#adminmenuback, #adminmenuwrap { display: none !important; }
		</style>
		<script type="text/javascript" id="yaymail-onload">
			jQuery(document).ready( function() {
				jQuery('#adminmenuback, #adminmenuwrap').remove();
			});
		</script>
		<?php
		// add new buttons
		add_filter( 'mce_buttons', array( $this, 'nitWebPluginRegisterButtons' ) );

		// Load the TinyMCE plugin
		add_filter( 'mce_external_plugins', array( $this, 'njtWebPluginRegisterPlugin' ) );
		$viewPath = YAYMAIL_PLUGIN_PATH . 'views/pages/html-settings.php';
		include_once $viewPath;
	}

	public function enqueueAdminScripts( $screenId ) {
		if ( 'woocommerce_page_yaymail-settings' === $screenId ) {
			// Get list template from Woo
			$wc_emails    = \WC_Emails::instance();
			$this->emails = (array) $wc_emails::instance()->emails;

			// Insert database all order template from Woo
			$templateEmail = Templates::getInstance();
			$templates     = $templateEmail::getList();

			foreach ( $templates as $key => $template ) {
				if ( ! CustomPostType::postIDByTemplate( $key ) ) {
					$arr = array(
						'mess'                            => '',
						'post_date'                       => current_time( 'Y-m-d H:i:s' ),
						'post_type'                       => 'yaymail_template',
						'post_status'                     => 'publish',
						'_yaymail_template'               => $key,
						'_email_backgroundColor_settings' => 'rgb(236, 236, 236)',
						'_yaymail_elements'               => json_decode( $template['elements'], true ),

					);
					$insert = CustomPostType::insert( $arr );
				}
			}

			/*
			@@@@ Enable Disable
			@@@@ note: Note the default value section is required when displaying in vue
			 */

			$settingDefaultEnableDisable = array(
				'new_order'                 => 1,
				'cancelled_order'           => 1,
				'failed_order'              => 1,
				'customer_on_hold_order'    => 1,
				'customer_processing_order' => 1,
				'customer_completed_order'  => 1,
				'customer_refunded_order'   => 1,
				'customer_invoice'          => 0,
				'customer_note'             => 0,
				'customer_reset_password'   => 0,
				'customer_new_account'      => 0,
			);

			$settingEnableDisables = ( CustomPostType::templateEnableDisable( false ) ) ? CustomPostType::templateEnableDisable( false ) : $settingDefaultEnableDisable;

			foreach ( $this->emails as $key => $value ) {
				if ( ! array_key_exists( $value->id, $settingEnableDisables ) ) {
					$settingEnableDisables[ $value->id ] = '0';
				}
			}

			$settingDefaultGenerals = array(
				'payment'                      => 2,
				'product_image'                => 0,
				'image_size'                   => 'thumbnail',
				'image_width'                  => '30px',
				'image_height'                 => '30px',
				'product_sku'                  => 1,
				'background_color_table_items' => '#e5e5e5',
				'content_items_color'          => '#636363',
				'title_items_color'            => '#96588a',
				'container_width'              => '605px',
				'order_url'                    => '',
				'custom_css'                   => '',
				'enable_css_custom'            => 'no',
				'image_position'               => 'Top',
			);
			$settingGenerals        = get_option( 'yaymail_settings' ) ? get_option( 'yaymail_settings' ) : $settingDefaultGenerals;
			foreach ( $settingDefaultEnableDisable as $keyDefaultEnableDisable => $settingDefaultEnableDisable ) {
				if ( ! array_key_exists( $keyDefaultEnableDisable, $settingEnableDisables ) ) {
					$settingEnableDisables[ $keyDefaultEnableDisable ] = $settingDefaultEnableDisable;
				};
			}
			$settings['enableDisable'] = $settingEnableDisables;

			/*
			@@@@ General
			@@@@ note: Note the default value section is required when displaying in vue
			 */

			$settingGenerals = get_option( 'yaymail_settings' ) ? get_option( 'yaymail_settings' ) : $settingDefaultGenerals;
			foreach ( $settingDefaultGenerals as $keyDefaultGeneral => $settingGeneral ) {
				if ( ! array_key_exists( $keyDefaultGeneral, $settingGenerals ) ) {
					$settingGenerals[ $keyDefaultGeneral ] = $settingDefaultGenerals[ $keyDefaultGeneral ];
				};
			}

			$settingGenerals['direction_rtl'] = get_option( 'yaymail_direction' ) ? get_option( 'yaymail_direction' ) : 'ltr';
			$settings['general']              = $settingGenerals;

			$scriptId = $this->getPageId();
			$order    = CustomPostType::getListOrders();
			wp_enqueue_script( 'vue', YAYMAIL_PLUGIN_URL . ( YAYMAIL_DEBUG ? 'assets/libs/vue.js' : 'assets/libs/vue.min.js' ), '', YAYMAIL_VERSION, true );
			wp_enqueue_script( 'vuex', YAYMAIL_PLUGIN_URL . 'assets/libs/vuex.js', '', YAYMAIL_VERSION, true );

			do_action( 'yaymail_before_enqueue_dependence' );

			wp_enqueue_script( $scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/js/main.js', array( 'jquery' ), YAYMAIL_VERSION, true );
			wp_enqueue_style( $scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/css/main.css', array(), YAYMAIL_VERSION );

			// Css of ant
			// wp_enqueue_style($scriptId . 'antd-css', YAYMAIL_PLUGIN_URL . 'assets/admin/css/antd.min.css');
			// Css file app
			// wp_enqueue_style($scriptId, YAYMAIL_PLUGIN_URL . 'assets/dist/css/main.css');

			wp_enqueue_script( $scriptId . '-script', YAYMAIL_PLUGIN_URL . 'assets/admin/js/script.js', '', YAYMAIL_VERSION, true );
			$yaymailSettings = get_option( 'yaymail_settings' );

			// Load ACE Editor -Start
			if ( isset( $yaymailSettings['enable_css_custom'] ) && 'yes' == $yaymailSettings['enable_css_custom'] ) {
				wp_enqueue_script( $scriptId . 'ace-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/ace.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace1-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/ext-language_tools.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace2-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/mode-css.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace3-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/theme-merbivore_soft.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace4-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/worker-css.js', '', YAYMAIL_VERSION, true );
				wp_enqueue_script( $scriptId . 'ace5-script', YAYMAIL_PLUGIN_URL . 'assets/aceeditor/snippets/css.js ', '', YAYMAIL_VERSION, true );
			} else {
				wp_dequeue_script( $scriptId . 'ace-script' );
				wp_dequeue_script( $scriptId . 'ace1-script' );
				wp_dequeue_script( $scriptId . 'ace2-script' );
				wp_dequeue_script( $scriptId . 'ace3-script' );
				wp_dequeue_script( $scriptId . 'ace4-script' );
				wp_dequeue_script( $scriptId . 'ace5-script' );
			}
			// Load ACE Editor -End
			// Css for page admin of WordPress.
			wp_enqueue_style( $scriptId . '-css', YAYMAIL_PLUGIN_URL . 'assets/admin/css/css.css', array(), YAYMAIL_VERSION );
			$current_user                 = wp_get_current_user();
			$default_email_test           = false != get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) ? get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) : $current_user->user_email;
			$element                      = new DefaultElement();
			$yaymailSettingsDefaultLogo   = get_option( 'yaymail_settings_default_logo' );
			$setDefaultLogo               = false != $yaymailSettingsDefaultLogo ? $yaymailSettingsDefaultLogo['set_default'] : '0';
			$yaymailSettingsDefaultFooter = get_option( 'yaymail_settings_default_footer' );
			$setDefaultFooter             = false != $yaymailSettingsDefaultFooter ? $yaymailSettingsDefaultFooter['set_default'] : '0';
			if ( isset( $_GET['template'] ) || ! empty( $_GET['template'] ) ) {
				$req_template['id'] = sanitize_text_field( $_GET['template'] );
			} else {
				$req_template['id'] = 'new_order';
			}
			foreach ( $this->emails as $value ) {
				if ( $value->id == $req_template['id'] ) {
					$req_template['title'] = $value->title;
				}
			}

			// List email supported

			$list_email_supported = array(
				'WC_Subscription'           => array(
					'plugin_name'   => 'WooCommerce Subscriptions',
					'template_name' => array(
						'cancelled_subscription',
						'expired_subscription',
						'suspended_subscription',
						'customer_completed_renewal_order',
						'customer_completed_switch_order',
						'customer_on_hold_renewal_order',
						'customer_renewal_invoice',
						'new_renewal_order',
						'new_switch_order',
						'customer_processing_renewal_order',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-subscription',
				),
				'yith_wishlist_constructor' => array(
					'plugin_name'   => 'YITH WooCommerce Wishlist Premium',
					'template_name' => array(
						'estimate_mail',
						'yith_wcwl_back_in_stock',
						'yith_wcwl_on_sale_item',
						'yith_wcwl_promotion_mail',
					),
					'link_upgrade'  => '#456',
				),
				'SUMO_Subscription'         => array(
					'plugin_name'   => 'SUMO Subscription',
					'template_name' => array(
						'subscription_new_order',
						'subscription_new_order_old_subscribers',
						'subscription_processing_order',
						'subscription_completed_order',
						'subscription_pause_order',
						'subscription_invoice_order_manual',
						'subscription_expiry_reminder',
						'subscription_automatic_charging_reminder',
						'subscription_renewed_order_automatic',
						'auto_to_manual_subscription_renewal',
						'subscription_overdue_order_automatic',
						'subscription_overdue_order_manual',
						'subscription_suspended_order_automatic',
						'subscription_suspended_order_manual',
						'subscription_preapproval_access_revoked',
						'subscription_turnoff_automatic_payments_success',
						'subscription_pending_authorization',
						'subscription_cancel_order',
						'subscription_cancel_request_submitted',
						'subscription_cancel_request_revoked',
						'subscription_expired_order',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/sumo-subscription',
				),

				'YITH_Subscription'         => array(
					'plugin_name'   => 'YITH WooCommerce Subscription Premium',
					'template_name' => array(
						'ywsbs_subscription_admin_mail',
						'ywsbs_customer_subscription_cancelled',
						'ywsbs_customer_subscription_suspended',
						'ywsbs_customer_subscription_expired',
						'ywsbs_customer_subscription_before_expired',
						'ywsbs_customer_subscription_paused',
						'ywsbs_customer_subscription_resumed',
						'ywsbs_customer_subscription_request_payment',
						'ywsbs_customer_subscription_renew_reminder',
						'ywsbs_customer_subscription_payment_done',
						'ywsbs_customer_subscription_payment_failed',
						'ywsbs_customer_subscription_delivery_schedules',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/yith-subscription',
				),
				'woo-b2b'                   => array(
					'plugin_name'   => 'WooCommerce B2B',
					'template_name' => array(
						'wcb2b_customer_onquote_order',
						'wcb2b_customer_quoted_order',
						'wcb2b_customer_status_notification',
						'wcb2b_new_quote',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-b2b',
				),
				'YITH_Vendors'              => array(
					'plugin_name'   => 'YITH WooCommerce Multi Vendor Premium',
					'template_name' => array(
						'cancelled_order_to_vendor',
						'commissions_paid',
						'commissions_unpaid',
						'new_order_to_vendor',
						'new_vendor_registration',
						'product_set_in_pending_review',
						'vendor_commissions_bulk_action',
						'vendor_commissions_paid',
						'vendor_new_account',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/yith-vendor',
				),
				'Germanized_Pro'            => array(
					'plugin_name'   => 'Germanized for WooCommerce Pro',
					'template_name' => array(
						'sab_cancellation_invoice',
						'sab_document',
						'sab_document_admin',
						'sab_simple_invoice',
						'sab_packing_slip',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-germanized-pro',
				),
				'WC_Bookings'               => array(
					'plugin_name'   => 'WooCommerce Bookings',
					'template_name' => array(
						'admin_booking_cancelled',
						'booking_cancelled',
						'booking_confirmed',
						'booking_notification',
						'booking_pending_confirmation',
						'booking_reminder',
						'new_booking',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-bookings',
				),
				'WooCommerce_Waitlist'      => array(
					'plugin_name'   => 'WooCommerce Waitlist',
					'template_name' => array(
						'woocommerce_waitlist_joined_email',
						'woocommerce_waitlist_left_email',
						'woocommerce_waitlist_mailout',
						'woocommerce_waitlist_signup_email',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-waitlist',
				),
				'WooCommerce_Quotes'        => array(
					'plugin_name'   => 'Quotes for WooCommerce',
					'template_name' => array(
						'qwc_req_new_quote',
						'qwc_request_sent',
						'qwc_send_quote',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo-quotes',
				),
				'YITH_Pre_Order'            => array(
					'plugin_name'   => 'YITH Pre-Order for WooCommerce Premium ',
					'template_name' => array(
						'yith_ywpo_date_end',
						'yith_ywpo_sale_date_changed',
						'yith_ywpo_is_for_sale',
						'yith_ywpo_out_of_stock',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/yith_pre_order',
				),
				'WooCommerce_Appointments'  => array(
					'plugin_name'   => 'WooCommerce Appointments',
					'template_name' => array(
						'admin_appointment_cancelled',
						'admin_new_appointment',
						'appointment_cancelled',
						'appointment_confirmed',
						'appointment_follow_up',
						'appointment_reminder',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/woo_appointments',
				),
				'SG_Order_Approval'         => array(
					'plugin_name'   => 'Sg Order Approval for Woocommerce',
					'template_name' => array(
						'wc_admin_order_new',
						'wc_customer_order_new',
						'wc_customer_order_approved',
						'wc_customer_order_rejected',
					),
					'link_upgrade'  => 'https://yaycommerce.com/yaymail-addon/sg_order_approval',
				),
			);

			$list_plugin_for_pro = array();

			if ( class_exists( 'WC_Shipment_Tracking_Actions' ) || class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
				$list_plugin_for_pro[] = 'WC_Shipment_Tracking';
			}
			if ( class_exists( 'WC_Admin_Custom_Order_Fields' ) ) {
				$list_plugin_for_pro[] = 'WC_Admin_Custom_Order_Fields';
			}

			wp_localize_script(
				$scriptId,
				'yaymail_data',
				array(
					'orders'               => $order,
					'imgUrl'               => YAYMAIL_PLUGIN_URL . 'assets/dist/images',
					'nonce'                => wp_create_nonce( 'email-nonce' ),
					'defaultDataElement'   => $element->defaultDataElement,
					'home_url'             => home_url(),
					'settings'             => $settings,
					'admin_url'            => get_admin_url(),
					'yaymail_plugin_url'   => YAYMAIL_PLUGIN_URL,
					'wc_emails'            => $this->emails,
					'default_email_test'   => $default_email_test,
					'template'             => $req_template,
					'set_default_logo'     => $setDefaultLogo,
					'set_default_footer'   => $setDefaultFooter,
					'list_plugin_for_pro'  => $list_plugin_for_pro,
					'plugins'              => apply_filters( 'yaymail_plugins', array() ),
					'list_email_supported' => $list_email_supported,
				)
			);
		}
		wp_enqueue_script( 'yaymail-notice', YAYMAIL_PLUGIN_URL . 'assets/admin/js/notice.js', array( 'jquery' ), YAYMAIL_VERSION, false );
		wp_localize_script(
			'yaymail-notice',
			'yaymail_notice',
			array(
				'admin_ajax' => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'yaymail_nonce' ),
			)
		);
	}
	public function getPageId() {
		if ( null == $this->pageId ) {
			$this->pageId = YAYMAIL_PREFIX . '-settings';
		}

		return $this->pageId;
	}
}
