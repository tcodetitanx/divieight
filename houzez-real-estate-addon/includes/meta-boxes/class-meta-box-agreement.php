<?php
/**
 * Metabox Agreement.
 *
 * @package    HRE_Addon\Includes\CPT
 */

namespace HRE_Addon\Includes\CPT;

use HRE_Addon\Includes\Model\Booking;
use HRE_Addon\Initializer;
use HRE_Addon\Libs\Settings;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Meta_Box_Agreement
 */
class Meta_Box_Agreement {

	/**
	 *  constructor.
	 */
	public function __construct() {

	}

	/**
	 * Initialize the meta box.
	 */
	public function init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
	}

	/**
	 * Save the meta box data.
	 *
	 * @param int $post_id The post ID.
	 */
	public function save_meta_box_data( int $post_id ): void {
		$nonce = filter_input( INPUT_POST, 'hre_nonce', FILTER_SANITIZE_STRING );
		// Check if our nonce is set.
		if ( ! $nonce ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'hre_addon' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$agreement  = filter_input( INPUT_POST, 'hre_property_agreement', FILTER_SANITIZE_STRING );
		$agreement2 = filter_input( INPUT_POST, 'hre_property_agreement_2', FILTER_SANITIZE_STRING );
		$sign_mode  = filter_input( INPUT_POST, 'hre_sign_mode', FILTER_SANITIZE_STRING );

		if ( empty( $agreement ) ) {
			$agreement = get_option( Settings::OPTION_DEFAULT_AGREEMENT_1 );
		}
		if ( empty( $agreement2 ) ) {
			$agreement2 = get_option( Settings::OPTION_DEFAULT_AGREEMENT_2 );
		}
		if ( empty( $sign_mode ) ) {
			$sign_mode = 'simple';
		}

		// Update the meta field.
		update_post_meta( $post_id, Settings::PM_PROPERTY_AGREEMENT, $agreement );
		update_post_meta( $post_id, Settings::PM_PROPERTY_AGREEMENT_2, $agreement2 );
		update_post_meta( $post_id, Settings::PM_PROPERTY_SIGN_MODE, $sign_mode );
	}

	/**
	 * Adds the metabox to the post editor screen.
	 */
	public function add_meta_box(): void {
		add_meta_box(
			'hre_booking_details',
			__( 'Agreement', 'hre-addon' ),
			array( $this, 'render_meta_box' ),
			'property', // todo Change to property later.
			'advanced',
			'high'
		);
	}

	/**
	 * Renders the content of the metabox.
	 */
	public function render_meta_box(): void {
		global $post;

		// Get the saved value of the dropdown.
		$agreement  = get_post_meta( $post->ID, Settings::PM_PROPERTY_AGREEMENT, true );
		$agreement2 = get_post_meta( $post->ID, Settings::PM_PROPERTY_AGREEMENT_2, true );
		$sign_mode  = get_post_meta( $post->ID, Settings::PM_PROPERTY_SIGN_MODE, true );
		if ( empty( $agreement ) ) {
			$agreement = get_option( Settings::OPTION_DEFAULT_AGREEMENT_1 );
		}
		if ( empty( $agreement2 ) ) {
			$agreement2 = get_option( Settings::OPTION_DEFAULT_AGREEMENT_2 );
		}
		if ( empty( $sign_mode ) ) {
			$sign_mode = 'simple';
		}

		do_action( 'hre_enqueue_default_admin_meta_box_agreement' );

		echo sprintf(
			'<div id="hre-meta-box-agreement" data-agreement-1="%1$s" data-agreement-2="%3$s" data-sign-mode="%4$s">%2$s</div>',
			$agreement,
			esc_attr__( 'Loading...', 'hre-addon' ),
			$agreement2,
			esc_attr( $sign_mode )
		);

		$nonce = wp_create_nonce( 'hre_addon' );
		echo sprintf(
			'<input type="hidden" name="hre_nonce" value="%1$s" />',
			esc_attr( $nonce )
		);
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts(): void {

		$css = mp_get_style( '/admin/meta-boxes/meta-box-agreement' );
		$js  = mp_get_script( '/admin/meta-boxes/meta-box-agreement' );

		wp_register_style( 'hre-admin-meta-box-agreement', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-admin-meta-box-agreement',
			$js,
			array( 'jquery', 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ),
			Initializer::$script_version,
			true
		);
		// Enqueue default admin styles to add .button classes.
		wp_enqueue_style( 'wp-admin' );

		/**
		 * Enqueue the admin settings scripts.
		 *
		 * @since 1.0.0
		 */
		add_action(
			'hre_enqueue_default_admin_meta_box_agreement',
			function () {
				wp_enqueue_style( 'hre-admin-meta-box-agreement' );
				wp_enqueue_script( 'hre-admin-meta-box-agreement' );
			}
		);
	}

}
