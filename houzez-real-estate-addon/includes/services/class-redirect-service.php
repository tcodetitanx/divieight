<?php
/**
 * Redirect service.
 * In charge of redirecting users to another page if they shouldn't be there.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\Rest_Api;

use Exception;
use HRE_Addon\Libs\Settings;
use WP_Error;

use function HRE_Addon\hre_buyer_has_paid;
use function HRE_Addon\hre_get_admin_settings;
use function HRE_Addon\hre_get_elite_application_product_user_form;
use function HRE_Addon\hre_get_client_settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'silence' ); // exit if accessed directly.
}

/**
 * Class Redirect_Service
 *
 * @package Beauty_Loaylty_Point\Includes\Helpers
 */
class Redirect_Service {

	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'template_redirect', array( $this, 'redirect_to_buyer_onboarding_process_2' ) );
		add_action( 'template_redirect', array( $this, 'redirect_to_buyer_elite_signup_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_to_seller_elite_signup_page' ) );
		add_action( 'template_redirect', array( $this, 'redirect_to_agent_login_page' ) );
	}

	/**
	 * Redirect to agent login if in create listing page and user is not an agent.
	 *
	 * @return void
	 */
	public function redirect_to_agent_login_page(): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}

		$admin_settings         = hre_get_admin_settings();
		$create_listing_page_id = $admin_settings['create_listing_page_id'];

		if ( ! is_page( $create_listing_page_id ) ) {
			return;
		}

		if ( is_user_logged_in() ) {
			$user_is_agent = Settings::USER_ROLE_AGENT === get_user_by( 'id', get_current_user_id() )->roles[0];

			if ( $user_is_agent ) {
				return;
			}
		}

		$client_settings      = hre_get_client_settings();
		$agent_login_page_url = $client_settings['agent_login_page_url'];

		echo '<script>window.location.href = "' . $agent_login_page_url . '?redirect=' . get_permalink() . '";</script>';
		exit();
	}

	/**
	 * Redirect the user to buyer login page if they try to view buyer preference page.
	 *
	 * @return void
	 */
	public function redirect_to_buyer_login_page_if_from_buyer_preference_page(): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		$client_settings = hre_get_client_settings();

		$buyer_elite_login_page_url = $client_settings['buyer_elite_login_page_url'];

		if ( is_wp_error( $this->is_full_buyer() ) ) {
			echo '<script>window.location.href = "' . $buyer_elite_login_page_url . '?redirect=' . get_permalink() . '";</script>';
			exit();
		}
	}

	/**
	 * Redirect the user to the buyer onboarding process 2 page if
	 * - They visit the search by map page
	 * - AND They are not logged in
	 * - OR they are logged but not a buyer
	 * - OR they are logged in as a buyer but they have not paid for the elite membership
	 * - If they are admin, they are not redirected.
	 *
	 * @return void
	 * @throws Exception If the user is not logged in.
	 */
	public function redirect_to_buyer_onboarding_process_2(): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		$admin_settings        = hre_get_admin_settings();
		$client_settings       = hre_get_client_settings();
		$search_by_map_page_id = $admin_settings['search_by_map_page_id'];
		global $post;

		$is_search_by_map_page = is_page( $search_by_map_page_id );
		// Dont run for admin.
		if ( ! $is_search_by_map_page || is_admin() ) {
			return;
		}

		if ( is_wp_error( $this->is_full_buyer() ) ) {
			echo '<script>window.location.href = "' . $client_settings['buyer_onboarding_process_2_page_url'] . '?redirect=' . get_permalink() . '";</script>';
			exit();
		}
	}

	/**
	 * Redirect the user to elite buyer login if they try to view buyer elite page.
	 *
	 * @return void
	 */
	public function redirect_to_buyer_elite_signup_page(): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		$admin_settings  = hre_get_admin_settings();
		$client_settings = hre_get_client_settings();

		$buyer_elite_page_id = $admin_settings['buyer_elite_page_id'];
		global $post;

		if ( ! is_page( $buyer_elite_page_id ) || is_admin() ) {
			return;
		}

		$buyer_elite_login_page_url = $client_settings['buyer_elite_login_page_url'];

		if ( is_wp_error( $this->is_full_buyer() ) ) {
			echo '<script>window.location.href = "' . $buyer_elite_login_page_url . '?redirect=' . get_permalink() . '";</script>';
			exit();
		}
	}

	/**
	 * Redirect the user to elite seller login if they try to view seller elite page.
	 *
	 * @return void
	 */
	public function redirect_to_seller_elite_signup_page(): void {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		$admin_settings  = hre_get_admin_settings();
		$client_settings = hre_get_client_settings();

		$seller_elite_page_id = $admin_settings['seller_elite_page_id'];

		if ( ! is_page( $seller_elite_page_id ) || is_admin() ) {
			return;
		}

		$seller_elite_login_page_url = $client_settings['seller_elite_login_page_url'];

		if ( is_wp_error( $this->is_full_seller() ) ) {
			echo '<script>window.location.href = "' . $seller_elite_login_page_url . '?redirect=' . get_permalink() . '";</script>';
			exit();
		}
	}

	/**
	 * If the is a full user.
	 *
	 * @return true|WP_Error True if the user is a full buyer, WP_Error otherwise.
	 */
	private function is_full_buyer() {
		// Buyer must be logged in.
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'not_logged_in',
				'User is not logged in.',
				array( 'status' => 401 )
			);
		}

		// Buyer must have the buyer role.
		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );
		if ( ! in_array( Settings::USER_ROLE_BUYER, $user->roles, true ) ) {
			return new WP_Error(
				'not_buyer',
				'User is not a buyer.',
				array( 'status' => 401 )
			);
		}

		// Buyer must have paid for the elite membership.
		if ( ! hre_buyer_has_paid( $user_id ) ) {
			return new WP_Error(
				'not_paid',
				'User has not paid for the elite membership.',
				array( 'status' => 401 )
			);
		}

		return true;
	}

	/**
	 * If the is a full seller.
	 *
	 * @return true|WP_Error True if the user is a full seller, WP_Error otherwise.
	 */
	private function is_full_seller() {
		// Seller must be logged in.
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'not_logged_in',
				'User is not logged in.',
				array( 'status' => 401 )
			);
		}

		// Seller must have the seller role.
		$user_id = get_current_user_id();
		$user    = get_userdata( $user_id );
		if ( ! in_array( Settings::USER_ROLE_SELLER, $user->roles, true ) ) {
			return new WP_Error(
				'not_seller',
				'User is not a seller.',
				array( 'status' => 401 )
			);
		}

		// Seller must have paid for the elite membership.
		if ( ! hre_buyer_has_paid( $user_id ) ) {
			return new WP_Error(
				'not_paid',
				__( 'User has not paid for the elite membership.', 'hre-addon' ),
				array( 'status' => 401 )
			);
		}

		return true;
	}


}
