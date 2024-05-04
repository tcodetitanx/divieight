<?php
/**
 * Checkout service.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\services;

use HRE_Addon\Libs\Common;
use HRE_Addon\Libs\Settings;
use WP_Error;

use function HRE_Addon\hre_get_elite_application_product_user_form;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'silence' ); // exit if accessed directly.
}

/**
 * Class Product Category_Helper
 *
 * @package Beauty_Loaylty_Point\Includes\Helpers
 */
class Checkout_Services {

	/**
	 * Initialize the class.
	 */
	public function init(): void {
		// In thank you page.
		add_action( 'woocommerce_thankyou', array( $this, 'generate_elite_account' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'display_elite_thank_you_view' ) );

		add_filter( 'woocommerce_checkout_get_value', array( $this, 'auto_fill_fields_in_checkout_page' ), 10, 2 );

		// Watch for order status change.
		add_action( 'woocommerce_order_status_changed', array( $this, 'watch_order_status_change' ), 10, 4 );
	}

	/**
	 * @param int $order_id
	 *
	 * @return void
	 */
	public function display_elite_thank_you_view( int $order_id ): void {
		$order = wc_get_order( $order_id );

		if ( ! $order->is_paid() && $order->get_status() !== 'completed' ) {
			return;
		}

		$for = '';

		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();

			$for = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FOR, true );
			if ( ! in_array( $for, array( 'buyer', 'seller' ), true ) ) {
				continue; // Not for buyer or seller.
			}

			$form_details = hre_get_elite_application_product_user_form( $product_id );
			if ( ! $form_details ) {
				continue; // No form details.
			}

