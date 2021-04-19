<?php
namespace YayMail\Templates\DefaultTemplate;

defined( 'ABSPATH' ) || exit;

class CustomerProcessingOrder {

	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public static function getTemplates() {
		/*
		@@@ Html default send email.
		@@@ Note: Add characters '\' before special characters in a string.
		@@@ Example: font-family: \'Helvetica Neue\'...
		*/

		$emailTitle          = __( 'Thank you for your order', 'woocommerce' );
		$emailHi             = esc_html__( 'Hi ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_billing_first_name]' ) . ',' );
		$emailtext           = esc_html__( 'Just to let you know &mdash; we\'ve received your order #', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_order_id]' ) ) . esc_html__( ', and it is now being processed:', 'woocommerce' );
		$additionalContent   = __( 'Thanks for using {site_url}!', 'woocommerce' );
		$additionalContent   = str_replace( '{site_url}!', '', $additionalContent );
		$textShippingAddress = __( 'Shipping Address', 'woocommerce' );
		$textBillingAddress  = __( 'Billing Address', 'woocommerce' );

		// Html
		$html = '
      <html>
        <head>
          <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        </head>
        <body style="background: rgb(236, 236, 236); padding: 0;">
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="web8ffa62b5-7258-42cc-ba53-7ae69638c1fe" class="web-main-row" style="display: table; background-color: rgb(236, 236, 236); width: 605px;">
              <tbody >
                <tr >
                    <td  id="web8ffa62b5-7258-42cc-ba53-7ae69638c1fe-img" align="center" class="web-img-wrap" style="word-break: break-word; padding: 15px 50px;"><a  href="#" target="_blank" style="border: none; text-decoration: none;"><img  border="0" tabindex="0" src="' . YAYMAIL_PLUGIN_URL . 'assets/dist/images/woocommerce-logo.png" class="web-img" width="172" height="auto"></a></td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="web802bfe24-7af8-48af-ac5e-6560a81345b3" class="web-main-row" style="display: table; background-color: #96588a; width: 605px;">
              <tbody >
                <tr >
                    <td  id="web802bfe24-7af8-48af-ac5e-6560a81345b3-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 36px 48px;">
                      <div >
                          <h1 style="font-size: 30px; font-weight: 300; line-height: normal; margin: 0; color: #fff;">' . $emailTitle . '</h1>
                      </div>
                    </td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="webb035d1f1-0cfe-41c5-b79c-0478f144ef5f" class="web-main-row" style="display: table; background-color: rgb(255, 255, 255); width: 605px;">
              <tbody >
                <tr >
                    <td  id="webb035d1f1-0cfe-41c5-b79c-0478f144ef5f-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 47px 50px 0px;">
                      <div >
                          <p style="margin: 0 0 16px"><span style="color: #636363; font-size: 14px;">' . $emailHi . '</span></p>
                          <p style="margin: 0 0 16px"><span style="color: #636363; font-size: 14px;">' . $emailtext . '</span></p>
                      </div>
                    </td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="webad422370-f762-4a26-92de-c4cf3821b8eb" class="web-main-row" style="display: table; background-color: rgb(255, 255, 255); width: 605px;">
              <tbody >
                <tr >
                    <td  id="web-ad422370-f762-4a26-92de-c4cf3821b8eb-order-item" align="left" class="web-order-item" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 2px 50px 15px;">
                      <div class="yaymail_items_border_default" style="color: rgb(99, 99, 99);border-color: #e5e5e5;">
                          <p></p>
                            [yaymail_items_border]
                          <p></p>
                      </div>
                    </td>
                </tr>
              </tbody>
            </table>';
		if ( class_exists( 'WC_Subscription' ) ) {
			$html .= '<table width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="weso422370-f762-4a26-92de-c4cf3821b8eb" class="web-main-row" style="display: table; background-color: rgb(255, 255, 255); width: 605px;">
                  <tbody>
                      <tr>
                          <td id="web-so422370-f762-4a26-92de-c4cf3821b8eb-order-item" align="left" class="web-order-item" style="font-size: 13px; line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 15px 50px;">
                              <div style="color: rgb(99, 99, 99);border-color: #e5e5e5;">
                                  <p></p>
                                  [yaymail_items_subscription_information]
                                  <p></p>
                              </div>
                          </td>
                      </tr>
                  </tbody>
              </table>';
		}
			$html .= '<table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="webcd546d92-1791-4dcc-8d35-0ea4f20718eb" class="web-main-row" style="display: table; background-color: rgb(255, 255, 255); width: 605px;">
              <tbody >
                <tr >
                    <td  id="web-cd546d92-1791-4dcc-8d35-0ea4f20718eb-billing-address" align="left" class="web-billing-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding: 12px 50px 15px;">
                      <div style="color: rgb(99, 99, 99);">
                        <p>
                          [yaymail_billing_shipping_address]
                        </p>
                      </div
                    </td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="webb39bf2e6-8c1a-4384-a5ec-37663da27c8d" class="web-main-row" style="display: table; background-color: rgb(255, 255, 255); width: 605px;">
              <tbody >
                <tr >
                    <td  id="webb39bf2e6-8c1a-4384-a5ec-37663da27c8d-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Helvetica, Roboto, Arial, sans-serif; padding-bottom: 38px; padding-right: 50px; padding-left: 50px;">
                      <div >
                          <p><span style="font-size: 14px; color: #636363;">' . $additionalContent . '[yaymail_domain]!</span></p>
                      </div>
                    </td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="webea509383-1c35-4ab6-8169-dae1807f847b" class="web-main-row" style="display: table; background-color: rgb(236, 236, 236); width: 605px;">
              <tbody >
                <tr >
                    <td  id="webea509383-1c35-4ab6-8169-dae1807f847b-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Verdana, Geneva, sans-serif; padding: 15px 50px;">
                      <div >
                          <p style="font-size: 14px; color: #8a8a8a; margin: 0px 0px 16px; text-align: center;">[yaymail_site_name] - Built with <a style="color: #96588a; font-weight: normal; text-decoration: underline;" href="https://woocommerce.com" target="_blank" rel="noopener">WooCommerce</a></p>
                      </div>
                    </td>
                </tr>
              </tbody>
          </table>
        </body>
      </html>
    ';

