<?php

namespace YayMail\MailBuilder;

use YayMail\Helper\Helper;
use YayMail\Page\Source\CustomPostType;
use YayMail\Templates\Templates;

defined( 'ABSPATH' ) || exit;
global $woocommerce, $wpdb, $current_user, $order;

class Shortcodes {

	protected static $instance = null;
	public $order_id           = false;
	public $sent_to_admin      = false;
	public $order;
	public $order_data;
	public $template      = false;
	public $customer_note = false;
	// public $array_content_template = false;
	public $shortcodes_lists;
	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct( $template = false, $checkOrder = '' ) {
		if ( $template ) {
			$this->template = $template;
			if ( 'sampleOrder' === $checkOrder ) {
				$this->shortCodesOrderSample();
			} else {
				$this->shortCodesOrderDefined();
			}
			// style css
			add_filter( 'woocommerce_email_styles', array( $this, 'customCss' ) );

						// Order Details
			$order_details_list = array(
				'items_border',
				'items',
				'order_date',
				'order_fee',
				'order_id',
				'order_link',
				'order_number',
				'order_refund',
				'order_sub_total',
				'order_total',
				'order_tn',
				'items_border_before',
				'items_border_after',
				// 'items_border_title',
				// 'items_border_content',
				'items_downloadable_product',
				'items_downloadable_title',
				'subscription_header',
				'subscription_table_title',
				'subscription_table',
				'woocommerce_email_before_order_table',
				'woocommerce_email_after_order_table',
			);

						// Order Supcription
						$order_subscription_list = array( 'items_subscription_suspended', 'items_subscription_expired', 'items_subscription_cancelled', 'items_subscription_information' );

			// Payments
			$payments_list = array(
				'order_payment_method',
				'order_payment_url',
				'order_payment_url_string',
				'payment_method',
				'transaction_id',
			);

			// Shippings
			$shippings_list = array(
				'order_shipping',
				'shipping_address',
				'shipping_address_1',
				'shipping_address_2',
				'shipping_city',
				'shipping_company',
				'shipping_country',
				'shipping_first_name',
				'shipping_last_name',
				'shipping_method',
				'shipping_postcode',
				'shipping_state',
			);

			// Billings
			$billings_list = array(
				'billing_address',
				'billing_address_1',
				'billing_address_2',
				'billing_city',
				'billing_company',
				'billing_country',
				'billing_email',
				'billing_first_name',
				'billing_last_name',
				'billing_phone',
				'billing_postcode',
				'billing_state',
			);

			// Reset Password
			$reset_password_list = array( 'password_reset_url', 'password_reset_url_string' );

			// New Users
			$new_users_list = array( 'user_new_password', 'user_activation_link' );

			// General
			$general_list = array(
				'customer_note',
				'customer_notes',
				'customer_provided_note',
				'site_name',
				'site_url',
				'site_url_string',
				'user_email',
				'user_id',
				'user_name',
				'customer_username',
				'view_order_url',
				'view_order_url_string',
				'billing_shipping_address',
				'domain',
				'user_account_url',
				'user_account_url_string',
				'billing_shipping_address_title',
				// 'billing_shipping_address_content',
			);

			// Additional Order Meta.
			$order = CustomPostType::getListOrders();

			/* Define Shortcodes */
			$shortcodes_lists = array();
			$shortcodes_lists = array_merge( $shortcodes_lists, $order_details_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $order_subscription_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $payments_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $shippings_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $billings_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $reset_password_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $new_users_list );
			$shortcodes_lists = array_merge( $shortcodes_lists, $general_list );

			$this->shortcodes_lists = $shortcodes_lists;
			foreach ( $this->shortcodes_lists as $key => $shortcode_name ) {
				if ( 'woocommerce_email_before_order_table' == $shortcode_name || 'woocommerce_email_after_order_table' == $shortcode_name ) {
					add_shortcode( $shortcode_name, array( $this, 'shortcodeCallBack' ) );
				} else {
					add_shortcode( 'yaymail_' . $shortcode_name, array( $this, 'shortcodeCallBack' ) );
				}
			}
			add_shortcode( 'yaymail_billing_shipping_address_content', array( $this, 'billing_shipping_address_content' ) );
			add_shortcode( 'yaymail_items_border_content', array( $this, 'items_border_content' ) );
			add_shortcode( 'yaymail_items_border_title', array( $this, 'items_border_title' ) );
		}
	}

