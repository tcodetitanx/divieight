<?php
/**
 * REST controller for buyer features.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use HRE_Addon\Includes\helpers\Product_Helper;
use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

use function HRE_Addon\hre_get_admin_settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Admin_Settings.
 */
class Rest_Buyer extends WP_REST_Controller {

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
		$this->resource_name = 'buyer';
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
		// Generate product.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/become-a-user/save-form/generate-product',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_form_generate_product' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return true;
				},
				'args'                => array(
					'email'                      => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_email',
					),
					'username'                   => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'full_name'                  => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'phone'                      => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'password'                   => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'state'                      => array(
						'required'          => true,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
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
	 * Submit the form to generate product.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function save_form_generate_product( WP_REST_Request $request ) {
		$username                   = $request->get_param( 'username' );
		$email                      = $request->get_param( 'email' );
		$phone                      = $request->get_param( 'phone' );
		$state                      = $request->get_param( 'state' );
		$password                   = $request->get_param( 'password' );
		$full_name                  = $request->get_param( 'full_name' );
		$comment                    = $request->get_param( 'comment' );


		// Validate email.
		if ( ! is_email( $email ) ) {
			return new WP_Error( 'invalid_email', 'Invalid email.' );
		}

		// Check if the user exists.
		$user_by_email = get_user_by( 'email', $email );
		$user_by_login = get_user_by( 'login', $username );
		if ( $user_by_login instanceof \WP_User ) {
			return new WP_Error( 'username_exists', 'Username already exists' );
		}
		if ( $user_by_email instanceof \WP_User ) {
			return new WP_Error( 'email_exists', 'Email already exists' );
		}

		$product_title = sprintf(
		/* translators: %s: The full name of the user. */
			__( 'Buyer Application for %s', 'hre-addon' ),
			$full_name
		);

		$product_helper = new Product_Helper();

		$admin_settings = hre_get_admin_settings();
		$product_id     = $product_helper->create_product(
			array(
				'title'    => $product_title,
				'price'    => $admin_settings['buyer_application_fee'],
				'quantity' => 1,
			),
			array(
				'username'  => $username,
				'email'     => $email,
				'phone'     => $phone,
				'state'     => $state,
				'password'  => $password,
				'full_name' => $full_name,
				// seller agent's details.
			)
		);

		if ( is_wp_error( $product_id ) ) {
			return new WP_Error(
				'product_creation_failed',
				'Product creation failed',
				array(
					'message' => $product_id->get_error_message(),
				)
			);
		}

		return new WP_REST_Response(
			array(
				'product_id' => $product_id,
			),
			200
		);
	}

}
