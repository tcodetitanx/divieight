<?php
/**
 * REST controller for admin settings.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function HRE_Addon\hre_save_admin_settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Admin_Settings.
 */
class Rest_Admin_Settings extends WP_REST_Controller {

	/**
	 * The resource name.
	 *
	 * @var string $resource_name The resource name.
	 */
	protected string $resource_name;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->namespace     = Settings::REST_NAMESPACE;
		$this->resource_name = 'admin';
	}

	/**
	 * Initialize the rest endpoints.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes for the objects of the controller.
	 *
	 * @return void
	 */
	public function register_routes(): void {
		// Save admin settings.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/save-settings',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_settings' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'buyer_elite_access_fee'              => array(
						'description'       => __( 'The buyer elite access fee.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => true,
					),
					'seller_elite_access_fee'             => array(
						'description'       => __( 'The seller elite access fee.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => true,
					),
					'buyer_elite_signup_page_id'          => array(
						'description'       => __( 'The buyer elite signup page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_elite_login_page_id'           => array(
						'description'       => __( 'The buyer elite login page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_preference_page_id'            => array(
						'description'       => __( 'The buyer preference page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'seller_elite_signup_page_id'         => array(
						'description'       => __( 'The seller elite signup page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'agent_login_page_id'                 => array(
						'description'       => __( 'The agent login page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'agent_signup_page_id'                => array(
						'description'       => __( 'The agent signup page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'seller_elite_login_page_id'          => array(
						'description'       => __( 'The seller elite login page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'property_agreement_page_id'          => array(
						'description'       => __( 'The property agreement page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_dashboard_page_id'             => array(
						'description'       => __( 'The buyer dashboard page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'default_agreement'                   => array(
						'description'       => __( 'The default agreement.', 'hre-addon' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_textarea_field',
						'required'          => true,
					),
					'default_agreement2'                  => array(
						'description'       => __( 'The default agreement 2.', 'hre-addon' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_textarea_field',
						'required'          => true,
					),
					'search_by_map_page_id'               => array(
						'description'       => __( 'The search by map page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'terms_and_conditions_page_id'        => array(
						'description'       => __( 'The terms and conditions page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'elite_membership_duration_months'    => array(
						'description'       => __( 'The elite membership duration months.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_onboarding_process_1_page_id'  => array(
						'description'       => __( 'The buyer onboarding process 1 page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_onboarding_process_2_page_id'  => array(
						'description'       => __( 'The buyer onboarding process 2 page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'seller_onboarding_process_1_page_id' => array(
						'description'       => __( 'The seller onboarding process 1 page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'seller_elite_page_id'                => array(
						'description'       => __( 'The seller elite page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'buyer_elite_page_id'                 => array(
						'description'       => __( 'The buyer elite page id.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'create_listing_page_id'              => array(
						'description'       => __( 'The page where agents can create listings.', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => true,
					),
					'google_captcha_site_key'             => array(
						'description'       => __( 'The google captcha site key.', 'hre-addon' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => false,
					),
					'google_captcha_secret_key'           => array(
						'description'       => __( 'The google captcha secret key.', 'hre-addon' ),
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
						'required'          => false,
					)

				),
			),
		);

		// Get the schema for this endpoint.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/schema',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_public_item_schema' ),
				'permission_callback' => function () {
					return true;
				},
			)
		);
	}

	/**
	 * Save admin settings.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_settings( WP_REST_Request $request ) {
		$buyer_elite_access_fee              = $request->get_param( 'buyer_elite_access_fee' );
		$seller_elite_access_fee             = $request->get_param( 'seller_elite_access_fee' );
		$buyer_elite_signup_page_id          = $request->get_param( 'buyer_elite_signup_page_id' );
		$buyer_elite_login_page_id           = $request->get_param( 'buyer_elite_login_page_id' );
		$seller_elite_signup_page_id         = $request->get_param( 'seller_elite_signup_page_id' );
		$agent_signup_page_id                = $request->get_param( 'agent_signup_page_id' );
		$agent_login_page_id                 = $request->get_param( 'agent_login_page_id' );
		$seller_elite_login_page_id          = $request->get_param( 'seller_elite_login_page_id' );
		$property_agreement_page_id          = $request->get_param( 'property_agreement_page_id' );
		$buyer_dashboard_page_id             = $request->get_param( 'buyer_dashboard_page_id' );
		$default_agreement                   = $request->get_param( 'default_agreement' );
		$default_agreement2                  = $request->get_param( 'default_agreement2' );
		$search_by_map_page_id               = $request->get_param( 'search_by_map_page_id' );
		$terms_and_conditions_page_id        = $request->get_param( 'terms_and_conditions_page_id' );
		$elite_membership_duration_months    = $request->get_param( 'elite_membership_duration_months' );
		$buyer_onboarding_process_1_page_id  = $request->get_param( 'buyer_onboarding_process_1_page_id' );
		$seller_onboarding_process_1_page_id = $request->get_param( 'seller_onboarding_process_1_page_id' );
		$seller_elite_page_id                = $request->get_param( 'seller_elite_page_id' );
		$buyer_elite_page_id                 = $request->get_param( 'buyer_elite_page_id' );
		$create_listing_page_id              = $request->get_param( 'create_listing_page_id' );
		$buyer_preference_page_id            = $request->get_param( 'buyer_preference_page_id' );
		$buyer_onboarding_process_2_page_id  = $request->get_param( 'buyer_onboarding_process_2_page_id' );
		$google_captcha_site_key             = $request->get_param( 'google_captcha_site_key' );
		$google_captcha_secret_key           = $request->get_param( 'google_captcha_secret_key' );

		// Save the settings.
		hre_save_admin_settings(
			array(
				'buyer_elite_access_fee'              => $buyer_elite_access_fee,
				'seller_elite_access_fee'             => $seller_elite_access_fee,
				'buyer_elite_signup_page_id'          => $buyer_elite_signup_page_id,
				'buyer_elite_login_page_id'           => $buyer_elite_login_page_id,
				'seller_elite_signup_page_id'         => $seller_elite_signup_page_id,
				'buyer_preference_page_id'         => $buyer_preference_page_id,
				'agent_login_page_id'                 => $agent_login_page_id,
				'agent_signup_page_id'                => $agent_signup_page_id,
				'seller_elite_login_page_id'          => $seller_elite_login_page_id,
				'property_agreement_page_id'          => $property_agreement_page_id,
				'buyer_dashboard_page_id'             => $buyer_dashboard_page_id,
				'default_agreement'                   => $default_agreement,
				'default_agreement2'                  => $default_agreement2,
				'search_by_map_page_id'               => $search_by_map_page_id,
				'create_listing_page_id'              => $create_listing_page_id,
				'terms_and_conditions_page_id'        => $terms_and_conditions_page_id,
				'elite_membership_duration_months'    => $elite_membership_duration_months,
				'buyer_onboarding_process_1_page_id'  => $buyer_onboarding_process_1_page_id,
				'seller_onboarding_process_1_page_id' => $seller_onboarding_process_1_page_id,
				'seller_elite_page_id'                => $seller_elite_page_id,
				'buyer_elite_page_id'                 => $buyer_elite_page_id,
				'buyer_onboarding_process_2_page_id'  => $buyer_onboarding_process_2_page_id,
				'google_captcha_site_key'             => $google_captcha_site_key,
				'google_captcha_secret_key'           => $google_captcha_secret_key

			)
		);

		return new WP_REST_Response( true, 200 );
	}

}
