<?php
/**
 * Shortcode Buyer onboarding process 2.
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Shortcodes;

use HRE_Addon\Initializer;
use HRE_Addon\Libs\Settings;
use function HRE_Addon\hre_buyer_has_paid;
use function HRE_Addon\hre_get_client_settings;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}

/**
 * Class Shortcode_Buyer_Onboarding_2
 * Shortcode for buyer onboarding process 2.
 */
class Shortcode_Buyer_Onboarding_2 {

	/**
	 * Constructor
	 */
	public function __construct() {
	}

	/**
	 * Initialize this shortcode.
	 *
	 * @return $this
	 */
	public function init(): self {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 99 );
		add_action( 'init', array( $this, 'add_shortcode' ) );

		return $this;
	}

	/**
	 * Add the shortcode.
	 *
	 * @return void
	 */
	public function add_shortcode(): void {
		if ( ( ! wp_doing_ajax() ) && ( ! is_admin() ) ) {
			add_shortcode( 'buyer_onboarding_2', array( $this, 'load_view' ) );
		}
	}

	/**
	 * Load the view.
	 *
	 * @return string
	 */
	public function load_view(): string {
//		$client_settings           = hre_get_client_settings();
//		$buyer_signup_page_url     = $client_settings['search_by_map_page_url'];
//		$allow_go_to_search_by_map = true;
//
//		// Buyer must be logged in and has paid elite membership.
//		if ( ! is_user_logged_in() ) {
//			$allow_go_to_search_by_map = false;
//		}
//
//		$user_id = get_current_user_id();
//		$user    = get_userdata( $user_id );
//		if ( $allow_go_to_search_by_map ) {
//			// Buyer must have the buyer role.
//			if ( ! in_array( Settings::USER_ROLE_BUYER, $user->roles, true ) ) {
//				$allow_go_to_search_by_map = false;
//			}
//		}
//
//		if ( $allow_go_to_search_by_map && ! hre_buyer_has_paid( $user_id ) ) {
//			$allow_go_to_search_by_map = false;
//		}
//
//		if ( $allow_go_to_search_by_map ) {
//			echo '<script>window.location.href = "' . $buyer_signup_page_url . '";</script>';
//
//			return '';
//		}

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_shortcode_buyer_onboarding_2' );

		return sprintf(
			'<div class="hre-shortcode-buyer-onboarding-2" >%1$s</div>',
			esc_attr__(
				'Loading...',
				'hre-addon'
			)
		);
	}

	/**
	 * Loads client data.
	 *
	 * @return void
	 */
	public function load_client_data(): void {
		Initializer::add_to_client_data( 'client_settings', hre_get_client_settings() );
	}

	/**
	 * Register Scripts and styles.
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		$css = mp_get_style( '/public/sc-buyer-onboarding-2' );
		$js  = mp_get_script( '/public/sc-buyer-onboarding-2' );

		wp_register_style( 'hre-shortcode-buyer-onboarding-2', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-buyer-onboarding-2',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_buyer_onboarding_2',
			function () {
				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-buyer-onboarding-2' );
				wp_enqueue_script( 'hre-shortcode-buyer-onboarding-2' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