	public function billing_shipping_address_content( $atts ) {
		$order           = $this->order;
		$postID          = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
		if ( $order ) {
			$shipping_address = $order && $order->get_formatted_shipping_address() ? $order->get_formatted_shipping_address() : null;
			$billing_address  = $order && $order->get_formatted_billing_address() ? $order->get_formatted_billing_address() : null;
			if ( $order->get_billing_phone() ) {
				$billing_address .= "<br/> <a href='tel:" . esc_html( $order->get_billing_phone() ) . " 'style='color:" . $text_link_color . "; font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_phone() ) . '</a>';
			}
			if ( $order->get_billing_email() ) {
				$billing_address .= "<br/><a href='mailto:" . esc_html( $order->get_billing_email() ) . "' style='color:" . $text_link_color . ";font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_email() ) . '</a>';
			}
		} else {
			$billing_address  = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
			$shipping_address = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		}

		ob_start();
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-content.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function items_border_content( $atts ) {
		$order = $this->order;
		if ( ! $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-content.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-content.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function items_border_title( $atts ) {
		$order = $this->order;
		if ( ! $order ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function applyCSSFormat( $defaultCss = '' ) {
		$templateEmail = \YayMail\Templates\Templates::getInstance();
		$css           = $templateEmail::getCssFortmat();
		$cssDirection  = '';
		$cssDirection .= 'td{direction: rtl}';
		$cssDirection .= 'td, th, td{text-align:right;}';

		$css .= get_option( 'yaymail_direction' ) && get_option( 'yaymail_direction' ) === 'rtl' ? $cssDirection : '';
		$css .= $defaultCss;
		$css .= '.td { color: inherit; }';
		return $css;
	}
	public function customCss( $css = '' ) {
		return $this->applyCSSFormat( $css );
	}
	public function setOrderId( $order_id, $sent_to_admin ) {
		$this->order_id      = $order_id;
		$this->sent_to_admin = $sent_to_admin;

		// Additional Order Meta.
		$order_meta_list = array();
		if ( ! empty( $this->order_id ) ) {
			$order_metaArr = get_post_meta( $this->order_id );
			if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
				foreach ( $order_metaArr as $k => $v ) {
					$nameField         = str_replace( ' ', '_', trim( $k ) );
					$order_meta_list[] = 'order_meta:' . $nameField;
				}
			}
		}
		$shortcodes_lists = array();
		$shortcodes_lists = array_merge( $shortcodes_lists, $order_meta_list );
		foreach ( $shortcodes_lists as $key => $shortcode_name ) {
			add_shortcode( 'yaymail_' . $shortcode_name, array( $this, 'shortcodeCallBack' ) );
		}
	}

	protected function _shortcode_atts( $defaults = array(), $atts = array() ) {
		if ( isset( $atts['class'] ) ) {
			$atts['classname'] = $atts['class'];
		}

		return \shortcode_atts( $defaults, $atts );
	}

	// short Codes Order when select SampleOrder
	public function shortCodesOrderSample( $sent_to_admin = '' ) {
		$user  = wp_get_current_user();
		$useId = get_current_user_id();
		$this->defaultSampleOrderData( $sent_to_admin );
	}

	public function shortCodesOrderDefined( $sent_to_admin = '', $args = array() ) {
		if ( $this->order_id && class_exists( 'WC_Order' ) ) {
			$this->order = new \WC_Order( $this->order_id );
			$this->collectOrderData( $sent_to_admin );
		}
		if ( ! function_exists( 'get_user_by' ) ) {
			return false;
		}
				$action = isset( $_REQUEST['action'] ) ? sanitize_key( $_REQUEST['action'] ) : '';
		if ( empty( $this->order_id ) || ! $this->order_id ) {
			$shortcode = $this->order_data;
			if ( isset( $_REQUEST['billing_email'] ) ) {
				$shortcode['[yaymail_user_email]'] = sanitize_email( $_REQUEST['billing_email'] );
				$user                              = get_user_by( 'email', sanitize_email( $_REQUEST['billing_email'] ) );
				if ( ! empty( $user ) ) {
					$shortcode['[yaymail_customer_username]'] = $user->user_login;
					$shortcode['[yaymail_customer_name]']     = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					$shortcode['[yaymail_user_id]']           = $user->ID;
				}
			}
			if ( empty( $shortcode['[yaymail_customer_username]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_login ) ) {
						$shortcode['[yaymail_customer_username]'] = $user->user_login;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_login ) ) {
						$shortcode['[yaymail_customer_username]'] = $user->user_login;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( empty( $shortcode['[yaymail_user_email]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_user_email]'] = $user->user_email;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_user_email]'] = $user->user_email;
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( empty( $shortcode['[yaymail_customer_name]'] ) ) {
				if ( isset( $_REQUEST['user_email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['user_email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				} elseif ( isset( $_REQUEST['email'] ) ) {
					$user = get_user_by( 'email', sanitize_email( $_REQUEST['email'] ) );
					if ( isset( $user->user_email ) ) {
						$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
					}
					if ( isset( $user->ID ) ) {
						$shortcode['[yaymail_user_id]'] = $user->ID;
					}
				}
			}
			if ( ! empty( $args ) ) {
				if ( isset( $args['email'] ) ) {
					$postID          = CustomPostType::postIDByTemplate( $this->template );
					$text_link_color = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';

					if ( isset( $args['email']->id ) && 'customer_reset_password' == $args['email']->id ) {
						$shortcode['[yaymail_customer_username]'] = $args['email']->user_login;
						// $shortcode['[yaymail_customer_name]'] = $args['email']->first_name . ' ' . $args['email']->last_name;
						$shortcode['[yaymail_user_email]']         = $args['email']->user_email;
						$resetURL                                  = esc_url(
							add_query_arg(
								array(
									'key'   => $args['email']->reset_key,
									'login' => rawurlencode( $args['email']->user_login ),
								),
								wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
							)
						);
						$shortcode['[yaymail_password_reset_url]'] = '<a style="color: ' . $text_link_color . ';" href="' . esc_url( $resetURL ) . '">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
						$shortcode['[yaymail_password_reset_url_string]'] = esc_url( $resetURL );
						$shortcode['[yaymail_site_name]']                 = get_bloginfo( 'name' );
					}
					if ( isset( $args['email']->id ) && ( 'customer_new_account' == $args['email']->id || 'customer_new_account_activation' == $args['email']->id ) ) {
						$shortcode['[yaymail_site_name]']        = get_bloginfo( 'name' );
						$shortcode['[yaymail_site_url]']         = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
						$shortcode['[yaymail_site_url_string]']  = get_site_url();
						$shortcode['[yaymail_user_account_url]'] = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
						if ( isset( $args['email']->user_pass ) && ! empty( $args['email']->user_pass ) ) {
							$shortcode['[yaymail_user_new_password]'] = $args['email']->user_pass;
						} else {
							if ( isset( $_REQUEST['pass1-text'] ) && '' != $_REQUEST['pass1-text'] ) {
								$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
							} elseif ( isset( $_REQUEST['pass1'] ) && '' != $_REQUEST['pass1'] ) {
								$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
							} else {
								$shortcode['[yaymail_user_new_password]'] = '';
							}
						}
						if ( isset( $args['email']->user_login ) && ! empty( $args['email']->user_login ) ) {
							$shortcode['[yaymail_customer_username]'] = $args['email']->user_login;
							// $shortcode['[yaymail_customer_name]'] = $args['email']->first_name . ' ' . $args['email']->last_name;
						}
						if ( isset( $args['email']->user_email ) && ! empty( $args['email']->user_email ) ) {
							$shortcode['[yaymail_user_email]'] = $args['email']->user_email;
						}
						if ( 'customer_new_account_activation' == $args['email']->id ) {
							if ( isset( $args['email']->user_activation_url ) && ! empty( $args['email']->user_activation_url ) ) {
								$shortcode['[yaymail_user_activation_link]'] = $args['email']->user_activation_url;
							}
						} else {
							global $wpdb, $wp_hasher;
							$newHash = $wp_hasher;
							// Generate something random for a password reset key.
							$key = wp_generate_password( 20, false );

							/**
*
 * This action is documented in wp-login.php
*/
							do_action( 'retrieve_password_key', $args['email']->user_login, $key );

							// Now insert the key, hashed, into the DB.
							if ( empty( $wp_hasher ) ) {
								if ( ! class_exists( 'PasswordHash' ) ) {
									include_once ABSPATH . 'wp-includes/class-phpass.php';
								}
								$newHash = new \PasswordHash( 8, true );
							}
							$hashed = time() . ':' . $newHash->HashPassword( $key );
							$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $args['email']->user_login ) );
							$activation_url                              = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $args['email']->user_login ), 'login' );
							$shortcode['[yaymail_user_activation_link]'] = $activation_url;
						}
					}
				}
			}

			$this->order_data = $shortcode;
		}
	}
	public function shortcodeCallBack( $atts, $content, $tag ) {
		return isset( $this->order_data[ '[' . $tag . ']' ] ) ? $this->order_data[ '[' . $tag . ']' ] : false;
	}

	public function templateParser() {
		// Helper::checkNonce();
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
		if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
			wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
		} else {
			$request        = $_POST;
			$this->order_id = false;
			if ( isset( $request['order_id'] ) ) {
				$order_id = sanitize_text_field( $request['order_id'] );
				if ( 'sampleOrder' !== $order_id ) {
					$order_id = intval( $order_id );
				}
				if ( ! $order_id ) {
					$order_id = '';
				}

				$this->template = isset( $request['template'] ) ? $request['template'] : false;
				$this->order_id = $order_id;
			}

			if ( ! $this->order_id || ! $this->template ) {
				return false;
			}

			if ( 'sampleOrder' !== $order_id ) {
				$this->order = new \WC_Order( $this->order_id );
			}

			if ( 'sampleOrder' !== $order_id && ( is_null( $this->order ) || empty( $this->order ) || ! isset( $this->order ) ) ) {
				return false;
			}

			if ( 'sampleOrder' !== $order_id ) {
				$this->collectOrderData();
			} else {
				$this->defaultSampleOrderData();
			}

			$result             = (object) array();
			$result->order_id   = $this->order_id;
			$result->order_data = $this->order_data;

				$shortcode_order_meta        = array();
				$shortcode_order_custom_meta = array();
			if ( 'sampleOrder' !== $order_id ) {
				$result->order        = $this->order;
				$result->order_items  = $result->order->get_items();
				$result->user_details = $result->order->get_user();

				/*
				@@@@ Get name field in custom field of order woocommerce.
				*/
				$order_metaArr = get_post_meta( $order_id );
				if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
					$pattern = '/^_.*/i';
					$n       = 0;
					foreach ( $order_metaArr as $k => $v ) {
						// @@@ starts with the "_" character of the woo field.
						if ( ! preg_match( $pattern, $k ) ) {
							$nameField              = str_replace( ' ', '_', trim( $k ) );
							$nameShorcode           = '[yaymail_post_meta:' . $nameField . ']';
							$key_order_meta         = 'post_meta:' . $nameField . '_' . $n;
							$shortcode_order_meta[] = array(
								'key'         => $key_order_meta,
								$nameShorcode => 'Loads value of order meta key - ' . $nameField,
							);
							$n++;
						}
					}
				}
				if ( ! empty( $result->order ) ) {
					foreach ( $result->order->get_meta_data() as $meta ) {
						$nameField                     = str_replace( ' ', '_', trim( $meta->get_data()['key'] ) );
						$nameShorcode                  = '[yaymail_order_meta:' . $nameField . ']';
						$key_order_custom_meta         = 'order_meta:' . $nameField;
						$shortcode_order_custom_meta[] = array(
							'key'         => $key_order_custom_meta,
							$nameShorcode => 'Loads value of order custom meta key - ' . $nameField,
						);
					}
				}
			} else {
				$result->order        = '';
				$result->order_items  = '';
				$result->user_details = '';
			}

			if ( isset( $request['template'] ) ) {
				if ( CustomPostType::postIDByTemplate( $this->template ) ) {
					$postID                                      = CustomPostType::postIDByTemplate( $this->template );
							$emailTemplate                       = get_post( $postID );
					$result->elements                            = Helper::unsanitize_array( get_post_meta( $postID, '_yaymail_elements', true ) );
					$result->html                                = html_entity_decode( get_post_meta( $postID, '_yaymail_html', true ), ENT_QUOTES, 'UTF-8' );
							$result->emailBackgroundColor        = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : 'rgb(236, 236, 236)';
							$result->emailTextLinkColor          = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
							$result->titleShipping               = get_post_meta( $postID, '_email_title_shipping', true ) ? get_post_meta( $postID, '_email_title_shipping', true ) : 'Shipping Address';
							$result->titleBilling                = get_post_meta( $postID, '_email_title_billing', true ) ? get_post_meta( $postID, '_email_title_billing', true ) : 'Billing Address';
							$result->orderTitle                  = get_post_meta( $postID, '_yaymail_email_order_item_title', true );
							$result->customCSS                   = $this->applyCSSFormat();
							$result->shortcode_order_meta        = $shortcode_order_meta;
							$result->shortcode_order_custom_meta = $shortcode_order_custom_meta;
				}
			}
			echo json_encode( $result );
			die();
		}
	}

	public function collectOrderData( $sent_to_admin = '' ) {
		$order = $this->order;
		if ( empty( $this->order_id ) || empty( $order ) ) {
			return false;
		}

		// Getting Fee & Refunds:
		$fee    = 0;
		$refund = 0;
		$totals = $order->get_order_item_totals();
		foreach ( $totals as $index => $value ) {
			if ( strpos( $index, 'fee' ) !== false ) {
				$fees = $order->get_fees();
				foreach ( $fees as $feeVal ) {
					if ( method_exists( $feeVal, 'get_amount' ) ) {
						$fee += $feeVal->get_amount();
					}
				}
			}
			if ( strpos( $index, 'refund' ) !== false ) {
				$refund = $order->get_total_refunded();
			}
		}
		// User Info
		$user_data        = $order->get_user();
		$created_date     = $order->get_date_created();
		$items            = $order->get_items();
		$yaymail_settings = get_option( 'yaymail_settings' );
		$order_url        = $order->get_edit_order_url();
		$shipping_address = $order->get_formatted_shipping_address();
		$billing_address  = $order->get_formatted_billing_address();
		$postID           = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color  = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
		if ( $order->get_billing_phone() ) {
			$billing_address .= "<br/> <a href='tel:" . esc_html( $order->get_billing_phone() ) . " 'style='color:" . $text_link_color . "; font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_phone() ) . '</a>';
		}
		if ( $order->get_billing_email() ) {
			$billing_address .= "<br/><a href='mailto:" . esc_html( $order->get_billing_email() ) . "' style='color:" . $text_link_color . ";font-weight: normal; text-decoration: underline;'>" . esc_html( $order->get_billing_email() ) . '</a>';
		}
		$customerNotes        = $order->get_customer_order_notes();
		$customerNoteHtmlList = '';
		$customerNoteHtml     = $customerNoteHtmlList;
		if ( ! empty( $customerNotes ) && count( $customerNotes ) ) {
			$customerNoteHtmlList  = $this->getOrderCustomerNotes( $customerNotes );
			$customerNote_single[] = $customerNotes[0];
			$customerNoteHtml      = $this->getOrderCustomerNotes( $customerNote_single );
		}

		$resetURL = '';
		if ( isset( $args['email']->reset_key ) && ! empty( $args['email']->reset_key )
			&& isset( $args['email']->user_login ) && ! empty( $args['email']->user_login )
		) {
			$resetURL = esc_url(
				add_query_arg(
					array(
						'key'   => $args['email']->reset_key,
						'login' => rawurlencode( $args['email']->user_login ),
					),
					wc_get_endpoint_url( 'lost-password', '', wc_get_page_permalink( 'myaccount' ) )
				)
			);
		}

		// Link Downloadable Product
		$shortcode['[yaymail_items_downloadable_title]']   = $this->orderItemsDownloadableTitle( array(), $sent_to_admin );
		$shortcode['[yaymail_items_downloadable_product]'] = $this->orderItemsDownloadable( array(), $sent_to_admin );
		// YITH Subscription
		$shortcode['[yaymail_subscription_table]']       = $this->subscriptionDetailTable( $items, $sent_to_admin );
		$shortcode['[yaymail_subscription_table_title]'] = $this->subscriptionDetailTableTitle( $items, $sent_to_admin );
		$shortcode['[yaymail_subscription_header]']      = $this->subscriptionHeader( $items, $sent_to_admin );
		// ORDER DETAILS
		$shortcode['[yaymail_items_border]']         = $this->orderItemsBorder( $items, $sent_to_admin );
		$shortcode['[yaymail_items_border_before]']  = $this->orderItemsBorderBefore( array(), $sent_to_admin );
		$shortcode['[yaymail_items_border_after]']   = $this->orderItemsBorderAfter( array(), $sent_to_admin );
		$shortcode['[yaymail_items_border_title]']   = $this->orderItemsBorderTitle( array(), $sent_to_admin );
		$shortcode['[yaymail_items_border_content]'] = $this->orderItemsBorderContent( array(), $sent_to_admin );

		// WC HOOK
		$shortcode['[woocommerce_email_before_order_table]'] = $this->orderWoocommerceBeforeHook( array(), $sent_to_admin );
		$shortcode['[woocommerce_email_after_order_table]']  = $this->orderWoocommerceAfterHook( array(), $sent_to_admin );
		// ORDER SUBSCRIPTION
		$shortcode['[yaymail_items_subscription_suspended]']   = $this->orderSubscriptionSuspended( array(), $sent_to_admin );
		$shortcode['[yaymail_items_subscription_expired]']     = $this->orderSubscriptionExpired( array(), $sent_to_admin );
		$shortcode['[yaymail_items_subscription_cancelled]']   = $this->orderSubscriptionCancelled( array(), $sent_to_admin );
		$shortcode['[yaymail_items_subscription_information]'] = $this->orderSubscriptionInformation( array(), $sent_to_admin );

		$shortcode['[yaymail_items]'] = $this->orderItems( $items, $sent_to_admin );
		if ( null != $created_date ) {
			$shortcode['[yaymail_order_date]'] = $order->get_date_created()->date_i18n( wc_date_format() );
		} else {
			$shortcode['[yaymail_order_date]'] = '';
		}
		$shortcode['[yaymail_order_fee]'] = $fee;
		if ( ! empty( $order->get_id() ) ) {
			$shortcode['[yaymail_order_id]'] = $order->get_id();
		} else {
			$shortcode['[yaymail_order_id]'] = '';
		}
		$shortcode['[yaymail_order_link]'] = '<a href="' . $order_url . '" style="color:' . $text_link_color . ';">' . esc_html__( 'Order', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_link]'] = str_replace( '[yaymail_order_id]', $order->get_id(), $shortcode['[yaymail_order_link]'] );
		if ( ! empty( $order->get_order_number() ) ) {
			$shortcode['[yaymail_order_number]'] = $order->get_order_number();
		} else {
			$shortcode['[yaymail_order_number]'] = '';
		}
		$shortcode['[yaymail_order_refund]'] = $refund;
		if ( isset( $totals['cart_subtotal']['value'] ) ) {
			$shortcode['[yaymail_order_sub_total]'] = $totals['cart_subtotal']['value'];
		} else {
			$shortcode['[yaymail_order_sub_total]'] = '';
		}
		$shortcode['[yaymail_order_total]'] = wc_price( $order->get_total() );

		// PAYMENTS
		if ( isset( $totals['payment_method']['value'] ) ) {
			$shortcode['[yaymail_order_payment_method]'] = $totals['payment_method']['value'];
		} else {
			$shortcode['[yaymail_order_payment_method]'] = '';
		}
		$shortcode['[yaymail_order_payment_url]']        = '<a href="' . esc_url( $order->get_checkout_payment_url() ) . '">' . esc_html__( 'Payment page', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_payment_url_string]'] = esc_url( $order->get_checkout_payment_url() );
		if ( ! empty( $order->get_payment_method_title() ) ) {
			$shortcode['[yaymail_payment_method]'] = $order->get_payment_method_title();
		} else {
			$shortcode['[yaymail_payment_method]'] = '';
		}
		if ( ! empty( $order->get_transaction_id() ) ) {
			$shortcode['[yaymail_transaction_id]'] = $order->get_transaction_id();
		} else {
			$shortcode['[yaymail_transaction_id]'] = '';
		}

		// SHIPPINGS
		if ( ! empty( $order->calculate_shipping() ) ) {
			$shortcode['[yaymail_order_shipping]'] = $order->calculate_shipping();
		} else {
			$shortcode['[yaymail_order_shipping]'] = 0;
		}
		$shortcode['[yaymail_shipping_address]'] = $shipping_address;
		if ( ! empty( $order->get_shipping_address_1() ) ) {
			$shortcode['[yaymail_shipping_address_1]'] = $order->get_shipping_address_1();
		} else {
			$shortcode['[yaymail_shipping_address_1]'] = '';
		}
		if ( ! empty( $order->get_shipping_address_2() ) ) {
			$shortcode['[yaymail_shipping_address_2]'] = $order->get_shipping_address_2();
		} else {
			$shortcode['[yaymail_shipping_address_2]'] = '';
		}
		if ( ! empty( $order->get_shipping_city() ) ) {
			$shortcode['[yaymail_shipping_city]'] = $order->get_shipping_city();
		} else {
			$shortcode['[yaymail_shipping_city]'] = '';
		}
		if ( ! empty( $order->get_shipping_company() ) ) {
			$shortcode['[yaymail_shipping_company]'] = $order->get_shipping_company();
		} else {
			$shortcode['[yaymail_shipping_company]'] = '';
		}
		if ( ! empty( $order->get_shipping_country() ) ) {
			$shortcode['[yaymail_shipping_country]'] = $order->get_shipping_country();
		} else {
			$shortcode['[yaymail_shipping_country]'] = '';
		}
		if ( ! empty( $order->get_shipping_first_name() ) ) {
			$shortcode['[yaymail_shipping_first_name]'] = $order->get_shipping_first_name();
		} else {
			$shortcode['[yaymail_shipping_first_name]'] = '';

		}
		if ( ! empty( $order->get_shipping_last_name() ) ) {
			$shortcode['[yaymail_shipping_last_name]'] = $order->get_shipping_last_name();
		} else {
			$shortcode['[yaymail_shipping_last_name]'] = '';

		}
		if ( ! empty( $order->get_shipping_method() ) ) {
			$shortcode['[yaymail_shipping_method]'] = $order->get_shipping_method();
		} else {
			$shortcode['[yaymail_shipping_method]'] = '';
		}
		if ( ! empty( $order->get_shipping_postcode() ) ) {
			$shortcode['[yaymail_shipping_postcode]'] = $order->get_shipping_postcode();
		} else {
			$shortcode['[yaymail_shipping_postcode]'] = '';
		}
		if ( ! empty( $order->get_shipping_state() ) ) {
			$shortcode['[yaymail_shipping_state]'] = $order->get_shipping_state();
		} else {
			$shortcode['[yaymail_shipping_state]'] = '';
		}

		// BILLINGS
		$shortcode['[yaymail_billing_address]'] = $billing_address;
		if ( ! empty( $order->get_billing_address_1() ) ) {
			$shortcode['[yaymail_billing_address_1]'] = $order->get_billing_address_1();
		} else {
			$shortcode['[yaymail_billing_address_1]'] = '';
		}
		if ( ! empty( $order->get_billing_address_2() ) ) {
			$shortcode['[yaymail_billing_address_2]'] = $order->get_billing_address_2();
		} else {
			$shortcode['[yaymail_billing_address_2]'] = '';
		}
		if ( ! empty( $order->get_billing_city() ) ) {
			$shortcode['[yaymail_billing_city]'] = $order->get_billing_city();
		} else {
			$shortcode['[yaymail_billing_city]'] = $order->get_billing_city();
		}
		if ( ! empty( $order->get_billing_company() ) ) {
			$shortcode['[yaymail_billing_company]'] = $order->get_billing_company();
		} else {
			$shortcode['[yaymail_billing_company]'] = '';
		}
		if ( ! empty( $order->get_billing_country() ) ) {
			$shortcode['[yaymail_billing_country]'] = $order->get_billing_country();
		} else {
			$shortcode['[yaymail_billing_country]'] = '';
		}
		if ( ! empty( $order->get_billing_email() ) ) {
			$shortcode['[yaymail_billing_email]'] = '<a style="color: inherit" href="mailto:' . $order->get_billing_email() . '">' . $order->get_billing_email() . '</a>';
		} else {
			$shortcode['[yaymail_billing_email]'] = '';
		}
		if ( ! empty( $order->get_billing_first_name() ) ) {
			$shortcode['[yaymail_billing_first_name]'] = $order->get_billing_first_name();
		} else {
			$shortcode['[yaymail_billing_first_name]'] = '';
		}
		if ( ! empty( $order->get_billing_last_name() ) ) {
			$shortcode['[yaymail_billing_last_name]'] = $order->get_billing_last_name();
		} else {
			$shortcode['[yaymail_billing_last_name]'] = '';
		}
		if ( ! empty( $order->get_billing_phone() ) ) {
			$shortcode['[yaymail_billing_phone]'] = $order->get_billing_phone();
		} else {
			$shortcode['[yaymail_billing_phone]'] = '';
		}
		if ( ! empty( $order->get_billing_postcode() ) ) {
			$shortcode['[yaymail_billing_postcode]'] = $order->get_billing_postcode();
		} else {
			$shortcode['[yaymail_billing_postcode]'] = '';
		}
		if ( ! empty( $order->get_billing_state() ) ) {
			$shortcode['[yaymail_billing_state]'] = $order->get_billing_state();
		} else {
			$shortcode['[yaymail_billing_state]'] = '';
		}

		// Reset Passwords
		$shortcode['[yaymail_password_reset_url]']        = '<a style="color: ' . $text_link_color . ';" href="' . esc_url( $resetURL ) . '">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
		$shortcode['[yaymail_password_reset_url_string]'] = esc_url( $resetURL );

		// New Users
		if ( isset( $args['email']->user_pass ) && ! empty( $args['email']->user_pass ) ) {
			$shortcode['[yaymail_user_new_password]'] = $args['email']->user_pass;
		} else {
			if ( isset( $_REQUEST['pass1-text'] ) && '' != $_REQUEST['pass1-text'] ) {
				$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
			} elseif ( isset( $_REQUEST['pass1'] ) && '' != $_REQUEST['pass1'] ) {
				$shortcode['[yaymail_user_new_password]'] = sanitize_text_field( $_REQUEST['pass1-text'] );
			} else {
				$shortcode['[yaymail_user_new_password]'] = '';
			}
		}
		// Review this code ??
		if ( isset( $args['email']->user_activation_url ) && ! empty( $args['email']->user_activation_url ) ) {
			$shortcode['[yaymail_user_activation_link]'] = $args['email']->user_activation_url;
		} else {
			$shortcode['[yaymail_user_activation_link]'] = '';
		}

		// GENERALS
		$shortcode['[yaymail_customer_note]']  = $customerNoteHtml;
		$shortcode['[yaymail_customer_notes]'] = $customerNoteHtmlList;
		if ( ! empty( $order->get_customer_note() ) ) {
			$shortcode['[yaymail_customer_provided_note]'] = $order->get_customer_note();
		} else {
			$shortcode['[yaymail_customer_provided_note]'] = '';
		}
		$shortcode['[yaymail_site_name]']       = get_bloginfo( 'name' );
		$shortcode['[yaymail_site_url]']        = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		$shortcode['[yaymail_site_url_string]'] = get_site_url();
		if ( isset( $user_data->user_email ) ) {
			$shortcode['[yaymail_user_email]'] = $user_data->user_email;
		} else {
			$shortcode['[yaymail_user_email]'] = $order->get_billing_email();
		}
		if ( isset( $shortcode['[yaymail_user_email]'] ) && '' != $shortcode['[yaymail_user_email]'] ) {
			$user                           = get_user_by( 'email', $shortcode['[yaymail_user_email]'] );
			$shortcode['[yaymail_user_id]'] = ( isset( $user->ID ) ) ? $user->ID : '';
		}
		if ( isset( $user_data->user_login ) && ! empty( $user_data->user_login ) ) {
			$shortcode['[yaymail_customer_username]'] = $user_data->user_login;
		} elseif ( isset( $user_data->user_nicename ) ) {
			$shortcode['[yaymail_customer_username]'] = $user_data->user_nicename;
		} else {
			$shortcode['[yaymail_customer_username]'] = $order->get_billing_first_name();
		}
		if ( isset( $user->ID ) && ! empty( $user->ID ) ) {
			$shortcode['[yaymail_customer_name]'] = get_user_meta( $user->ID, 'first_name', true ) . ' ' . get_user_meta( $user->ID, 'last_name', true );
		} elseif ( isset( $user_data->user_nicename ) ) {
			$shortcode['[yaymail_customer_name]'] = $user_data->user_nicename;
		} else {
			$shortcode['[yaymail_customer_name]'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		}
		if ( ! empty( $order->get_view_order_url() ) ) {
			$shortcode['[yaymail_view_order_url]']        = '<a href="' . $order->get_view_order_url() . '" style="color:' . $text_link_color . ';">view order</a>';
			$shortcode['[yaymail_view_order_url_string]'] = $order->get_view_order_url();
		} else {
			$shortcode['[yaymail_view_order_url]'] = '';
		}
		$shortcode['[yaymail_billing_shipping_address]'] = $this->billingShippingAddress( $shipping_address, $billing_address );

		$shortcode['[yaymail_billing_shipping_address_title]']   = $this->billingShippingAddressTitle( $shipping_address, $billing_address );
		$shortcode['[yaymail_billing_shipping_address_content]'] = $this->billingShippingAddressContent( $shipping_address, $billing_address );
		$shortcode['[yaymail_check_billing_shipping_address]']   = $this->checkBillingShippingAddress( $shipping_address, $billing_address );

		if ( ! empty( parse_url( get_site_url() )['host'] ) ) {
			$shortcode['[yaymail_domain]'] = parse_url( get_site_url() )['host'];
		} else {
			$shortcode['[yaymail_domain]'] = '';
		}

		$shortcode['[yaymail_user_account_url]']        = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
		$shortcode['[yaymail_user_account_url_string]'] = wc_get_page_permalink( 'myaccount' );

		// ADDITIONAL ORDER META:
		$order_metaArr = get_post_meta( $this->order_id );
		if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
			foreach ( $order_metaArr as $k => $v ) {
				$nameField    = str_replace( ' ', '_', trim( $k ) );
				$nameShorcode = '[yaymail_post_meta:' . $nameField . ']';

				// when array $v has tow value ???
				if ( is_array( $v ) && count( $v ) > 0 ) {
					  $shortcode[ $nameShorcode ] = trim( $v[0] );
				} else {
					$shortcode[ $nameShorcode ] = trim( $v );
				}
			}
		}

		/*
		 To get custom fields support Checkout Field Editor for WooCommerce */
		// funtion wc_get_custom_checkout_fields of Plugin Checkout Field Editor ????
		// if (!empty($order)) {
		// if (function_exists('wc_get_custom_checkout_fields')) {
		// $custom_fields = wc_get_custom_checkout_fields($order);
		// if (!empty($custom_fields)) {
		// foreach ($custom_fields as $key => $custom_field) {
		// $shortcode['[yaymail_' . $key . ']'] = get_post_meta($order->get_id(), $key, true);
		// }
		// }
		// }
		// }
		if ( ! empty( $order ) ) {
			foreach ( $order->get_meta_data() as $meta ) {
				$nameField    = str_replace( ' ', '_', trim( $meta->get_data()['key'] ) );
				$nameShorcode = '[yaymail_order_meta:' . $nameField . ']';
				if ( '_wc_shipment_tracking_items' == $nameField ) {
					$shortcode[ $nameShorcode ] = $this->wc_shipment_tracking_items( $postID );
				} else {
					if ( is_array( $meta->get_data()['value'] ) ) {
						$shortcode[ $nameShorcode ] = implode( ', ', ( $meta->get_data()['value'] ) );
					} else {
						$shortcode[ $nameShorcode ] = $meta->get_data()['value'];
					}
				}
			}
		}

		$this->order_data = $shortcode;
	}

	public function defaultSampleOrderData( $sent_to_admin = '' ) {
		$current_user     = wp_get_current_user();
		$postID           = CustomPostType::postIDByTemplate( $this->template );
		$text_link_color  = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
		$billing_address  = "John Doe<br/>YayCommerce<br/>7400 Edwards Rd<br/>Edwards Rd<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		$shipping_address = "John Doe<br/>YayCommerce<br/>755 E North Grove Rd<br/>Mayville, Michigan<br/><a href='tel:+18587433828' style='color: " . $text_link_color . "; font-weight: normal; text-decoration: underline;'>(910) 529-1147</a><br/>";
		$user_id          = get_current_user_id();

		// Link Downloadable Product
		$shortcode['[yaymail_items_downloadable_title]']   = $this->orderItemsDownloadableTitle( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_downloadable_product]'] = $this->orderItemsDownloadable( array(), $sent_to_admin, 'sampleOrder' );

		// ORDER DETAILS
		$shortcode['[yaymail_items_border]'] = $this->orderItemsBorder( array(), $sent_to_admin, 'sampleOrder' );

		$shortcode['[yaymail_items_border_before]']  = $this->orderItemsBorderBefore( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_border_after]']   = $this->orderItemsBorderAfter( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_border_title]']   = $this->orderItemsBorderTitle( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_border_content]'] = $this->orderItemsBorderContent( array(), $sent_to_admin, 'sampleOrder' );
		// YITH Subscription
		$shortcode['[yaymail_subscription_table]']       = $this->subscriptionDetailTable( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_subscription_table_title]'] = $this->subscriptionDetailTableTitle( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_subscription_header]']      = $this->subscriptionHeader( array(), $sent_to_admin, 'sampleOrder' );
		// WC HOOK
		$shortcode['[woocommerce_email_before_order_table]'] = $this->orderWoocommerceBeforeHook( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[woocommerce_email_after_order_table]']  = $this->orderWoocommerceAfterHook( array(), $sent_to_admin, 'sampleOrder' );
		// ORDER SUBSCRIPTION
		$shortcode['[yaymail_items_subscription_suspended]']   = $this->orderSubscriptionSuspended( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_subscription_expired]']     = $this->orderSubscriptionExpired( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_subscription_cancelled]']   = $this->orderSubscriptionCancelled( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_items_subscription_information]'] = $this->orderSubscriptionInformation( array(), $sent_to_admin, 'sampleOrder' );

		$shortcode['[yaymail_items]']           = $this->orderItems( array(), $sent_to_admin, 'sampleOrder' );
		$shortcode['[yaymail_order_date]']      = gmdate( 'd-m-Y' );
		$shortcode['[yaymail_order_fee]']       = 0;
		$shortcode['[yaymail_order_id]']        = 1;
		$shortcode['[yaymail_order_link]']      = '<a href="" style="color:' . $text_link_color . ';">' . esc_html__( 'Order', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_number]']    = '1';
		$shortcode['[yaymail_order_refund]']    = 0;
		$shortcode['[yaymail_order_sub_total]'] = '£18.00';
		$shortcode['[yaymail_order_total]']     = '£18.00';

		// PAYMENTS
		$shortcode['[yaymail_order_payment_method]']     = 'Direct bank transfer';
		$shortcode['[yaymail_order_payment_url]']        = '<a href="">' . esc_html__( 'Payment page', 'yaymail' ) . '</a>';
		$shortcode['[yaymail_order_payment_url_string]'] = '';
		$shortcode['[yaymail_payment_method]']           = 'Check payments';
		$shortcode['[yaymail_transaction_id]']           = 1;

		// SHIPPINGS
		$shortcode['[yaymail_order_shipping]']      = '333';
		$shortcode['[yaymail_shipping_address]']    = $shipping_address;
		$shortcode['[yaymail_shipping_address_1]']  = '755 E North Grove Rd';
		$shortcode['[yaymail_shipping_address_2]']  = '755 E North Grove Rd';
		$shortcode['[yaymail_shipping_city]']       = 'Mayville, Michigan';
		$shortcode['[yaymail_shipping_company]']    = 'YayCommerce';
		$shortcode['[yaymail_shipping_country]']    = '';
		$shortcode['[yaymail_shipping_first_name]'] = 'John';
		$shortcode['[yaymail_shipping_last_name]']  = 'Doe';
		$shortcode['[yaymail_shipping_method]']     = '';
		$shortcode['[yaymail_shipping_postcode]']   = '48744';
		$shortcode['[yaymail_shipping_state]']      = 'Random';

		// BILLING
		$shortcode['[yaymail_billing_address]']    = $billing_address;
		$shortcode['[yaymail_billing_address_1]']  = '7400 Edwards Rd';
		$shortcode['[yaymail_billing_address_2]']  = '7400 Edwards Rd';
		$shortcode['[yaymail_billing_city]']       = 'Edwards Rd';
		$shortcode['[yaymail_billing_company]']    = 'YayCommerce';
		$shortcode['[yaymail_billing_country]']    = '';
		$shortcode['[yaymail_billing_email]']      = 'johndoe@gmail.com';
		$shortcode['[yaymail_billing_first_name]'] = 'John';
		$shortcode['[yaymail_billing_last_name]']  = 'Doe';
		$shortcode['[yaymail_billing_phone]']      = '(910) 529-1147';
		$shortcode['[yaymail_billing_postcode]']   = '48744';
		$shortcode['[yaymail_billing_state]']      = 'Random';

		// RESET PASSWORD:
		$shortcode['[yaymail_password_reset_url]']        = '<a style="color:' . $text_link_color . ';" href="">' . esc_html__( 'Click here to reset your password', 'woocommerce' ) . '</a>';
		$shortcode['[yaymail_password_reset_url_string]'] = '';

		// NEW USERS:
		$shortcode['[yaymail_user_new_password]']    = 'G(UAM1(eIX#G';
		$shortcode['[yaymail_user_activation_link]'] = '';

		// GENERALS
		$shortcode['[yaymail_customer_note]']          = 'note';
		$shortcode['[yaymail_customer_notes]']         = 'notes';
		$shortcode['[yaymail_customer_provided_note]'] = 'provided note';
		$shortcode['[yaymail_site_name]']              = get_bloginfo( 'name' );
		$shortcode['[yaymail_site_url]']               = '<a href="' . get_site_url() . '"> ' . get_site_url() . ' </a>';
		$shortcode['[yaymail_site_url_string]']        = get_site_url();
		$shortcode['[yaymail_user_email]']             = $current_user->data->user_email;
		$shortcode['[yaymail_user_id]']                = $user_id;
		$shortcode['[yaymail_customer_username]']      = $current_user->data->user_login;
		$shortcode['[yaymail_customer_name]']          = get_user_meta( $current_user->data->ID, 'first_name', true ) . ' ' . get_user_meta( $current_user->data->ID, 'last_name', true );
		$shortcode['[yaymail_view_order_url]']         = '<a href=" " style="color:' . $text_link_color . ';">view order</a>';

		$shortcode['[yaymail_view_order_url_string]']    = '';
		$shortcode['[yaymail_billing_shipping_address]'] = $this->billingShippingAddress( $shipping_address, $billing_address );

		$shortcode['[yaymail_billing_shipping_address_title]']   = $this->billingShippingAddressTitle( $shipping_address, $billing_address );
		$shortcode['[yaymail_billing_shipping_address_content]'] = $this->billingShippingAddressContent( $shipping_address, $billing_address );
		$shortcode['[yaymail_check_billing_shipping_address]']   = $this->checkBillingShippingAddress( true, true );

		$shortcode['[yaymail_domain]']                  = parse_url( get_site_url() )['host'];
		$shortcode['[yaymail_user_account_url]']        = '<a style="color:' . $text_link_color . '; font-weight: normal; text-decoration: underline;" href="' . wc_get_page_permalink( 'myaccount' ) . '">' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '</a>';
		$shortcode['[yaymail_user_account_url_string]'] = wc_get_page_permalink( 'myaccount' );

		// ADDITIONAL ORDER META:
		$order         = CustomPostType::getListOrders();
		$order_metaArr = get_post_meta( $order[0]['id'] );
		if ( is_array( $order_metaArr ) && count( $order_metaArr ) > 0 ) {
			foreach ( $order_metaArr as $k => $v ) {
				$nameField    = str_replace( ' ', '_', trim( $k ) );
				$nameShorcode = '[yaymail_post_meta:' . $nameField . ']';

				// when array $v has tow value ???
				if ( is_array( $v ) && count( $v ) > 0 ) {
					$shortcode[ $nameShorcode ] = trim( $v[0] );
				} else {
					$shortcode[ $nameShorcode ] = trim( $v );
				}
			}
		}

		$this->order_data = $shortcode;
	}

	public function ordetItemTables( $order, $default_args ) {
		$items            = $order->get_items();
		$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-items.php';
		$yaymail_settings = get_option( 'yaymail_settings' );

		$show_product_image            = isset( $yaymail_settings['product_image'] ) ? $yaymail_settings['product_image'] : 0;
		$show_product_sku              = isset( $yaymail_settings['product_sku'] ) ? $yaymail_settings['product_sku'] : 0;
		$default_args['image_size'][0] = isset( $yaymail_settings['image_width'] ) ? $yaymail_settings['image_width'] : 32;
		$default_args['image_size'][1] = isset( $yaymail_settings['image_height'] ) ? $yaymail_settings['image_height'] : 32;
		$default_args['image_size'][2] = isset( $yaymail_settings['image_size'] ) ? $yaymail_settings['image_size'] : 'thumbnail';

		$args = array(
			'order'                         => $order,
			'items'                         => $order->get_items(),
			'show_download_links'           => $order->is_download_permitted() && ! $default_args['sent_to_admin'],
			'show_sku'                      => $show_product_sku,
			'show_purchase_note'            => $order->is_paid() && ! $default_args['sent_to_admin'],
			'show_image'                    => $show_product_image,
			'image_size'                    => $default_args['image_size'],
			'plain_text'                    => $default_args['plain_text'],
			'sent_to_admin'                 => $default_args['sent_to_admin'],
			'order_item_table_border_color' => isset( $yaymail_settings['background_color_table_items'] ) ? $yaymail_settings['background_color_table_items'] : '#dddddd',
		);
		include $path;
	}
	public function orderItemsBorder( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();

			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();

			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

	}

	public function subscriptionDetailTable( $items, $sent_to_admin = '', $checkOrder = '' ) {
		$order        = $this->order;
		$subscription = false;
		if ( 'sampleOrder' !== $checkOrder ) {
			foreach ( $order->get_meta_data() as $meta ) {
				$item_meta_data = $meta->get_data()['key'];
				if ( 'subscriptions' == $item_meta_data ) {
					$subscription = is_array( $meta->get_data()['value'] ) ? $meta->get_data()['value'][0] : $meta->get_data()['value'];
					break;
				}
			}
		}
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'YWSBS_Subscription' ) || false == $subscription ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-subscription-detail-table.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-subscription-detail-table.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	public function subscriptionDetailTableTitle( $items, $sent_to_admin = '', $checkOrder = '' ) {
		$order        = $this->order;
		$subscription = false;
		if ( 'sampleOrder' !== $checkOrder ) {
			foreach ( $order->get_meta_data() as $meta ) {
				$item_meta_data = $meta->get_data()['key'];
				if ( 'subscriptions' == $item_meta_data ) {
					$subscription = is_array( $meta->get_data()['value'] ) ? $meta->get_data()['value'][0] : $meta->get_data()['value'];
					break;
				}
			}
		}
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'YWSBS_Subscription' ) || false == $subscription ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-subscription-detail-table-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-subscription-detail-table-title.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	public function subscriptionHeader( $items, $sent_to_admin = '', $checkOrder = '' ) {
		$order        = $this->order;
		$subscription = false;
		if ( 'sampleOrder' !== $checkOrder ) {
			foreach ( $order->get_meta_data() as $meta ) {
				$item_meta_data = $meta->get_data()['key'];
				if ( 'subscriptions' == $item_meta_data ) {
					$subscription = is_array( $meta->get_data()['value'] ) ? $meta->get_data()['value'][0] : $meta->get_data()['value'];
					break;
				}
			}
		}
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'YWSBS_Subscription' ) || false == $subscription ) {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-subscription-header.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-subscription-header.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	/* Link Downloadable Product - start */
	public function orderItemsDownloadableTitle( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( $this->order ) {
			$order     = $this->order;
			$downloads = $order->get_downloadable_items();
		}
		ob_start();
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-item-download-title.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	public function orderItemsDownloadable( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-item-download.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$order     = $this->order;
			$downloads = $order->get_downloadable_items();
			$path      = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-item-download.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	/* Order items border - start */
	public function orderItemsBorderBefore( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '';
		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-before.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function orderItemsBorderAfter( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '';
		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-after.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function orderItemsBorderTitle( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-title.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-title.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	public function orderItemsBorderContent( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details-border-content.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details-border-content.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	/* Order items border - end */

	public function billingShippingAddress( $shipping_address, $billing_address ) {

		ob_start();
		$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address.php';
		$order = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;

	}

	/* Order Subscription  - start */
	public function orderSubscriptionSuspended( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'WC_Subscription' ) || ! class_exists( 'YWSBS_Subscription' ) ) {
			ob_start();
			$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-subscription.php';
			$order            = $this->order;
			$subscriptionType = 'suspended';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			$order           = $this->order;
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					foreach ( $arrSubscription as $subscriptionItem ) {
						$subscription = $subscriptionItem;
						ob_start();
						$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-on-hold-subscription.php';
						include $path;
						$html = ob_get_contents();
						ob_end_clean();
						return $html;
					}
				}
			} else {
				$subscription = wcs_get_subscription( $order->get_id() );
				if ( false != $subscription ) {
					ob_start();
					$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-on-hold-subscription.php';
					include $path;
					$html = ob_get_contents();
					ob_end_clean();
					return $html;
				} else {
					return '';
				}
			}
		}
	}

	public function orderSubscriptionExpired( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'WC_Subscription' ) || ! class_exists( 'YWSBS_Subscription' ) ) {
			ob_start();
			$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-subscription.php';
			$order            = $this->order;
			$subscriptionType = 'expired';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			$order           = $this->order;
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					foreach ( $arrSubscription as $subscriptionItem ) {
						$subscription = $subscriptionItem;
						ob_start();
						$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-expired-subscription.php';
						include $path;
						$html = ob_get_contents();
						ob_end_clean();
						return $html;
					}
				}
			} else {
				$subscription = wcs_get_subscription( $order->get_id() );
				if ( false != $subscription ) {
					ob_start();
					$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-expired-subscription.php';
					include $path;
					$html = ob_get_contents();
					ob_end_clean();
					return $html;
				} else {
					return '';
				}
			}
		}

	}

	public function orderSubscriptionCancelled( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'WC_Subscription' ) || ! class_exists( 'YWSBS_Subscription' ) ) {
			ob_start();
			$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-subscription.php';
			$order            = $this->order;
			$subscriptionType = 'cancelled';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			$order           = $this->order;
			$is_parent_order = wcs_order_contains_subscription( $order, 'parent' );
			if ( $is_parent_order ) {
				$arrSubscription = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
				if ( $arrSubscription ) {
					foreach ( $arrSubscription as $subscriptionItem ) {
						$subscription = $subscriptionItem;
						ob_start();
						$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-cancelled-subscription.php';
						include $path;
						$html = ob_get_contents();
						ob_end_clean();
						return $html;
					}
				}
			} else {
				$subscription = wcs_get_subscription( $order->get_id() );
				if ( false != $subscription ) {
					ob_start();
					$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-cancelled-subscription.php';
					include $path;
					$html = ob_get_contents();
					ob_end_clean();
					return $html;
				} else {
					return '';
				}
			}
		}

	}

	public function orderSubscriptionInformation( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder || ! class_exists( 'WC_Subscription' ) || ! class_exists( 'YWSBS_Subscription' ) ) {
			ob_start();
			$path             = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-subscription.php';
			$order            = $this->order;
			$subscriptionType = 'info';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		} else {
			$order         = $this->order;
			$subscriptions = wcs_get_subscriptions_for_order( $order->get_id(), array( 'order_type' => array( 'parent', 'renewal' ) ) );
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-subscription-info.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}

	/* Order Subscription  - end */

	/* Billing Shipping Address - start */
	public function billingShippingAddressTitle( $shipping_address, $billing_address ) {
		ob_start();
		$postID = CustomPostType::postIDByTemplate( $this->template );
		$path   = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-title.php';
		$order  = $this->order;
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	public function checkBillingShippingAddress( $shipping_address, $billing_address ) {
		$isShippingAddress = false;
		$isBillingAddress  = false;

		if ( ! empty( $billing_address ) ) {
			$isBillingAddress = true;
		}
		if ( ! empty( $shipping_address ) ) {
			$isShippingAddress = true;
		}

		$args = array(
			'isShippingAddress' => $isShippingAddress,
			'isBillingAddress'  => $isBillingAddress,
		);

		return $args;
	}

	public function billingShippingAddressContent( $shipping_address, $billing_address ) {
		ob_start();
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-billing-shipping-address-content.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
	/* Billing Shipping Address - end */

	public function orderItems( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/sampleOrder/email-order-details.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;

		} else {
			ob_start();
			$path  = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/email-order-details.php';
			$order = $this->order;
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

	}
	public function getOrderCustomerNotes( $customerNotes ) {
		ob_start();
		foreach ( $customerNotes as $customerNote ) {
			?>
			<!-- <div class="nta-web-note-content"> -->
				<?php echo wp_kses_post( $customerNote->comment_content ); ?>
			<!-- </div> -->
			<?php
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	/* Woo Shipment Tracking - Start */
	public function wc_shipment_tracking_items( $postID ) {
		ob_start();
		$order = $this->order;
		if ( ( ! class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) || ! $order ) {
			ob_end_clean();
			return null;
		}
		$setClassAvtive = null;

		$order_id = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : $order->id;
		if ( class_exists( 'WC_Shipment_Tracking_Actions' ) && ! class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$sta            = \WC_Shipment_Tracking_Actions::get_instance();
			$tracking_items = $sta->get_tracking_items( $order_id, true );
			$setClassAvtive = 'WC_Shipment_Tracking_Actions';
		}
		if ( class_exists( 'WC_Advanced_Shipment_Tracking_Actions' ) ) {
			$ast            = \WC_Advanced_Shipment_Tracking_Actions::get_instance();
			$tracking_items = $ast->get_tracking_items( $order_id );
			$setClassAvtive = 'WC_Advanced_Shipment_Tracking_Actions';
		}
		$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc_shipment_tracking-info.php';
		include $path;
		$html = ob_get_contents();
		ob_end_clean();
		return $html;

	}
	/*  Woo Shipment Tracking - End */

	/*  Woocommerce Hook - End */
	public function orderWoocommerceBeforeHook( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '[woocommerce_email_before_order_table]';
		} else {
			$order = $this->order;
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc-email-before.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
	public function orderWoocommerceAfterHook( $items, $sent_to_admin = '', $checkOrder = '' ) {
		if ( 'sampleOrder' === $checkOrder ) {
			return '[woocommerce_email_after_order_table]';
		} else {
			$order = $this->order;
			ob_start();
			$path = YAYMAIL_PLUGIN_PATH . 'views/templates/emails/wc-email-after.php';
			include $path;
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}
	}
}
