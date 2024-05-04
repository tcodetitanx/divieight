<?php
/**
 * Checkout service.
 *
 * @package HRE_Addon\Includes\helpers;
 */

namespace HRE_Addon\Includes\services;

use HRE_Addon\Libs\Settings;
use WP_Error;
use function HRE_Addon\hre_buyer_has_paid;
use function HRE_Addon\hre_get_elite_application_product_user_form;
use function HRE_Addon\hre_get_client_settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'silence' ); // exit if accessed directly.
}

/**
 * Class Property_Apply_Sections
 *
 * @package Beauty_Loaylty_Point\Includes\Helpers
 */
class Property_Apply_Sections {

	/**
	 * Initialize the class.
	 */
	public function init(): void {
		// add_action( 'get_template_part_property-details/property-title', array( $this, 'display_on_property' ) );
		// add_action( 'get_template_part_' . 'property-details/single-property', array( $this, 'display_on_property' ) );
		add_action(
			sprintf( '%1$s%2$s', 'get_template_part_', 'property-details/description' ),
			array(
				$this,
				'add_section_to_properties',
			)
		);
	}

	/**
	 * Add section to single property page.
	 *
	 * @return void
	 */
	public function add_section_to_properties(): void {
		if ( ! is_admin() && is_singular( 'property' ) ) {
			$this->display_apply_html_section();
		}
	}

	/**
	 * Display apply section.
	 *
	 * @return void
	 */
	private function display_apply_html_section(): void {
		global $post;
		$post_id      = $post->ID;
		$property_url = get_permalink( $post_id );

		$user_id         = get_current_user_id();
		$client_settings = hre_get_client_settings();
		$link            = $client_settings['agreement_page_url'];
		$buyer_has_paid  = hre_buyer_has_paid( $user_id );
		if ( ! $buyer_has_paid ) {
			$link = $client_settings['buyer_elite_signup_page_url'] . '?redirect=' . $property_url;
		}

		?>
		<div class='property-description-wrap property-section-wrap' id='property-description-wrap'>
			<div class='block-wrap'>
				<div class='block-title-wrap'>
					<h2><?php echo esc_attr__( 'Apply', 'hre-addon' ); ?></h2>
				</div>
				<div class='block-content-wrap'>
					<p><?php echo esc_attr__( 'Apply for this property as a buyer.', 'hre-addon' ); ?></p>
					<br/>
					<?php if ( $buyer_has_paid ) : ?>
						<!--						<button style='width: 100px;' class='btn btn-secondary'>-->
						<!--							--><?php // echo esc_attr__( 'Apply', 'hre-addon' ); ?>
						<!--						</button>-->

						<div class="hre-property-root"
							 data-property-id="<?php echo esc_attr( $post_id ); ?>"> <?php echo 'loading...'; ?></div>
					<?php else : ?>
						<a href="<?php echo esc_url_raw( $link ); ?>" style="width: 100px;" class="btn btn-secondary">
							<?php echo esc_attr__( 'Apply', 'hre-addon' ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php

		if ( $buyer_has_paid ) {
			wp_enqueue_script( 'hre-property-section-apply' );
			wp_enqueue_style( 'hre-property-section-apply' );
		}

	}

}
