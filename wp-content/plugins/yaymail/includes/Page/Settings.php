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
		//add_shortcode( 'yaymail_billing_shipping_address_content', array( $this, 'shortcodeCallBack' ) );
		// Excute Ajax
		Ajax::getInstance();
	}
	public function __construct() {}
	public function shortcodeCallBack( $atts ) {
		$billing_address  = $atts['billing_address'];
		$shipping_address = $atts['shipping_address'];
		ob_start();
		$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-content.php';
		$order = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function yaymail_email_setting_columns( $array ) {
		// filter...
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
		$arrEmailDefault = array(
			'new_order',
			'cancelled_order',
			'failed_order',
			'customer_on_hold_order',
			'customer_processing_order',
			'customer_completed_order',
			'customer_refunded_order',
			'customer_invoice',
			'customer_note',
			'customer_reset_password',
			'customer_new_account',
		);

		if ( in_array( $email->id, $arrEmailDefault ) ) {
			echo '<td class="wc-email-settings-table-template">
			<a class="button alignright" target="_blank" href="' . esc_attr( admin_url( 'admin.php?page=yaymail-settings' ) ) . '&template=' . esc_attr( $email->id ) . '">' . esc_html( __( 'Customize with YayMail', 'yaymail' ) ) . '</a></td>';
		} else {
			echo '<td class="wc-email-settings-table-template">
			<a class="button alignright" target="_blank" href="https://yaycommerce.com/yaymail-woocommerce-email-customizer/">' . esc_html( __( 'Customize with YayMail (PRO)', 'yaymail' ) ) . '</a></td>';
		}

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
						'_yaymail_html'                   => $template['html'],
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
			$settingEnableDisables['yaymail_direction'] = get_option( 'yaymail_direction' ) ? get_option( 'yaymail_direction' ) : 'ltr';
			$settings['enableDisable']                  = $settingEnableDisables;

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
			$settings['general'] = $settingGenerals;

			$scriptId = $this->getPageId();
			$order    = CustomPostType::getListOrders();
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
			$current_user       = wp_get_current_user();
			$default_email_test = false != get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) ? get_user_meta( get_current_user_id(), 'yaymail_default_email_test', true ) : $current_user->user_email;
			$element            = new DefaultElement();
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
			wp_localize_script(
				$scriptId,
				'yaymail_data',
				array(
					'orders'             => $order,
					'imgUrl'             => YAYMAIL_PLUGIN_URL . 'assets/dist/images',
					'nonce'              => wp_create_nonce( 'email-nonce' ),
					'defaultDataElement' => $element->defaultDataElement,
					'home_url'           => home_url(),
					'settings'           => $settings,
					'admin_url'          => get_admin_url(),
					'yaymail_plugin_url' => YAYMAIL_PLUGIN_URL,
					'wc_emails'          => $this->emails,
					'default_email_test' => $default_email_test,
					'template'           => $req_template,
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
