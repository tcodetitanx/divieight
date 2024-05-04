<?php
/**
 * Script service.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\services;

use HRE_Addon\Initializer;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'silence' ); // exit if accessed directly.
}

/**
 * Class Script_Service
 *
 * @package HRE_Addon\Includes\services;
 */
class Script_Service {

	/**
	 * Initialize the class.
	 */
	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_property_section_apply' ) );
	}


	/**
	 * Enqueue property section apply.
	 *
	 * @return void
	 */
	public function enqueue_property_section_apply(): void {
		$js  = mp_get_script( '/public/property-section-apply' );
		$css = mp_get_style( '/public/property-section-apply' );
		wp_register_style(
			'hre-property-section-apply',
			$css,
			array(),
			Initializer::$script_version
		);
		wp_register_script(
			'hre-property-section-apply',
			$js,
			array(
				'jquery',
				'wp-element',
				'wp-components',
			),
			Initializer::$script_version,
			true
		);
	}


}