			$for = $form_details['for'];
			break;
		}

		if ( in_array( $for, array( 'buyer', 'seller' ), true ) ) {
			do_action( 'hre_display_elite_thankyou_view', $for );
		}
	}

	/**
	 * Watch order status change.
	 *
	 * @param int $order_id The order id.
	 * @param string $old_status The old status.
	 * @param string $new_status The new status.
	 * @param object $order The order.
	 *
	 * @return void
	 */
	public function watch_order_status_change(
		int $order_id,
		string $old_status,
		string $new_status,
		object $order
	): void {
		if ( 'completed' !== $new_status ) {
			return;
		}

		$this->generate_elite_account( $order_id );
	}


	/**
	 * Auto fill fields in checkout page.
	 *
	 * @param string $value The value.
	 * @param string $input The input.
	 *
	 * @return string
	 */
	public function auto_fill_fields_in_checkout_page( $value, $input ): string {
		if ( is_user_logged_in() ) {
			return null === $value ? '' : $value;
		}
		$items = WC()->cart->get_cart();

		$fee_product_id = null;
		foreach ( $items as $item => $values ) {
			$product_id               = $values['data']->get_id();
			$is_buyer_application_fee = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_PRODUCT, true );
			if ( 'yes' !== $is_buyer_application_fee ) {
				continue;
			}
			$fee_product_id = $product_id;
		}

		if ( null !== $fee_product_id ) {
			$form_details = hre_get_elite_application_product_user_form( $product_id );

			// if ( 'billing_first_name' === $input ) {
			// return $form_details['full_name'];
			// }

			if ( 'billing_email' === $input ) {
				return $form_details['email'];
			}

			if ( 'billing_phone' === $input ) {
				return $form_details['phone'];
			}

			if ( 'billing_state' === $input ) {
				return $form_details['state'];
			}
		}

		return '';
	}

	/**
	 * Watch thank you page.
	 *
	 * @param int $order_id The order id.
	 *
	 * @return void
	 */
	public function generate_elite_account( int $order_id ): void {
		$order = wc_get_order( $order_id );

		if ( ! $order->is_paid() && $order->get_status() !== 'completed' ) {
			return;
		}

		$items = $order->get_items();
		foreach ( $items as $item ) {
			$product_id = $item->get_product_id();

			$for = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FOR, true );
			if ( ! in_array( $for, array( 'buyer', 'seller' ) ) ) {
				continue; // Not for buyer or seller.
			}

			$form_details = hre_get_elite_application_product_user_form( $product_id );
			if ( ! $form_details ) {
				continue; // No form details.
			}

			// Check if buyer account is created.
			$old_buyer_id = get_post_meta( $product_id, Settings::PM_IS_ELITE_ACCOUNT_CREATED_USER_ID, true );
			if ( ! empty( $old_buyer_id ) ) {
				Common::in_script_or_send_error(
					array(
						'Elite account is already created.',
					)
				);
				continue;
			}

			// Check if user exists.
			$user_already_created = false;
			$user_by_email        = get_user_by( 'email', $form_details['email'] );
			$user_by_login        = get_user_by( 'login', $form_details['username'] );
			$user                 = $user_by_email instanceof \WP_User ? $user_by_email : $user_by_login;
			if ( $user_by_login instanceof \WP_User ) {
				$user_already_created = true;
			}
			if ( $user_by_email instanceof \WP_User ) {
				$user_already_created = true;
			}

			$buyer_or_seller = 'buyer' === $for ? 'Buyer' : 'Seller';

			if ( ! $user_already_created ) {
				// Create user.
				$user_id = $this->create_buyer_or_seller(
					$product_id,
					$form_details['username'],
					$form_details['email'],
					$form_details['phone'],
					$form_details['state'],
					$form_details['password'],
					$form_details['full_name'],
					$for,
					$form_details['seller_agent_id'],
					$form_details['seller_agent_doesnt_exist'],
					$form_details['seller_agent_first_name'],
					$form_details['seller_agent_last_name'],
					$form_details['seller_agent_phone']
				);

				if ( is_wp_error( $user_id ) ) {
					continue;
				}

				$order->add_order_note(
					sprintf(
					/* translators: %s: The buyer name. */
						__( '%1$s account created for %2$s', 'hre-addon' ),
						$buyer_or_seller,
						esc_attr( $form_details['full_name'] )
					)
				);
			} else {
				$user_id = $user->ID;
				$this->save_buyer_extras( $user_id, $product_id );

				$order->add_order_note(
					sprintf(
					/* translators: %s: The buyer name. */
						__( '%1$s "%2$s" upgraded their plan.', 'hre-addon' ),
						$buyer_or_seller,
						esc_attr( $form_details['full_name'] )
					)
				);
			}

			update_post_meta( $product_id, Settings::PM_IS_ELITE_ACCOUNT_CREATED_USER_ID, $user_id );

			$user = get_user_by( 'ID', $user_id );

			// Log the user in if still in checkout page.
			if ( is_checkout() ) {
				wp_set_current_user( $user_id );
				wp_set_auth_cookie( $user_id );
				do_action( 'wp_login', $user->user_login, $user );
			}
		}
	}

	/**
	 * Create buyer or seller.
	 *
	 * @param int $product_id The product id.
	 * @param string $username The username.
	 * @param string $email The email.
	 * @param string $phone The phone.
	 * @param string $state The state.
	 * @param string $password The password.
	 * @param string $full_name The full name.
	 * @param string $for The for. Can be 'buyer' or 'seller'.
	 * @param int $seller_agent_id The seller agent id.
	 * @param string $seller_agent_doesnt_exist The seller agent doesnt exist.
	 * @param string $seller_agent_first_name The seller agent first name.
	 * @param string $seller_agent_last_name The seller agent last name.
	 * @param string $seller_agent_phone The seller agent phone.
	 *
	 * @return int|WP_Error
	 */
	public function create_buyer_or_seller(
		int $product_id,
		string $username,
		string $email,
		string $phone,
		string $state,
		string $password,
		string $full_name,
		string $for,
		int $seller_agent_id = 0,
		string $seller_agent_doesnt_exist = 'no',
		string $seller_agent_first_name = '',
		string $seller_agent_last_name = '',
		string $seller_agent_phone = ''
	) {
		$user_role = 'seller' === $for ? 'houzez_seller' : 'houzez_buyer';
		$user_id   = wp_insert_user(
			array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => $user_role,
			)
		);
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Add user meta.
		update_user_meta( $user_id, 'first_name', $full_name );
		update_user_meta( $user_id, Settings::UM_BUYER_PHONE, $phone );
		update_user_meta( $user_id, Settings::UM_BUYER_STATE, $state );
		update_user_meta( $user_id, Settings::UM_SELLER_AGENT_ID, $seller_agent_id );
		update_user_meta( $user_id, Settings::UM_SELLER_AGENT_DOESNT_EXIST, $seller_agent_doesnt_exist );
		update_user_meta( $user_id, Settings::UM_SELLER_AGENT_FIRST_NAME, $seller_agent_first_name );
		update_user_meta( $user_id, Settings::UM_SELLER_AGENT_LAST_NAME, $seller_agent_last_name );
		update_post_meta( $user_id, Settings::UM_SELLER_AGENT_PHONE, $seller_agent_phone );

		$this->save_buyer_extras( $user_id, $product_id );

		return $user_id;
	}

//$form_details['seller_agent_id'],
//$form_details['seller_agent_doesnt_exist'],
//$form_details['seller_agent_first_name'],
//$form_details['seller_agent_last_name'],
//$form_details['seller_agent_phone']
	/**
	 * Save buyer extras.
	 *
	 * @param int $user_id The user id.
	 * @param int $product_id The product id.
	 *
	 * @return void
	 */
	private function save_buyer_extras( int $user_id, int $product_id ): void {
		// Extra.
		update_user_meta( $user_id, Settings::UM_BUYER_APPLICATION_FEE_PRODUCT_ID, $product_id );
		update_user_meta( $user_id, Settings::UM_BUYER_APPLICATION_PAID, 'yes' );
		update_user_meta( $user_id, Settings::UM_ELITE_MEMBERSHIP_PAID_DATE, Common::get_date_time() );
	}

}
