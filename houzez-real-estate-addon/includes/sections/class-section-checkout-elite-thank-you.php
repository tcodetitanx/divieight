<?php
/**
 * Section Checkout Elite Thank You.
 *
 * @package HRE_Addon
 */


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
 * Class Section_Checkout_Elite_Thank_You
 */
class Section_Checkout_Elite_Thank_You {

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
		add_action( 'hre_display_elite_thankyou_view', array( $this, 'load_view' ) );

		return $this;
	}

	/**
	 * Load the view.
	 *
	 * @param string $for The user type. 'buyer' or 'seller'.
	 *
	 * @return void
	 */
	public function load_view( string $for ): void {

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_section_checkout_elite_thankyou' );

		echo sprintf(
			'<div class="hre-section-checkout-elite-thank-you" data-for="%2$s" >%1$s</div>',
			esc_attr__(
				'Loading...',
				'hre-addon'
			),
			esc_attr( $for )
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
		$css = mp_get_style( '/public/section-checkout-elite-thankyou' );
		$js  = mp_get_script( '/public/section-checkout-elite-thankyou' );

		wp_register_style( 'hre-section-checkout-elite-thankyou', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-section-checkout-elite-thankyou',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_section_checkout_elite_thankyou',
			function () {

				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-section-checkout-elite-thankyou' );
				wp_enqueue_script( 'hre-section-checkout-elite-thankyou' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
