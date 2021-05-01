<?php
namespace YayMail\Page\Source;

defined( 'ABSPATH' ) || exit;
/**
 * Plugin activate/deactivate logic
 */
class CustomPostType {

	public static function checkEmailTemplateExist( $args = false ) {
		if ( false != $args ) {
			$default = array(
				'post_type'   => 'yaymail_template',
				'post_status' => array( 'publish', 'pending', 'future' ),
				'meta_query'  => array(
					'relation' => 'OR',
					array(
						'key'     => '_yaymail_template',
						'value'   => $args['email_template'],
						'compare' => '=',
					),
				),
			);
			$posts   = new \WP_Query( $default );
			if ( $posts->have_posts() ) {
				return $posts->post->ID;
			}
		}
		return false;
	}
	public static function getListPostTemplate() {

		$posts = get_posts(
			array(
				'post_type'   => 'yaymail_template',
				'post_status' => array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit' ),
				'numberposts' => -1,
			)
		);
		return $posts;
	}
	// delete all template in database.
	// public static function deleteAllTemplate()
	// {
	// $templates = self::getListPostTemplate();
	// foreach ($templates as $key => $template) {
	// wp_delete_post($template->ID);
	// }
	// }
	public static function templateEnableDisable( $getPostID = true ) {
		$template_export = array();
		$posts           = self::getListPostTemplate();
		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $key => $post ) {
				$template = get_post_meta( $post->ID, '_yaymail_template', true );
				if ( $getPostID ) {
					$template_export[ $template ]['post_id']         = $post->ID;
					$template_export[ $template ]['_yaymail_status'] = get_post_meta( $post->ID, '_yaymail_status', true );
				} else {
					$template_export[ $template ] = get_post_meta( $post->ID, '_yaymail_status', true );
				}
			}
		}
		return $template_export;
	}
	public static function insert( $args = false ) {
		if ( false != $args && is_array( $args ) ) {
			$arr       = array(
				'post_content'  => $args['mess'],
				'post_date'     => $args['post_date'],
				'post_date_gmt' => $args['post_date'],
				'post_type'     => $args['post_type'],
				'post_title'    => wp_trim_words( $args['mess'], 200 ),
				'post_status'   => 'publish',
			);
			$insert_id = wp_insert_post( $arr );
			if ( 'yaymail_template' == $args['post_type'] ) {
				update_post_meta( $insert_id, '_yaymail_template', $args['_yaymail_template'] );
				// update_post_meta( $insert_id, '_yaymail_html', $args['_yaymail_html'] );
				update_post_meta( $insert_id, '_yaymail_elements', $args['_yaymail_elements'] );
				update_post_meta( $insert_id, '_yaymail_email_backgroundColor_settings', $args['_email_backgroundColor_settings'] );
				update_post_meta( $insert_id, '_yaymail_status', 0 );
			}
			return $insert_id;
		}
		return false;
	}

	public static function postIDByTemplate( $template ) {
		$agrs_template = array( 'email_template' => $template );
		if ( self::checkEmailTemplateExist( $agrs_template ) ) {
			$postID = self::checkEmailTemplateExist( $agrs_template );
			return $postID;
		}
		return false;
	}

	public static function getTemplateExport() {
		$template_export                    = array();
		$template_export['yaymail_version'] = get_option( 'yaymail_version' );
		$posts                              = self::getListPostTemplate();
		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $key => $post ) {
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_template'] = get_post_meta( $post->ID, '_yaymail_template', true );
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_html']     = get_post_meta( $post->ID, '_yaymail_html', true );
				$template_export['yaymailTemplateExport'][ $key ]['_yaymail_elements'] = get_post_meta( $post->ID, '_yaymail_elements', true );
			}
		}
		return $template_export;
	}
	public static function getListOrders() {
		$data_orders = array();
		if ( defined( 'WOOCOMMERCE_VERSION' ) ) {
			$list_orders = new \WP_Query(
				array(
					'post_type'      => 'shop_order',
					'post_status'    => array_keys( wc_get_order_statuses() ),
					'posts_per_page' => 50,
				)
			);
			if ( isset( $list_orders->posts ) && ! empty( ( $list_orders->posts ) ) ) {
				foreach ( $list_orders->posts as $order_item ) {
					if ( $order_item->ID ) {
						$order         = new \WC_Order( $order_item->ID );
						$data_orders[] = array(
							'id'         => $order_item->ID,
							'id_real'    => $order->get_order_number(),
							'email'      => $order->get_billing_email(),
							'first_name' => $order->get_billing_first_name(),
							'last_name'  => $order->get_billing_last_name(),
						);
					}
				}
			}
		}
		return $data_orders;

	}
}