		/*
		@@@ Elements default when reset template.
		@@@ Note 1: Add characters '\' before special characters in a string.
		@@@ example 1: "family": "\'Helvetica Neue\',Helvetica,Roboto,Arial,sans-serif",

		@@@ Note 2: Add characters '\' before special characters in a string.
		@@@ example 2: "<h1 style=\"font-family: \'Helvetica Neue\',...."
		*/

		// Elements
		$elements =
		'[{
        "id": "8ffa62b5-7258-42cc-ba53-7ae69638c1fe",
        "type": "Logo",
        "nameElement": "Logo",
        "settingRow": {
          "backgroundColor": "#ECECEC",
          "align": "center",
          "pathImg": "",
          "paddingTop": "15",
          "paddingRight": "50",
          "paddingBottom": "15",
          "paddingLeft": "50",
          "width": "172",
          "url": "#"
        }
      }, {
        "id": "802bfe24-7af8-48af-ac5e-6560a81345b3",
        "type": "ElementText",
        "nameElement": "Email Heading",
        "settingRow": {
          "content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0;color: inherit;\">' . $emailTitle . '</h1>",
          "backgroundColor": "#96588A",
          "textColor": "#ffffff",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "36",
          "paddingRight": "48",
          "paddingBottom": "36",
          "paddingLeft": "48"
        }
      }, {
        "id": "b035d1f1-0cfe-41c5-b79c-0478f144ef5f",
        "type": "ElementText",
        "nameElement": "Text",
        "settingRow": {
          "content": "<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailHi . '<br /><br /></span></p>\n<p style=\"margin: 0px;\"><span style=\"font-size: 14px;\">' . $emailtext . '</span></p>",
          "backgroundColor": "#fff",
          "textColor": "#636363",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "47",
          "paddingRight": "50",
          "paddingBottom": "0",
          "paddingLeft": "50"
        }
      },
      {
        "id": "ad422370-f762-4a26-92de-c4cf3878h0oi",
        "type": "OrderItem",
        "nameElement": "Order Item",
        "settingRow": {
          "contentBefore": "[yaymail_items_border_before]",
          "contentAfter": "[yaymail_items_border_after]",
          "contentTitle": "[yaymail_items_border_title]",
          "content": "[yaymail_items_border_content]",
          "backgroundColor": "#fff",
          "titleColor" : "#96588a",
          "textColor": "#636363",
          "borderColor": "#e5e5e5",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "15",
          "paddingRight": "50",
          "paddingBottom": "15",
          "paddingLeft": "50"
        }
      },';
		if ( class_exists( 'WC_Subscription' ) ) {
			$elements .= '{
          "id": "os422370-f762-4a26-92de-c4cf3878h0oi",
          "type": "OrderSubscription",
          "nameElement": "Order Subscription",
          "settingRow": {
            "content" : "[yaymail_items_subscription_information]",
            "backgroundColor" : "#fff",
            "titleColor" : "#96588a",
            "textColor" : "#636363",
            "borderColor" : "#e5e5e5",
            "family" : "Helvetica,Roboto,Arial,sans-serif",
            "paddingTop" : "15",
            "paddingRight" : "50",
            "paddingBottom" : "15",
            "paddingLeft" : "50"
          }
        },';
		}
		$elements .= '{
        "id": "de242956-a617-4213-9107-138842oi4tch",
        "type": "BillingAddress",
        "nameElement": "Billing Shipping Address",
        "settingRow": {
          "nameColumn": "BillingShippingAddress",
          "contentTitle": "[yaymail_billing_shipping_address_title]",
          "checkBillingShipping": "[yaymail_billing_shipping_address_title]",
          "titleBilling": "' . $textBillingAddress . '",
          "titleShipping": "' . $textShippingAddress . '",
          "content": "[yaymail_billing_shipping_address_content]",
          "titleColor" : "#96588a",
          "backgroundColor": "#fff",
          "borderColor": "#e5e5e5",
          "textColor": "#636363",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "15",
          "paddingRight": "50",
          "paddingBottom": "15",
          "paddingLeft": "50"
        }
      },
      {
        "id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8d",
        "type": "ElementText",
        "nameElement": "Footer",
        "settingRow": {
          "content": "<p><span style=\"font-size: 14px;\">' . $additionalContent . '[yaymail_domain]!</span></p>",
          "backgroundColor": "#fff",
          "textColor": "#636363",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "",
          "paddingRight": "50",
          "paddingBottom": "38",
          "paddingLeft": "50"
        }
      },
      {
        "id": "b39bf2e6-8c1a-4384-a5ec-37663da27c8ds",
        "type": "ElementText",
        "nameElement": "Footer",
        "settingRow": {
          "content": "<p style=\"font-size: 14px;margin: 0px 0px 16px; text-align: center;\">[yaymail_site_name]&nbsp;- Built with <a style=\"color: #96588a; font-weight: normal; text-decoration: underline;\" href=\"https://woocommerce.com\" target=\"_blank\" rel=\"noopener\">WooCommerce</a></p>",
          "backgroundColor": "#ececec",
          "textColor": "#8a8a8a",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "15",
          "paddingRight": "50",
          "paddingBottom": "15",
          "paddingLeft": "50"
        }
      }]';

		// Templates New Order
		$templates = array(
			'customer_processing_order' => array(),
		);

		$templates['customer_processing_order']['html']     = $html;
		$templates['customer_processing_order']['elements'] = $elements;
		return $templates;
	}
}
