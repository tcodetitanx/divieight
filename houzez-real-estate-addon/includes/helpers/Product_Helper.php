<?php
/**
 * Category Helper.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\helpers;

use HRE_Addon\Libs\Settings;
use WP_Error;

use function HRE_Addon\hre_save_buyer_application_product_user_form;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'silence' ); // exit if accessed directly.
}

/**
 * Class Product Category_Helper
 *
 * @package Beauty_Loaylty_Point\Includes\Helpers
 */
class Product_Helper {

	/**
	 * Create a product and add it to the cart.
	 *
	 * @param array $product_details The product details.
	 * @param array $form_details The form details.
	 *
	 * @return int|WP_Error
	 */
	public function create_product( array $product_details, array $form_details ) {
		$product_details = wp_parse_args(
			$product_details,
			array(
				'title'    => '',
				'price'    => 0,
				'quantity' => 1,
			)
		);

		$form_details = wp_parse_args(
			$form_details,
			array(
				'username'                  => '',
				'email'                     => '',
				'phone'                     => '',
				'state'                     => '',
				'password'                  => '',
				'full_name'                 => '',
				'for'                       => 'buyer',
				// seller agent.
				'seller_agent_id'           => 0,
				'seller_agent_doesnt_exist' => 'no',
				'seller_agent_first_name'   => '',
				'seller_agent_last_name'    => '',
				'seller_agent_phone'        => '',
				'seller_agent_state'        => '',
			)
		);

		$item        = array(
			'Name'        => $product_details['title'],
			'Description' => $product_details['title'],
			'SKU'         => substr( str_shuffle( str_repeat( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ', 10 ) ), 0, 10 ),
		);
		$user_id     = get_current_user();
		$insert_args = array(
			'post_author'   => $user_id,
			'post_title'    => $product_details['title'],
			'post_content'  => $product_details['title'],
			'post_status'   => 'publish',
			'post_type'     => 'product',
			'post_category' => 0,
		);

		$post_id = wp_insert_post( $insert_args );

		if ( is_wp_error( $post_id ) ) {
			return $post_id;
		}

		$regular_price = $product_details['price'] + 10;
		wp_set_object_terms( $post_id, 'simple', 'product_type' );
		update_post_meta( $post_id, '_visibility', 'visible' );
		update_post_meta( $post_id, '_stock_status', 'instock' );
		update_post_meta( $post_id, 'total_sales', '0' );
		update_post_meta( $post_id, '_downloadable', 'no' );
		update_post_meta( $post_id, '_virtual', 'no' );
		update_post_meta( $post_id, '_regular_price', (string) $regular_price );
		update_post_meta( $post_id, '_sale_price', '' . $product_details['price'] );
		update_post_meta( $post_id, '_purchase_note', '' );
		update_post_meta( $post_id, '_featured', 'no' );
		update_post_meta( $post_id, '_weight', 0 );
		update_post_meta( $post_id, '_length', 0 );
		update_post_meta( $post_id, '_width', 0 );
		update_post_meta( $post_id, '_height', 0 );
		update_post_meta( $post_id, '_sku', $item['SKU'] );
		update_post_meta( $post_id, '_product_attributes', array() );
		update_post_meta( $post_id, '_sale_price_dates_from', '' );
		update_post_meta( $post_id, '_sale_price_dates_to', '' );
		update_post_meta( $post_id, '_price', '' . $product_details['price'] );
		update_post_meta( $post_id, '_sold_individually', '' );
		update_post_meta( $post_id, '_manage_stock', 'yes' );
		update_post_meta( $post_id, '_backorders', 'yes' );
		update_post_meta( $post_id, '_stock', '' );

		$new_id = $post_id;

		hre_save_buyer_application_product_user_form(
			$new_id,
			$form_details['username'],
			$form_details['email'],
			$form_details['phone'],
			$form_details['state'],
			$form_details['password'],
			$form_details['full_name'],
			$form_details['for'],
			// seller agent.
			$form_details['seller_agent_id'],
			$form_details['seller_agent_doesnt_exist'],
			$form_details['seller_agent_first_name'],
			$form_details['seller_agent_last_name'],
			$form_details['seller_agent_phone'],
			$form_details['seller_agent_state']
		);

		// set_post_thumbnail( $new_id, $product_details['image_id'] );

		// try {
		// Wc()->cart->add_to_cart( $new_id, $product_details['quantity'] );
		// } catch ( \Exception $e ) {
		// Do nothing.
		// return false;
		// }

		return $new_id;
	}

}
