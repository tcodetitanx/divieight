<?php
/**
 * Shortcode Agent Login .
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
 * Class  Shortcode_Agent_Login
 */
class Shortcode_Agent_Login {

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
			add_shortcode( 'hre_agent_login', array( $this, 'load_view' ) );
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
		do_action( 'hre_enqueue_shortcode_agent_login' );

		return sprintf(
			'<div class="hre-shortcode-agent-login" >%1$s</div>',
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
		$css = mp_get_style( '/public/sc-agent-login' );
		$js  = mp_get_script( '/public/sc-agent-login' );

		wp_register_style( 'hre-shortcode-agent-login', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-agent-login',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_agent_login',
			function () {
				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-agent-login' );
				wp_enqueue_script( 'hre-shortcode-agent-login' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
