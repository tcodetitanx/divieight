<?php
/**
 * Shortcode for property agreement
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Shortcodes;

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
 * Class  Shortcode_Property_Agreement
 */
class Shortcode_Property_Agreement {


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
	public function init(): Shortcode_Property_Agreement {
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
			add_shortcode( 'property_agreement_view', array( $this, 'load_view' ) );
		}
	}

	/**
	 * Load the view.
	 *
	 * @return string
	 */
	public function load_view(): string {
		$property_id = filter_input( INPUT_GET, 'property_id', FILTER_SANITIZE_NUMBER_INT );
		$post        = get_post( $property_id );

		if ( 'property' !== $post->post_type ) {
			return sprintf(
				'<div class="%1$s" data-property-id="%2$s" >' . esc_attr__(
					'Invalid property',
					'hre-addon'
				) . '</div>',
				'invalid-property bg-gray-100 border border-solid border-gray-100 rounded-md p-4',
			);
		}

		$sign_mode = get_post_meta( $property_id, Settings::PM_PROPERTY_SIGN_MODE, true );
		if ( empty( $sign_mode ) ) {
			$sign_mode = 'simple';
		}
		$agreement  = get_post_meta( $property_id, Settings::PM_PROPERTY_AGREEMENT, true );
		$agreement2 = get_post_meta( $property_id, Settings::PM_PROPERTY_AGREEMENT_2, true );
		if ( empty( $agreement ) ) {
			$agreement = get_option( Settings::OPTION_DEFAULT_AGREEMENT_1, '' );
		}
		if ( empty( $agreement2 ) ) {
			$agreement2 = get_option( Settings::OPTION_DEFAULT_AGREEMENT_2, '' );
		}

		/**
		 * Enqueue shortcode scripts.
		 *
		 * @since 1.0.0
		 */
		do_action( 'hre_enqueue_shortcode_property_agreement' );

		$agreement_section = sprintf(
			'
					<div class="hre-sc-property-agreement" data-sign-mode="%4$s"> </div>
					<div class="hre-shortcode-property-agreement1 %3$s" style="display:none" >%1$s</div>
				   	<br />
				  	<div class="hre-shortcode-property-agreement2 %3$s"  style="display:none" >%2$s</div>
				  	<div class="hre-loading-agreement">Loading...</div>
					 ',
			htmlspecialchars_decode( $agreement ),
			htmlspecialchars_decode( $agreement2 ),
			esc_attr( '' ),
			esc_attr( $sign_mode )
		);
		$html_agreement_1  = sprintf(
			' <div class="hre-agreement-section"> <div class="ql-editor">
                    <div class="hre-shortcode-property-agreement1 %4$s">%2$s</div>
				  	<div class="hre-shortcode-property-agreement2 %4$s">%3$s</div>
				  	<div class="hre-sc-property-agreement" data-sign-mode="%5$s" >%1$s</div>
				  	</div></div>
                ',
			esc_attr__(
				'Loading...',
				'hre-addon'
			),
			htmlspecialchars_decode( $agreement ),
			htmlspecialchars_decode( $agreement2 ),
			esc_attr( 'border border-solid border-gray-100 p-2 mb-2' ),
			esc_attr( $sign_mode )
		);

		// Common::in_script_or_send_error(
		// array(
		// 'property_id' => $property_id,
		// 'post'        => $post,
		// 'sign_mode'   => $sign_mode,
		// ),
		// );

		return sprintf(
			'<div class="hre-shortcode-property-agreement-container" 
						data-property-id="%4$s"
						data-sign-mode="%3$s" >%1$s%2$s</div>',
			'complex' === $sign_mode ? $agreement_section : '',
			'simple' === $sign_mode ? $html_agreement_1 : '',
			'mode::' . $sign_mode,
			$property_id
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
		$css = mp_get_style( '/public/sc-property-agreement' );
		$js  = mp_get_script( '/public/sc-property-agreement' );

		wp_register_style( 'hre-shortcode-property-agreement', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-shortcode-property-agreement',
			$js,
			array( 'jquery', 'wp-element', 'wp-components' ),
			Initializer::$script_version,
			true
		);

		// enqueue the scripts.
		add_action(
			'hre_enqueue_shortcode_property_agreement',
			function () {
				/**
				 * Enqueue default public scripts.
				 *
				 * @since 1.0.0
				 */
				wp_enqueue_style( 'hre-shortcode-property-agreement' );
				wp_enqueue_script( 'hre-shortcode-property-agreement' );

				// Load client data.
				$this->load_client_data();
			}
		);
	}

}
