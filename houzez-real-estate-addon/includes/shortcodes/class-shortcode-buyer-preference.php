<?php
/**
 * Shortcode for property agreement
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Shortcodes;

use HRE_Addon\Includes\Rest_Api\Redirect_Service;
use HRE_Addon\Includes\Rest_Api\User_Service;
use HRE_Addon\Initializer;
use HRE_Addon\Libs\Common;
use HRE_Addon\Libs\Settings;

use function HRE_Addon\hre_get_client_settings;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;
use function HRE_Addon\mp_get_template_path;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}

/**
 * Class Shortcode_Buyer_Preference
 */
class Shortcode_Buyer_Preference {

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
			add_shortcode( 'hre_buyer_preference', array( $this, 'load_view' ) );
		}
	}

	/**
	 * Load the view.
	 *
	 * @return string
	 */
	public function load_view(): string {

		// Redirect to buyer login page if the user is not logged in or something else.
		( new Redirect_Service() )->redirect_to_buyer_login_page_if_from_buyer_preference_page();

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_shortcode_buyer_preference' );

		return sprintf(
			'<div class="hre-shortcode-buyer-preference" >%1$s</div>',
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

		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			Initializer::add_to_client_data( 'buyer_preference', User_Service::get_buyer_preference( $user_id ) );
		}
	}

	/**
	 * Register Scripts and styles.
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		$css = mp_get_style( '/public/sc-buyer-preference' );
		$js  = mp_get_script( '/public/sc-buyer-preference' );

		wp_register_style( 'hre-shortcode-buyer-preference', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-buyer-preference',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_buyer_preference',
			function () {
				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-buyer-preference' );
				wp_enqueue_script( 'hre-shortcode-buyer-preference' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
