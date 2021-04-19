<?php
namespace YayMail\Templates\DefaultTemplate;

defined( 'ABSPATH' ) || exit;

class CustomerResetPassword {

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
		$emailTitle        = __( 'Password Reset Request', 'woocommerce' );
		$emailHi           = esc_html__( 'Hi ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_customer_username]' ) . ',' );
		$emailtext         = esc_html__( 'Someone has requested a new password for the following account on ', 'woocommerce' ) . esc_html( do_shortcode( '[yaymail_site_name]' ) . ':' );
		$emailtext_1       = esc_html__( 'If you didn\'t make this request, just ignore this email. If you\'d like to proceed:', 'woocommerce' );
		$textUserName      = __( 'Username', 'woocommerce' );
		$additionalContent = __( 'Thanks for reading.', 'woocommerce' );

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
                    <td  id="web802bfe24-7af8-48af-ac5e-6560a81345b3-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family:  Helvetica, Roboto, Arial, sans-serif; padding: 36px 48px;">
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
                    <td  id="webb035d1f1-0cfe-41c5-b79c-0478f144ef5f-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family:  Helvetica, Roboto, Arial, sans-serif; padding: 32px 48px;">
                      <div >
                          <p style="margin: 0 0 16px;"><span style="color: #636363; font-size: 14px;">' . $emailHi . '</span></p>
                          <p style="margin: 0 0 16px;"><span style="color: #636363; font-size: 14px;">' . $emailtext . '</span></p>
                          <p style="margin: 0 0 16px;"><span style="color: #636363; font-size: 14px;">' . $textUserName . ': [yaymail_customer_username]</span></p>
                          <p style="margin: 0 0 16px;"><span style="color: #636363; font-size: 14px;">' . $emailtext_1 . '</span></p>
                          <p style="margin: 0 0 16px;">
                          [yaymail_password_reset_url]
                          </p>
                          <p style="margin: 0 0 16px;"><span style="color: #636363; font-size: 14px;">' . $additionalContent . '</span></p>
                      </div>
                    </td>
                </tr>
              </tbody>
          </table>
          <table   width="605px" cellspacing="0" cellpadding="0" border="0" align="center" id="web964bb3b1-2e11-4eb1-a2b0-440c8da21257" class="web-main-row" style="display: table; background-color: rgb(236, 236, 236); width: 605px;">
              <tbody >
                <tr >
                    <td  id="web964bb3b1-2e11-4eb1-a2b0-440c8da21257-shipping-address" align="left" class="web-shipping-address" style="font-size: 13px;  line-height: 22px; word-break: break-word; font-family: Verdana, Geneva, sans-serif; padding: 15px 50px;">
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
		'[
        {
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
          "content": "<h1 style=\"font-size: 30px; font-weight: 300; line-height: normal; margin: 0; color: inherit;\">' . $emailTitle . '</h1>",
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
          "content": "<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">' . $emailHi . '</span></p>\n<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">' . $emailtext . '</span></p>\n<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">' . $textUserName . ': [yaymail_customer_username]</span></p>\n<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">' . $emailtext_1 . '</span></p>\n<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">[yaymail_password_reset_url]</span></p>\n<p style=\"margin: 0 0 16px;\"><span style=\"font-size: 14px;\">' . $additionalContent . '</span></p>",
          "backgroundColor": "#fff",
          "textColor": "#636363",
          "family": "Helvetica,Roboto,Arial,sans-serif",
          "paddingTop": "32",
          "paddingRight": "48",
          "paddingBottom": "32",
          "paddingLeft": "48"
        }
      }, {
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
			'customer_reset_password' => array(),
		);

		$templates['customer_reset_password']['html']     = $html;
		$templates['customer_reset_password']['elements'] = $elements;
		return $templates;
	}
}
