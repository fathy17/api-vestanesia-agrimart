<?php

namespace YayMail;

use YayMail\Helper\Helper;
use YayMail\Helper\LogHelper;
use YayMail\MailBuilder\Shortcodes;
use YayMail\Page\Source\CustomPostType;
use YayMail\Page\Source\DefaultElement;
use YayMail\Page\Source\UpdateElement;
use YayMail\Templates\Templates;

defined( 'ABSPATH' ) || exit;

class Ajax {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}
	private function doHooks() {
		add_action( 'wp_ajax_yaymail_send_mail', array( $this, 'sendTestMail' ) );
		add_action( 'wp_ajax_yaymail_parse_template', array( new Shortcodes(), 'templateParser' ) );

		add_action( 'wp_ajax_yaymail_save_mail', array( $this, 'saveTemplate' ) );
		add_action( 'wp_ajax_yaymail_copy_mail', array( $this, 'copyTemplate' ) );
		add_action( 'wp_ajax_yaymail_reset_template', array( $this, 'resetTemplate' ) );
		add_action( 'wp_ajax_yaymail_export_all_template', array( $this, 'exportAllTemplate' ) );
		add_action( 'wp_ajax_yaymail_import_template', array( $this, 'importAllTemplate' ) );
		add_action( 'wp_ajax_yaymail_enable_disable_template', array( $this, 'enableDisableTempalte' ) );
		add_action( 'wp_ajax_yaymail_general_setting', array( $this, 'generalSettings' ) );
	}

	private function __construct() {}
	// public function showMailError($wp_error)
	// {
	// wp_send_json_error(array('mess' => $wp_error));
	// }
	public function exportAllTemplate() {
		try {
			// 1. check nonce
			Helper::checkNonce();
			// 2. download
			$template_export = CustomPostType::getTemplateExport();
			$fileName        = 'yaymail_all-customize-email-templates_' . gmdate( 'm-d-Y' ) . '.json';
			header( 'Content-Type: application/json' );
			header( 'Content-Disposition: attachment; filename="' . $fileName . '";' );
			$response_object = array(
				'result'   => $template_export,
				'fileName' => $fileName,
				'mess'     => __( 'Export successfully.', 'yaymail' ),
			);
			wp_send_json_success( $response_object );
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

	public static function getHtmlByElements( $postID, $args = array() ) {
		$updateElement        = new UpdateElement();
		$yaymail_elements     = get_post_meta( $postID, '_yaymail_elements', true );
		$yaymail_elements     = $updateElement->merge_new_props_to_elements( $yaymail_elements );
		$yaymail_settings     = get_option( 'yaymail_settings' );
		$emailBackgroundColor = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : '#ECECEC';
		$general_attrs        = array( 'tableWidth' => str_replace( 'px', '', $yaymail_settings['container_width'] ) );
		$html                 = '<!DOCTYPE html>
			<html lang="en">
			<head>
				<meta charset="UTF-8">
				<meta http-equiv="X-UA-Compatible" content="IE=edge">
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
				<style>
				h1{ font-family:inherit;text-shadow:unset;text-align:inherit;}
				h2,h3{ font-family:inherit;color:inherit;text-align:inherit;}
				</style>
			</head><body style="background:' . esc_html( $emailBackgroundColor ) . '">';
		foreach ( $yaymail_elements as $key => $element ) {
			// add shortcode params
			$reg_pattern = '/\[([a-z0-9A-Z_]+)\]/';
			if ( isset( $element['settingRow']['content'] ) ) {
				$content      = $element['settingRow']['content'];
				$contentTitle = isset( $element['settingRow']['contentTitle'] ) ? $element['settingRow']['contentTitle'] : '';

				// Add $atts for content if has shortcode
				preg_match_all( $reg_pattern, $content, $result );
				if ( ! empty( $result[0] ) ) {
					foreach ( $result[0] as $key => $shortcode ) {
						$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
						$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
						$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
						$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
						$newshortcode  = substr( $shortcode, 0, -1 );
						$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
						$content       = str_replace( $shortcode, $newshortcode, $content );
					}
					$element['settingRow']['content'] = $content;
				}
				// Add $atts for contentTitle if has shortcode
				if ( $contentTitle ) {
					preg_match_all( $reg_pattern, $contentTitle, $result );
					if ( ! empty( $result[0] ) ) {
						foreach ( $result[0] as $key => $shortcode ) {
							$textcolor     = isset( $element['settingRow']['textColor'] ) ? ' textcolor=' . $element['settingRow']['textColor'] : '';
							$bordercolor   = isset( $element['settingRow']['borderColor'] ) ? ' bordercolor=' . $element['settingRow']['borderColor'] : '';
							$titlecolor    = isset( $element['settingRow']['titleColor'] ) ? ' titlecolor=' . $element['settingRow']['titleColor'] : '';
							$fontfamily    = isset( $element['settingRow']['family'] ) ? ' fontfamily=' . str_replace( ' ', '', str_replace( array( '\'', '"' ), '', $element['settingRow']['family'] ) ) : '';
							$newshortcode  = substr( $shortcode, 0, -1 );
							$newshortcode .= $textcolor . $bordercolor . $titlecolor . $fontfamily . ']';
							$contentTitle  = str_replace( $shortcode, $newshortcode, $contentTitle );
						}
						$element['settingRow']['contentTitle'] = $contentTitle;
					}
				}
			}
			ob_start();
			do_action( 'Yaymail' . $element['type'], $args, $element['settingRow'], $general_attrs, $element['id'], $postID );
			$el_html = ob_get_clean();
			$html   .= $el_html;
		}
		$html .= '</body></html>';
		return $html;
	}

	// html output of mail must to map with html output in single-mail-template.php
	public function sendTestMail() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['order_id'] ) && isset( $_POST['template'] ) && isset( $_POST['email_address'] ) ) {
					// Helper::checkNonce();
					$template      = sanitize_text_field( $_POST['template'] );
					$email_address = sanitize_email( $_POST['email_address'] );
					// check email
					if ( ! is_email( $email_address ) ) {
						wp_send_json_error( array( 'mess' => __( 'Invalid email format!', 'yaymail' ) ) );
					}
					if ( CustomPostType::postIDByTemplate( $template ) ) {
						update_user_meta( get_current_user_id(), 'yaymail_default_email_test', $email_address );
						$customShortcode = new Shortcodes( $template, sanitize_text_field( $_POST['order_id'] ) );
						if ( sanitize_text_field( $_POST['order_id'] ) !== 'sampleOrder' ) {
							$order_id = intval( sanitize_text_field( $_POST['order_id'] ) );
							$WC_order = new \WC_Order( $order_id );
						}
						$postID   = CustomPostType::postIDByTemplate( $template );
						$order_tn = 'mail-sent';
						$customShortcode->setOrderId( $order_id, true );
						$customShortcode->shortCodesOrderDefined();
						if ( isset( $WC_order ) ) {
							$html = self::getHtmlByElements( $postID, array( 'order' => $WC_order ) );
						} else {
							$html = self::getHtmlByElements( $postID, array( 'order' => 'SampleOrder' ) );
						}

						// $html         = do_shortcode( get_post_meta( $postID, '_yaymail_html', true ) );
						$html         = html_entity_decode( $html, ENT_QUOTES, 'UTF-8' );
						$headers      = "Content-Type: text/html\r\n";
						$sendMail     = \WC_Emails::instance();
						$subjectEmail = $this->getSubjectEmail( $sendMail, $template );
						// $email_admin = get_bloginfo('admin_email');
						if ( ! empty( $email_address ) ) {
							$sendMailSucc = $sendMail->send( $email_address, $subjectEmail, $html, $headers, array() );
							wp_send_json_success(
								array(
									'sendMailSucc' => $sendMailSucc,
									'mess'         => __(
										'Email has been sent.',
										'yaymail'
									),
								)
							);
						}
					} else {
						wp_send_json_error( array( 'mess' => __( 'Template not Exists!.', 'yaymail' ) ) );
					}
				}
			}
			wp_send_json_error( array( 'mess' => __( 'Error send mail!', 'yaymail' ) ) );
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

	public function getSubjectEmail( $wc_emails, $template ) {
		foreach ( $wc_emails->emails as $email => $item ) {
			if ( $item->id == $template ) {
				if ( 'customer_invoice' == $template ) {
					$subject = Helper::getCustomerInvoiceSubject( $wc_emails->emails[ $email ] );
				} if ( 'new_booking' == $template ) {
					$subject = Helper::getNewBookingSubject( $wc_emails->emails[ $email ] );
				} else {
					$subject = $wc_emails->emails[ $email ]->get_subject();
				}

				return $subject;
			}
		}
		return null;
	}

	public function saveTemplate() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['template'] ) && isset( $_POST['htmlContent'] ) ) {
					// Helper::checkNonce();
					$emailBackgroundColor = isset( $_POST['emailBackgroundColor'] ) ? sanitize_text_field( $_POST['emailBackgroundColor'] ) : 'rgb(236, 236, 236)';
					$emailTextLinkColor   = isset( $_POST['emailTextLinkColor'] ) ? sanitize_text_field( $_POST['emailTextLinkColor'] ) : '#96588a';
					$titleShipping        = isset( $_POST['titleShipping'] ) ? sanitize_text_field( $_POST['titleShipping'] ) : 'Shipping Address';
					$titleBilling         = isset( $_POST['titleBilling'] ) ? sanitize_text_field( $_POST['titleBilling'] ) : 'Billing Address';
					$orderTitle           = isset( $_POST['orderTitle'] ) ? filter_var_array( $_POST['orderTitle'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$template             = sanitize_text_field( $_POST['template'] );
					$htmlContent          = filter_var( $_POST['htmlContent'], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_STRIP_LOW );
					$updateElement        = new UpdateElement();
					$setDefaultLogo       = isset( $_POST['setDefaultLogo'] ) ? 'true' == sanitize_text_field( $_POST['setDefaultLogo'] ) ? true : false : false;
					$setDefaultFooter     = isset( $_POST['setDefaultFooter'] ) ? 'true' == sanitize_text_field( $_POST['setDefaultFooter'] ) ? true : false : false;
					if ( isset( $_POST['emailContents'] ) ) {
						$emailContents = filter_var_array( $_POST['emailContents'] );
						$emailContents = $updateElement->merge_new_props_to_elements( $emailContents );
					} else {
						$emailContents = array();
					}
					// $emailContents = isset( $_POST['emailContents'] ) ? filter_var_array( $_POST['emailContents'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					foreach ( $emailContents as $key => $value ) {
						if ( 'TwoColumns' == $value['type'] || 'ThreeColumns' == $value['type'] || 'FourColumns' == $value['type'] ) {
							if ( ! array_key_exists( 'column1', $emailContents[ $key ]['settingRow'] ) ) {
								$emailContents[ $key ]['settingRow']['column1'] = array();
							}
							if ( ! array_key_exists( 'column2', $emailContents[ $key ]['settingRow'] ) ) {
								$emailContents[ $key ]['settingRow']['column2'] = array();
							}
							if ( ( 'ThreeColumns' == $value['type'] || 'FourColumns' == $value['type'] ) && ! array_key_exists( 'column3', $emailContents[ $key ]['settingRow'] ) ) {
								$emailContents[ $key ]['settingRow']['column3'] = array();
							}
							if ( 'FourColumns' == $value['type'] && ! array_key_exists( 'column4', $emailContents[ $key ]['settingRow'] ) ) {
								$emailContents[ $key ]['settingRow']['column4'] = array();
							}
						}
					}
					if ( CustomPostType::postIDByTemplate( $template ) ) {
						$postID = CustomPostType::postIDByTemplate( $template );
						// write Log use to check error
						// LogHelper::writeLogContent($htmlContent, 'html');
						// LogHelper::writeLogContent($emailContents, 'text');

						update_post_meta( $postID, '_yaymail_html', $htmlContent );
						update_post_meta( $postID, '_yaymail_elements', $emailContents );
						update_post_meta( $postID, '_email_backgroundColor_settings', $emailBackgroundColor );
						update_post_meta( $postID, '_yaymail_email_textLinkColor_settings', $emailTextLinkColor );
						update_post_meta( $postID, '_email_title_shipping', $titleShipping );
						update_post_meta( $postID, '_email_title_billing', $titleBilling );
						update_post_meta( $postID, '_yaymail_email_order_item_title', $orderTitle );
						// Change default logo
						$default_logo = array(
							'set_default' => (bool) $setDefaultLogo,
						);
						if ( 'true' == $setDefaultLogo ) {
							$posts = CustomPostType::getListPostTemplate();
							foreach ( $emailContents as $key => $element ) {
								if ( 'Logo' == $element['type'] ) {
									$logoDefault = $element['settingRow'];
									break;
								}
							}

							if ( count( $posts ) > 0 && isset( $logoDefault ) ) {
								foreach ( $posts as $post ) {
									$yaymail_elements = get_post_meta( $post->ID, '_yaymail_elements', true );
									foreach ( $yaymail_elements as $key => $element ) {
										if ( 'Logo' == $element['type'] ) {
											$yaymail_elements[ $key ]['settingRow'] = wp_parse_args( $logoDefault, $yaymail_elements[ $key ]['settingRow'] );
										}
									}
									update_post_meta( $post->ID, '_yaymail_elements', $yaymail_elements );
								}
							}
						}
						update_option( 'yaymail_settings_default_logo', $default_logo );
						// Change default footer
						$default_footer = array(
							'set_default' => (bool) $setDefaultFooter,
						);
						if ( 'true' == $setDefaultFooter ) {
							$posts = CustomPostType::getListPostTemplate();
							foreach ( $emailContents as $key => $element ) {
								if ( 'ElementText' == $element['type'] && 'Footer' == $element['nameElement'] ) {
									$footerDefault = $element['settingRow'];
									break;
								}
							}

							if ( count( $posts ) > 0 && isset( $footerDefault ) ) {
								foreach ( $posts as $post ) {
									$yaymail_elements = get_post_meta( $post->ID, '_yaymail_elements', true );
									foreach ( $yaymail_elements as $key => $element ) {
										if ( 'ElementText' == $element['type'] && 'Footer' == $element['nameElement'] ) {
											$yaymail_elements[ $key ]['settingRow'] = wp_parse_args( $footerDefault, $yaymail_elements[ $key ]['settingRow'] );
										}
									}
									update_post_meta( $post->ID, '_yaymail_elements', $yaymail_elements );
								}
							}
						}
						update_option( 'yaymail_settings_default_footer', $default_footer );

						wp_send_json_success( array( 'mess' => __( 'Email has been saved.', 'yaymail' ) ) );
					} else {
						wp_send_json_error( array( 'mess' => __( 'Template not Exists!.', 'yaymail' ) ) );
					}
				}
				wp_send_json_error( array( 'mess' => __( 'Error save data.', 'yaymail' ) ) );
			}
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}
	public function copyTemplate() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['copy_to'] ) && isset( $_POST['copy_from'] ) ) {
					Helper::checkNonce();
					$copyTo   = sanitize_text_field( $_POST['copy_to'] );
					$copyFrom = sanitize_text_field( $_POST['copy_from'] );
					if ( CustomPostType::postIDByTemplate( $copyFrom ) ) {
						$postID               = CustomPostType::postIDByTemplate( $copyFrom );
						$htmlFrom             = get_post_meta( $postID, '_yaymail_html', true );
						$emailContentsFrom    = get_post_meta( $postID, '_yaymail_elements', true );
						$emailBackgroundColor = get_post_meta( $postID, '_email_backgroundColor_settings', true ) ? get_post_meta( $postID, '_email_backgroundColor_settings', true ) : 'rgb(236, 236, 236)';
						$emailTextLinkColor   = get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) ? get_post_meta( $postID, '_yaymail_email_textLinkColor_settings', true ) : '#96588a';
						$titleShipping        = isset( $_POST['titleShipping'] ) ? sanitize_text_field( $_POST['titleShipping'] ) : 'Shipping Address';
						$titleBilling         = isset( $_POST['titleBilling'] ) ? sanitize_text_field( $_POST['titleBilling'] ) : 'Billing Address';
						$orderTitle           = isset( $_POST['orderTitle'] ) ? filter_var_array( $_POST['orderTitle'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						if ( CustomPostType::postIDByTemplate( $copyTo ) ) {
							$idTo = CustomPostType::postIDByTemplate( $copyTo );
							update_post_meta( $idTo, '_yaymail_html', $htmlFrom );
							update_post_meta( $idTo, '_yaymail_elements', $emailContentsFrom );
							update_post_meta( $idTo, '_email_backgroundColor_settings', $emailBackgroundColor );
							update_post_meta( $idTo, '_yaymail_email_textLinkColor_settings', $emailTextLinkColor );
							update_post_meta( $idTo, '_email_title_shipping', $titleShipping );
							update_post_meta( $idTo, '_email_title_billing', $titleBilling );
							update_post_meta( $idTo, '_yaymail_email_order_item_title', $orderTitle );
							wp_send_json_success(
								array(
									'mess' => __( 'Copied Template successfully.', 'yaymail' ),
									'data' => $emailContentsFrom,
								)
							);
						} else {
							wp_send_json_error( array( 'mess' => __( 'Template not Exists!.', 'yaymail' ) ) );
						}
					} else {
						wp_send_json_error( array( 'mess' => __( 'Template not Exists!.', 'yaymail' ) ) );
					}
				}
				wp_send_json_error( array( 'mess' => __( 'Error save data.', 'yaymail' ) ) );
			}
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

	public function resetTemplate() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['template'] ) ) {
					// Helper::checkNonce();
					$reset         = sanitize_text_field( $_POST['template'] );
					$templateEmail = \YayMail\Templates\Templates::getInstance();
					$templates     = $templateEmail::getList();
					$orderTitle    = array(
						'order_title'                   => '',
						'product_title'                 => 'Product',
						'quantity_title'                => 'Quantity',
						'price_title'                   => 'Price',
						'subtoltal_title'               => 'Subtotal:',
						'payment_method_title'          => 'Payment method:',
						'total_title'                   => 'Total:',
						'subscript_id'                  => 'ID',
						'subscript_start_date'          => 'Start date',
						'subscript_end_date'            => 'End date',
						'subscript_recurring_total'     => 'Recurring total',
						'subscript_subscription'        => 'Subscription',
						'subscript_price'               => 'Price',
						'subscript_last_order_date'     => 'Last Order Date',
						'subscript_end_of_prepaid_term' => 'End of Prepaid Term',
						'subscript_date_suspended'      => 'Date Suspended',
					);
					if ( 'all' == $reset ) {
						foreach ( $templates as $key => $template ) {
							if ( CustomPostType::postIDByTemplate( $key ) ) {
								$postID = CustomPostType::postIDByTemplate( $key );
								update_post_meta( $postID, '_yaymail_html', $template['html'] );
								update_post_meta( $postID, '_yaymail_elements', json_decode( $template['elements'], true ) );
								update_post_meta( $postID, '_email_backgroundColor_settings', 'rgb(236, 236, 236)' );
								update_post_meta( $postID, '_yaymail_email_textLinkColor_settings', '#96588a' );
								update_post_meta( $postID, '_email_title_shipping', __( 'Shipping Address', 'yaymail' ) );
								update_post_meta( $postID, '_email_title_billing', __( 'Billing Address', 'yaymail' ) );
								update_post_meta( $postID, '_yaymail_email_order_item_title', $orderTitle );
							}
						}

						if ( get_option( 'yaymail_settings' ) ) {
							$yaymail_settings                    = get_option( 'yaymail_settings' );
							$yaymail_settings['container_width'] = '605px';
							$yaymail_settings['direction_rtl'] = 'ltr';
							update_option( 'yaymail_settings', $yaymail_settings );
						}

						wp_send_json_success( array( 'mess' => __( 'Template reset successfully.', 'yaymail' ) ) );
					} else {
						if ( CustomPostType::postIDByTemplate( $reset ) && isset( $templates[ $reset ] ) ) {
							$postID = CustomPostType::postIDByTemplate( $reset );
							update_post_meta( $postID, '_yaymail_html', $templates[ $reset ]['html'] );
							update_post_meta( $postID, '_yaymail_elements', json_decode( $templates[ $reset ]['elements'], true ) );
							update_post_meta( $postID, '_email_backgroundColor_settings', 'rgb(236, 236, 236)' );
							update_post_meta( $postID, '_yaymail_email_textLinkColor_settings', '#96588a' );
							update_post_meta( $postID, '_email_title_shipping', __( 'Shipping Address', 'yaymail' ) );
							update_post_meta( $postID, '_email_title_billing', __( 'Billing Address', 'yaymail' ) );
							update_post_meta( $postID, '_yaymail_email_order_item_title', $orderTitle );
							wp_send_json_success( array( 'mess' => __( 'Template reset successfully.', 'yaymail' ) ) );
						} else {
							wp_send_json_error( array( 'mess' => __( 'Template not Exists!.', 'yaymail' ) ) );
						}
					}
				}
				wp_send_json_error( array( 'mess' => __( 'Error Reset Template!', 'yaymail' ) ) );
			}
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

	public function importAllTemplate() {
		try {
			Helper::checkNonce();
			if ( isset( $_FILES['file']['type'] ) ) {
				if ( 'application/json' == $_FILES['file']['type'] ) {
					if ( ! empty( $_FILES['file']['tmp_name'] ) ) {
						$fileJson    = sanitize_text_field( $_FILES['file']['tmp_name'] );
						$data        = file_get_contents( $fileJson );
						$data        = json_decode( $data, true );
						$dataImports = $data['yaymailTemplateExport'];

						$versionOld     = $data['yaymail_version'];
						$versionCurrent = YAYMAIL_VERSION;

						/*
						check key in settingRow whether or not it exists.
						note: case when add setting row for element.
						 */
						if ( $versionOld != $versionCurrent ) {
							$element             = new DefaultElement();
							$defaultDataElements = $element->defaultDataElement;

							foreach ( $defaultDataElements as $defaultelement ) {

								foreach ( $dataImports as $keyTemplate => $templateImport ) {
									foreach ( $templateImport['_yaymail_elements'] as $keyElem => $elemImport ) {
										if ( $defaultelement['type'] == $elemImport['type'] ) {
											/*
											@@@ add key default for element
											*/
											$keyEleDefaus = array();
											$keyEleDefaus = array_diff_key( $defaultelement, $elemImport );
											if ( count( $keyEleDefaus ) > 0 ) {
														$dataImports[ $keyTemplate ]['_yaymail_elements'][ $keyElem ] = array_merge( $elemImport, $keyEleDefaus );
											}

											/*
											add key default for setting row
											note: when add a field in setting row
											*/
											$propSettings    = array();
											$propSettings    = array_diff_key( $defaultelement['settingRow'], $elemImport['settingRow'] );
											$lenPropSettings = count( $propSettings );
											if ( $lenPropSettings > 0 ) {
												$result = array();
												$result = array_merge( $elemImport['settingRow'], $propSettings );
												$dataImports[ $keyTemplate ]['_yaymail_elements'][ $keyElem ]['settingRow'] = $result;
											}

											/*
											remove Key not needed for setting row
											note: when deleting a field in setting row
											*/
											// $propSetNotNeeds = array();
											// $propSetNotNeeds = array_diff_key($elemImport['settingRow'], $defaultelement['settingRow']);
											// $lenpropSetNotNeeds = count($propSetNotNeeds);
											// if ($lenpropSetNotNeeds > 0) {
											// foreach ($propSetNotNeeds as $keyNotNeed => $propSetNotNeed) {
											// unset($dataImports[$keyTemplate]['_yaymail_elements'][$keyElem]['settingRow'][$keyNotNeed]);
											// }
											// }

										}
									}
								}
							}
						}
						$flag = false;
						if ( count( $dataImports ) > 0 ) {
							foreach ( $dataImports as $key => $value ) {
								if ( isset( $value['_yaymail_html'] ) && isset( $value['_yaymail_elements'] ) ) {
										$template = $value['_yaymail_template'];
									if ( CustomPostType::postIDByTemplate( $template ) ) {
										$pID = CustomPostType::postIDByTemplate( $template );
										update_post_meta( $pID, '_yaymail_html', $value['_yaymail_html'] );
										update_post_meta( $pID, '_yaymail_elements', $value['_yaymail_elements'] );
									} else {
										$array = array(
											'mess'        => '',
											'post_date'   => current_time( 'Y-m-d H:i:s' ),
											'post_type'   => 'yaymail_template',
											'post_status' => 'publish',
											'_yaymail_template' => $template,
											'_yaymail_html' => $value['_yaymail_html'],
											'_yaymail_elements' => $value['_yaymail_elements'],

										);
										$insert = CustomPostType::insert( $array );
									}
									$flag = true;
								}
							}
						}
						if ( ! $flag ) {
							  wp_send_json_error( array( 'mess' => __( 'Import Failed.', 'yaymail' ) ) );
						}
						wp_send_json_success( array( 'mess' => __( 'Imported successfully.', 'yaymail' ) ) );
					} else {
						wp_send_json_error( array( 'mess' => __( 'File not found.', 'yaymail' ) ) );
					}
				} else {
					wp_send_json_error( array( 'mess' => __( 'File not correct format.', 'yaymail' ) ) );
				}
			}
			wp_send_json_error( array( 'mess' => __( 'Please upload 1 file to import.', 'yaymail' ) ) );
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}
	public function enableDisableTempalte() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['settings'] ) ) {
					// Helper::checkNonce();
					$settingDefault = CustomPostType::templateEnableDisable();
					$listTemplates  = ! empty( $settingDefault ) ? array_keys( $settingDefault ) : array();
					$settingCurrent = wp_unslash( $_POST['settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					
					if ( ! empty( $listTemplates ) ) {
						foreach ( $settingCurrent as $key => $value ) {
							if ( in_array( $key, $listTemplates ) ) {
								update_post_meta( $settingDefault[ $key ]['post_id'], '_yaymail_status', $value );
							}
						}
					}
					wp_send_json_success( array( 'mess' => __( 'Settings saved.', 'yaymail' ) ) );
				}
				wp_send_json_error( array( 'mess' => __( 'Settings Failed!.', 'yaymail' ) ) );
			}
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

	public function generalSettings() {
		try {
			$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';
			if ( ! wp_verify_nonce( $nonce, 'email-nonce' ) ) {
				wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
			} else {
				if ( isset( $_POST['settings'] ) ) {
					$setting           = wp_unslash( $_POST['settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$yaymail_direction = $setting['direction_rtl'];
					isset( $yaymail_direction ) ? update_option( 'yaymail_direction', $yaymail_direction ) : update_option( 'yaymail_direction', 'ltr' );
					// Helper::checkNonce();
					update_option( 'yaymail_settings', wp_unslash( $_POST['settings'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					wp_send_json_success( array( 'mess' => __( 'Settings saved.', 'yaymail' ) ) );
				}
				wp_send_json_error( array( 'mess' => __( 'Settings Failed!.', 'yaymail' ) ) );
			}
		} catch ( \Exception $ex ) {
			LogHelper::getMessageException( $ex, true );
		} catch ( \Error $ex ) {
			LogHelper::getMessageException( $ex, true );
		}

	}

}
