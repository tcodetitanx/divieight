<?php
/**
 * Displays info about the plugin.
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Includes\Pages;

use HRE_Addon\Initializer;

use function HRE_Addon\hre_get_admin_settings;
use function HRE_Addon\mp_get_all_wp_pages;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}


/**
 * Class Admin_Page_Info
 */
class Admin_Page_Info {
	/**
	 * AdminAuth constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
	}

	/**
	 * Initialize this class.
	 *
	 * @return $this
	 */
	public function init(): self {

		return $this;
	}

	/**
	 * Load the view.
	 *
	 * @return void
	 */
	public function load_view(): void {
		/**
		 * Enqueue the admin settings scripts.
		 *
		 * @since 1.0.0
		 */
//		do_action( 'hre_enqueue_default_admin_page_settings' );
		$pdf_url = Initializer::$plugin_url . '/assets/doc/Houzes Real Eastate Addone - Milestone 4 Update.pdf';
		echo "<iframe style='width:100%; height:100%;' src='{$pdf_url}'></iframe>";
	}

	/**
	 * Load the client data.
	 *
	 * @return void
	 */
	public function load_client_data(): void {
		$all_pages      = mp_get_all_wp_pages();
		$admin_settings = hre_get_admin_settings();

		Initializer::add_to_client_data( 'wp_pages', $all_pages );
		Initializer::add_to_client_data( 'admin_settings', $admin_settings );
	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts(): void {

		$css = mp_get_style( '/admin/pages/admin-page-settings' );
		$js  = mp_get_script( '/admin/pages/admin-page-settings' );

		wp_enqueue_editor();
		wp_register_style( 'hre-admin-page-settings', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-admin-page-settings',
			$js,
			array( 'jquery', 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ),
			Initializer::$script_version,
			true
		);

		/**
		 * Enqueue the admin settings scripts.
		 *
		 * @since 1.0.0
		 */
		add_action(
			'hre_enqueue_default_admin_page_settings',
			function () {
				/**
				 * Enqueue the admin default scripts.
				 *
				 * @since 1.0.0
				 */
				do_action( 'hre_enqueue_default_admin_scripts' );
				wp_enqueue_style( 'hre-admin-page-settings' );
				wp_enqueue_script( 'hre-admin-page-settings' );
				$this->load_client_data();
			}
		);
	}


}
