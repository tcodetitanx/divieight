<?php
/**
 * Metabox Booking.
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
 * Class Meta_Box_Booking
 */
class Meta_Box_Booking {

	/**
	 *  constructor.
	 */
	public function __construct() {

	}

	/**
	 * Initialize the meta box.
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
//		add_action( 'save_post', array( $this, 'save_meta_box_data' ) );
	}

	/**
	 * Adds the metabox to the post editor screen.
	 */
	public function add_meta_box() {
		add_meta_box(
			'hre_booking_details',
			__( 'Booking Detail', 'hre-addon' ),
			array( $this, 'render_meta_box' ),
			Settings::POST_TYPE_BOOKING,
			'advanced',
			'high'
		);
	}

	/**
	 * Renders the content of the metabox.
	 */
	public function render_meta_box() {
		global $post;

		// Get the saved value of the dropdown.
		$booking = ( new Booking() )
			->set_id( $post->ID )
			->read( $post->ID );

		if ( ! ( $booking instanceof Booking ) ) {
			echo sprintf(
				'<h3>%$1s</h3>',
				esc_attr__( 'No Booking Found.', 'hre-addon' )
			);

			return;
		}

		do_action( 'hre_enqueue_default_admin_meta_box_booking' );

		echo sprintf(
			'<div id="hre-meta-box-booking" data-booking-id="%1$s" data-user-id="%3$s">%2$s</div>',
			esc_attr( $booking->get_id() ),
			__( 'Loading...', 'hre-addon' ),
			esc_attr( $booking->get_user_id() )
		);
	}


	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {

		$css = mp_get_style( '/admin/meta-boxes/meta-box-booking' );
		$js  = mp_get_script( '/admin/meta-boxes/meta-box-booking' );

		wp_register_style( 'hre-admin-meta-box-booking', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-admin-meta-box-booking',
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
			'hre_enqueue_default_admin_meta_box_booking',
			function () {
				wp_enqueue_style( 'hre-admin-meta-box-booking' );
				wp_enqueue_script( 'hre-admin-meta-box-booking' );
			}
		);
	}


}
