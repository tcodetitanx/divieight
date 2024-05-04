<?php
/**
 * Shortcode Buyer Signup .
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Shortcodes;

use HRE_Addon\Initializer;
use HRE_Addon\Libs\Common;
use function HRE_Addon\hre_get_client_settings;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;
use function HRE_Addon\mp_get_template_path;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}

/**
 * Class  Shortcode_Buyer_Signup
 */
class Shortcode_Buyer_Signup {

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
	public function init(): Shortcode_Buyer_Signup {
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
			add_shortcode( 'hre_buyer_elite_signup', array( $this, 'load_view' ) );
		}
	}

	/**
	 * Load the view.
	 *
	 * @return string
	 */
	public function load_view(): string {

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_shortcode_buyer_signup' );

		return sprintf(
			'<div class="hre-shortcode-signup" >%1$s</div>',
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
		$css = mp_get_style( '/public/sc-buyer-signup' );
		$js  = mp_get_script( '/public/sc-buyer-signup' );

		wp_register_style( 'hre-shortcode-buyer-signup', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-buyer-signup',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_buyer_signup',
			function () {

				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-buyer-signup' );
				wp_enqueue_script( 'hre-shortcode-buyer-signup' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
