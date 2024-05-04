<?php
/**
 * Shortcode Buyer Dashboard .
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Shortcodes;

use HRE_Addon\Initializer;
use function HRE_Addon\hre_get_client_settings;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}

/**
 * Class  Shortcode_Login
 */
class Shortcode_Buyer_Dashboard {

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
	public function init(): Shortcode_Buyer_Dashboard {
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
			add_shortcode( 'buyer_dashboard_view', array( $this, 'load_view' ) );
		}
	}

	/**
	 * Load the view.
	 *
	 * @return string
	 */
	public function load_view(): string {

		if ( ! is_user_logged_in() ) {
			$client_settings = hre_get_client_settings();
			$login_url       = $client_settings['login_url'];

			return sprintf(
				'<div class="hre-shortcode-buyer-dashboard border border-solid border-gray-100 bg-gray-50 p-4" ><a href="%1$s">%2$s</a></div>',
				esc_url( $login_url ),
				esc_attr__(
					'Please login as a buyer to view your property applications.',
					'hre-addon'
				)
			);
		}

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_shortcode_buyer_dashboard' );

		return sprintf(
			'<div class="hre-shortcode-buyer-dashboard" >%1$s</div>',
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
		$css = mp_get_style( '/public/sc-buyer-dashboard' );
		$js  = mp_get_script( '/public/sc-buyer-dashboard' );

		wp_register_style( 'hre-shortcode-buyer-dashboard', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-buyer-dashboard',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_buyer_dashboard',
			function () {
				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-buyer-dashboard' );
				wp_enqueue_script( 'hre-shortcode-buyer-dashboard' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
