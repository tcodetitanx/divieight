<?php
/**
 * Shortcode Inquiry.
 *
 * @package HRE_Addon\Includes\Shortcodes
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
 * Class Shortcode_Inquiry
 */
class Shortcode_Inquiry {

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
			add_shortcode( 'hre_inquiry', array( $this, 'load_view' ) );
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
		do_action( 'hre_enqueue_shortcode_inquiry' );

		return sprintf(
			'<div class="hre-shortcode-inquiry" >%1$s</div>',
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
		$css = mp_get_style( '/public/sc-inquiry' );
		$js  = mp_get_script( '/public/sc-inquiry' );

		wp_register_style( 'hre-shortcode-inquiry', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-inquiry',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_inquiry',
			function () {
				wp_enqueue_style( 'hre-shortcode-inquiry' );
				wp_enqueue_script( 'hre-shortcode-inquiry' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
