<?php
/**
 * Registers custom post type Users.
 *
 * @package    HRE_Addon\Includes\CPT
 */

namespace HRE_Addon\Includes\CPT;

use HRE_Addon\Initializer;
use HRE_Addon\Libs\Settings;
use function HRE_Addon\mp_get_script;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class CPT_Users
 */
class CPT_Users {

	/**
	 * CPT_Users constructor.
	 */
	public function __construct() {
	}

	public function init() {
		$this->initialize();

		return $this;
	}

	/**
	 * Initialize this class.
	 *
	 * @return void
	 */
	public function initialize() {
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'init', array( $this, 'on_init' ) );
	}

	/**
	 * Run on init.
	 *
	 * @return void
	 */
	public function on_init() {
		register_post_type(
			Settings::POST_TYPE_USERS,
			array(
				'labels'                         => array(
					'name'          => __( 'Balance Sheet', 'hre-addon' ),
					'singular_name' => __( 'Composer Card', 'hre-addon' ),
				),
				// 'public'              => true,
								   'has_archive' => true,
				'show_in_rest'                   => true,
				'supports'                       => array( 'title' ),
				'exclude_from_search'            => true,
				'show_ui'                        => true,
				'show_in_menu'                   => true,
			)
		);
	}

	/**
	 * Load Client data.
	 *
	 * @return void
	 */
	public function load_client_data() {

	}

	/**
	 * Register scripts.
	 *
	 * @return void
	 */
	public function register_scripts() {
		$css = mp_get_script( 'cpt/cpt-users.css' );
		$js  = mp_get_script( 'cpt/cpt-users.js' );

		wp_register_style( 'ref-cpt-users', $css, array(), Initializer::$script_version );
		// wp-components is important so React will be loaded.
		wp_register_script(
			'ref-cpt-users',
			$js,
			array(
				'jquery',
				'wp-components',
			),
			Initializer::$script_version,
			true
		);

		/**
		 * Enqueue cpt users scripts and styles.
		 *
		 * @since 2.0.0
		 */
		add_action(
			'ref_enqueue_cpt_users',
			function () {
				do_action( 'ref_enqueue_default_admin_scripts' );
				wp_enqueue_style( 'ref-cpt-users' );
				wp_enqueue_script( 'ref-cpt-users' );

				$this->load_client_data();
			}
		);
	}

	/**
	 * Enqueue the Post type composer card scripts
	 *
	 * @return void
	 */
	public static function enqueue_post_type_composer_card_scripts() {
		/**
		 *  Enqueue the Post type composer card scripts.
		 */
		do_action( 'cbb_enqueue_post_type_composer_card' );
	}

}
