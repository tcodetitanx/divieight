<?php
/**
 * Category Helper.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\services;

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
class Cart_Services {

	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'wp_loaded', array( $this, 'watch_redirect_to_checkout_page' ), 99 );
	}

	/**
	 * Watch redirect to checkout page.
	 *
	 * @return void
	 */
	public function watch_redirect_to_checkout_page(): void {
		// '?buyer_action=add_to_cart&product_id=' +
		$buyer_action = filter_input( INPUT_GET, 'buyer_action', FILTER_SANITIZE_STRING );
		if ( 'add_to_cart' === $buyer_action ) {
			$product_id = filter_input( INPUT_GET, 'product_id', FILTER_SANITIZE_STRING );
			try {
				Wc()->cart->add_to_cart( $product_id, 1 );

				echo sprintf( '<style >body { display: none !important; }</style>' );
				echo sprintf( "<script>window.location.href = '%s';</script>", esc_url( wc_get_checkout_url() ) );
			} catch ( \Exception $e ) {
				echo sprintf( "<script>window.location.href = '%s';</script>", esc_url( home_url() ) );
			}
		}
	}
}
