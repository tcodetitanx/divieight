<?php
/**
 * REST controller for Rest_Eyp_Auth_Controller.
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
use WP_User;

use function HRE_Addon\hre_get_admin_settings;
use function HRE_Addon\hre_get_user_debug_info;
use function HRE_Addon\hre_is_buyer_or_seller;
use function HRE_Addon\hre_save_user_debug_info;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Auth.
 */
class Rest_Auth extends WP_REST_Controller {
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
		$this->resource_name = 'auth';
	}

	/**
	 * Initialize the rest endpoints.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );

		// Add custom value - checkout user hash.
		// add_filter( 'rest_prepare_user', array( $this, 'add_checkout_user_hash_to_user_api_response' ), 10, 3 );
	}

	/**
	 *  Get schema for password recovery send otp.
	 *
	 * @return array
	 */
	public function get_password_recovery_send_otp_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name,
			'type'       => 'object',
			'properties' => array(
				'username' => array(
					'description'       => __( 'The username or EMAIL of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 30,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},

				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 *  Get schema for password recovery send otp.
	 *
	 * @return array
	 */
	public function get_password_recovery_send_verify_otp_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name,
			'type'       => 'object',
			'properties' => array(
				'username' => array(
					'description'       => __( 'The username or EMAIL of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
				),
				'otp'      => array(
					'description'       => __( 'The OTP that was sent to the Email.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 4,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 *  Get schema for password recovery reset password.
	 *
	 * @return array
	 */
	public function get_password_recovery_reset_password(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name,
			'type'       => 'object',
			'properties' => array(
				'username' => array(
					'description'       => __( 'The username or EMAIL of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 30,
					'pattern'           => '^[a-zA-Z0-9_]*$',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'sanitize_callback' => 'sanitize_text_field',
				),
				'otp'      => array(
					'description'       => __( 'The OTP that was sent to the Email . ', 'job -and- promotion' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 4,
					'pattern'           => '^[0-9]*$',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'sanitize_callback' => 'sanitize_text_field',
				),
				'password' => array(
					'description'       => __( 'The new password . ', 'job -and- promotion' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 30,
					'pattern'           => '^[a-zA-Z0-9_]*$',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Register the routes for the objects of the controller.
	 */
	public function register_routes() {
		// NO LONGER IN USE. Now using JWT. Authenticate user.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/authenticate-user',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'authenticate_user' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'user_name'     => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'user_password' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);


		// Signup.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/signup',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'signup' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_signup_schema()['properties'],
				),
			),
		);

		// Signup Buyer.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/signup-buyer',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'signup_buyer' ),
					'permission_callback' => '__return_true',
//					'args'                => $this->get_signup_schema()['properties'],
					'args'                => array(
						'username'                                 => array(
							'description'       => __( 'The username of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'minLength'         => 4,
							'maxLength'         => 30,
							'pattern'           => '^[a-zA-Z0-9_]*$',
							'unique'            => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'email'                                    => array(
							'description'       => __( 'The email of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'full_name'                                => array(
							'description'       => __( 'The full name of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'required'          => true,
						),
						'phone'                                    => array(
							'description'       => __( 'The phone of the buyer', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'required'          => true,
						),
						'state'                                    => array(
							'description'       => __( 'The state of the buyer', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'required'          => true,
						),
						'password'                                 => array(
							'description'       => __( 'The password of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'minLength'         => 4,
							'maxLength'         => 30,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
						),
						'was_referred'                             => array(
							'type'        => 'string',
							'enum'        => array( 'yes', 'no', 'other_means' ),
							'description' => __( 'Indicates whether the user was referred.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
						'referrer_full_name'                       => array(
							'type'        => 'string',
							'description' => __( 'The full name of the referrer.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
						'referrer_email'                           => array(
							'type'        => 'string',
							'description' => __( 'The email of the referrer.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
						'referrer_phone'                           => array(
							'type'        => 'string',
							'description' => __( 'The phone number of the referrer.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
						'referrer_is_agent_or_broker'              => array(
							'type'        => 'string',
							'enum'        => array( 'yes', 'no' ),
							'description' => __( 'Indicates whether the referrer is an agent or broker.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
						'referrer_is_agent_or_broker_confirmation' => array(
							'type'        => 'string',
							'enum'        => array( 'yes', 'no' ),
							'description' => __( 'Confirmation of whether the referrer is an agent or broker.', 'hre-addon' ),
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
						),
					)
				),
			),
		);

		// Signup Agent.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/signup-agent',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'agent_signup' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'first_name'               => array(
							'description'       => __( 'First name of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'last_name'                => array(
							'description'       => __( 'Last name of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'licensed_agent'           => array(
							'description'       => __( 'Whether the agent is licensed', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return $param === 'yes' || $param === 'no';
							},
							'sanitize_callback' => function ( $param ) {
								return in_array( $param, array( 'yes', 'no' ) ) ? $param : 'no';
							},
						),
						'username'                 => array(
							'description'       => __( 'Username of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => false,
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'license_state'            => array(
							'description'       => __( 'License state of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'license_number'           => array(
							'description'       => __( 'License number of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'name_of_agency'           => array(
							'description'       => __( 'Name of the agency of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'state'                    => array(
							'description'       => __( 'State of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'city'                     => array(
							'description'       => __( 'City of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'zip_code'                 => array(
							'description'       => __( 'Zip code of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'name_of_principal_broker' => array(
							'description'       => __( 'Name of the principal broker of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'email'                    => array(
							'description'       => __( 'Email of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => 'is_email',
							'sanitize_callback' => 'sanitize_email',
						),
						'phone'                    => array(
							'description'       => __( 'Phone number of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'phone_landline'           => array(
							'description'       => __( 'The landline number of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'password'                 => array(
							'description'       => __( 'Password of the agent', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'recatpcha_token'          => array(
							'description' => __( 'Recaptcha token', 'hre-addon' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
							'required'    => true,
						),
					)
				),
			),
		);

		// Login.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/login',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'login' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'username' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
						'password' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			),
		);

		// Send OTP - Password recovery.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/password-recovery/send-otp',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'password_recovery_send_otp' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_password_recovery_send_otp_schema()['properties'],
				),
			),
		);

		// Verify OTP - Password recovery.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/password-recovery/verify-otp',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'password_recovery_verify_otp' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_password_recovery_send_verify_otp_schema()['properties'],
				),
			),
		);

		// Reset Password - Password recovery.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/password-recovery/reset-password',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'password_recovery_reset_password' ),
					'permission_callback' => '__return_true',
					'args'                => $this->get_password_recovery_reset_password()['properties'],
				),
			),
		);

		// Save user debug info.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/save-user-debug-info',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_user_debug_info' ),
					'permission_callback' => function ( WP_REST_Request $request ) {
						return is_user_logged_in();
					},
					'args'                => array(
						'custom_current_datetime' => array(
							'required'          => true,
							'type'              => 'string',
							'sanitize_callback' => 'sanitize_text_field',
						),
					)
				),
			),
		);

		// Get user debug info.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/get-user-debug-info',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_user_debug_info' ),
					'permission_callback' => function ( WP_REST_Request $request ) {
						return is_user_logged_in();
					},
					'args'                => array()
				),
			),
		);

		// Save buyer preference.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/save-buyer-preference',
			array(
				array(
					'methods'             => WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'save_buyer_preference' ),
					'permission_callback' => '__return_true',
					'args'                => array(
						'first_name'                                 => array(
							'description'       => __( 'The first name of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'required'          => true,
						),
						'last_name'                                  => array(
							'description'       => __( 'The last name of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',

						),
						'email'                                      => array(
							'description'       => __( 'The email of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'phone'                                      => array(
							'description'       => __( 'The phone of the user.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'state'                                      => array(
							'description'       => __( 'The state of the buyer.', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'preferred_budget'                           => array(
							'description'       => __( "The buyer's preferred budget", 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'no_of_1_8th_interest'                       => array(
							'description'       => __( "The buyer's no of 1/8th interest", 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'do_you_need_recommendation_for_buyer_agent' => array(
							'description' => __( "If the buyer needs recommendation for buyer agent.", 'hre-addon' ),
							'type'        => 'string',
							'context'     => array( 'view', 'edit', 'embed' ),
							'readonly'    => true,
							'required'    => true,
							'enum'        => array( 'yes', 'no' ),
						),
						'comment'                                    => array(
							'description'       => __( "Any comment", 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),
						'first_choice'                               => array(
							'description'       => __( 'First choice', 'hre-addon' ),
							'type'              => 'string',
							'context'           => array( 'view', 'edit', 'embed' ),
							'readonly'          => true,
							'required'          => true,
							'validate_callback' => function ( $param ) {
								return is_string( $param ) && ! empty( $param );
							},
							'sanitize_callback' => 'sanitize_text_field',
						),

					),
				),
			),
		);

		// Get buyer preference.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/get-buyer-preference',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_buyer_preference' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'user_id' => array(
						'description'       => __( 'The user id of the buyer.', 'hre-addon' ),
						'type'              => 'integer',
						'context'           => array( 'view', 'edit', 'embed' ),
						'readonly'          => true,
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param ) && (int) $param > 0;
						},
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			),
		);

		// Get agent details.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/get-agent-details',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_agent_detail' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'agent_id' => array(
						'description'       => __( 'The agent id.', 'hre-addon' ),
						'type'              => 'integer',
						'context'           => array( 'view', 'edit', 'embed' ),
						'readonly'          => true,
						'required'          => true,
						'validate_callback' => function ( $param ) {
							return is_numeric( $param ) && (int) $param > 0;
						},
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			),
		);

		// Generate elite product with signup.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/generate-elite-access-product-with-signup',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_form_generate_product_with_signup' ),
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
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'for'                        => array(
						'required'          => true,
						'type'              => 'string',
						'enum'              => array( 'seller', 'buyer' ),
						'sanitize_callback' => 'sanitize_text_field',
					),
					// Seller's agent info.
					'seller_agent_id'            => array(
						'description'       => __( 'Seller agent id', 'hre-addon' ),
						'type'              => 'number',
						'sanitize_callback' => 'absint',
						'required'          => false
					),
					'seller_agent_doesnt_exists' => array(
						'required' => false,
						'type'     => 'string',
						'enum'     => array( 'yes', 'no' ),
					),
					'seller_agent_first_name'    => array(
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'seller_agent_last_name'     => array(
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'seller_agent_phone'         => array(
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			),
		);

		// Generate Elite product without signup.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/generate-elite-access-product-without-signup',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'save_form_generate_product_without_signup' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return true;
				},
				'args'                => array(),
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
	 * Check if a given user has permission to do this in items.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function permission_check( WP_REST_Request $request ) {
		return true;
	}

	/**
	 *  Get item schema.
	 *
	 * @return array
	 */
	public function get_item_schema(): array {
		$schema = array(
			'schema'     => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name,
			'type'       => 'object',
			'properties' => array(
				'id'                   => array(
					'description' => __( 'Unique identifier for the object.', 'eyippee-web-app' ),
					'type'        => 'integer',
					'context'     => array( 'view', 'edit', 'embed' ),
					'readonly'    => false,
				),
				'session_id'           => array(
					'description'       => __( 'Unique identifier for the object.', 'eyippee-web-app' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_numeric( $param ) && (int) $param > 0;
					},
				),
				'product_id'           => array(
					'description'       => __( 'Product id', 'eyippee-web-app' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_numeric( $param ) && (int) $param > 0;
					},
				),
				'quantity'             => array(
					'description'       => __( 'Quantity', 'eyippee-web-app' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'required'          => true,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_numeric( $param ) && (int) $param > 0;
					},
				),
				'add_to_quantity'      => array(
					'description'       => __( 'Whether to add to quantity or update it.', 'eyippee-web-app' ),
					'type'              => 'enum',
					'enum'              => array( 'yes', 'no' ),
					'context'           => array( 'view', 'edit', 'embed' ),
					'required'          => false,
					'validate_callback' => function ( $param, $request, $key ) {
						// can be 'yes' or 'no'.
						return in_array( $param, array( 'yes', 'no' ), true );
					},
				),
				'variation_attributes' => array(
					'description'       => __( 'Variation attributes', 'eyippee-web-app' ),
					'type'              => 'array',
					'context'           => array( 'view', 'edit', 'embed' ),
					'required'          => false,
					'validate_callback' => function ( $param, $request, $key ) {
						return is_array( $param );
					},
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 *  Get signup schema.
	 *
	 * @return array
	 */
	public function get_signup_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name,
			'type'       => 'object',
			'properties' => array(
				'username'   => array(
					'description'       => __( 'The username of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 30,
					'pattern'           => '^[a-zA-Z0-9_]*$',
					'unique'            => true,
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'sanitize_callback' => 'sanitize_text_field',
				),
				'email'      => array(
					'description'       => __( 'The email of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'sanitize_callback' => 'sanitize_email',
					'validate_callback' => 'is_email',
				),
				'first_name' => array(
					'description'       => __( 'The first name of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'required'          => true,
				),
				'last_name'  => array(
					'description'       => __( 'The last name of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
					'sanitize_callback' => 'sanitize_text_field',

				),
				'password'   => array(
					'description'       => __( 'The password of the user.', 'hre-addon' ),
					'type'              => 'string',
					'context'           => array( 'view', 'edit', 'embed' ),
					'readonly'          => true,
					'required'          => true,
					'minLength'         => 4,
					'maxLength'         => 30,
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => function ( $param ) {
						return is_string( $param ) && ! empty( $param );
					},
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 *  Get items schema.
	 *
	 * @return array
	 */
	public function get_items_schema(): array {
		$schema = array(
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $this->resource_name . 's',
			'type'       => 'object',
			'properties' => array(
				'session_id' => array(
					'required'          => true,
					'sanitize_callback' => 'absint',
					'validate_callback' => 'sanitize_text_field',
				),
			),
		);

		return $this->add_additional_fields_schema( $schema );
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_user_id_by_jwt( WP_REST_Request $request ) {
		$user = get_current_user();

		if ( ! $user instanceof WP_User ) {
			return new WP_Error( 'rest_user_invalid_id', __( 'Invalid user .' ), array( 'status' => 404 ) );
		}

		return new WP_REST_Response( $user->ID, 200 );
	}

	/**
	 * Signup a user.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function signup( WP_REST_Request $request ) {
		$username   = $request->get_param( 'username' );
		$password   = $request->get_param( 'password' );
		$first_name = $request->get_param( 'first_name' );
		$last_name  = $request->get_param( 'last_name' );
		$email      = $request->get_param( 'email' );

		$user_service = new User_Service();
		$signup       = $user_service
			->signup( $username, $email, $first_name, $last_name, $password );

		if ( $signup instanceof User_Service ) {
			return new WP_REST_Response(
				$user_service
					->read_from_db()
					->to_array(),
				201
			);
		}

		if ( is_wp_error( $signup ) ) {
			return $signup;
		}

		return new WP_Error( 'cant-create', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}


	/**
	 * Signup a buyer.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function signup_buyer( WP_REST_Request $request ) {
		$username                                 = $request->get_param( 'username' );
		$password                                 = $request->get_param( 'password' );
		$full_name                                = $request->get_param( 'full_name' );
		$phone                                    = $request->get_param( 'phone' );
		$email                                    = $request->get_param( 'email' );
		$state                                    = $request->get_param( 'state' );
		$was_referred                             = $request->get_param( 'was_referred' );
		$referrer_full_name                       = $request->get_param( 'referrer_full_name' );
		$referrer_email                           = $request->get_param( 'referrer_email' );
		$referrer_phone                           = $request->get_param( 'referrer_phone' );
		$referrer_is_agent_or_broker              = $request->get_param( 'referrer_is_agent_or_broker' );
		$referrer_is_agent_or_broker_confirmation = $request->get_param( 'referrer_is_agent_or_broker_confirmation' );

		// Make sure user is not already signed up.
		$user = get_user_by( 'email', $email );
		if ( $user instanceof WP_User ) {
			return new WP_Error(
				'user_already_exist',
				__( 'This email is already registered.', 'hre-addon' )
			);
		}

		$user = get_user_by( 'login', $username );
		if ( $user instanceof WP_User ) {
			return new WP_Error(
				'user_already_exist',
				__( 'This username is already registered.', 'hre-addon' )
			);
		}

		$user_service = new User_Service();
		$signup       = $user_service
			->signup_buyer(
				$username,
				$email,
				$full_name,
				$phone,
				$password,
				$state,
				$was_referred,
				$referrer_full_name,
				$referrer_email,
				$referrer_phone,
				$referrer_is_agent_or_broker,
				$referrer_is_agent_or_broker_confirmation
			);

		if ( $signup instanceof User_Service ) {
			return new WP_REST_Response(
				$user_service
					->read_from_db()
					->to_array(),
				201
			);
		}

		if ( is_wp_error( $signup ) ) {
			return $signup;
		}

		return new WP_Error( 'cant-create', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}


	/**
	 * Agent Signup a user.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function agent_signup( WP_REST_Request $request ) {
		$username                 = $request->get_param( 'username' );
		$first_name               = $request->get_param( 'first_name' );
		$last_name                = $request->get_param( 'last_name' );
		$licensed_agent           = $request->get_param( 'licensed_agent' );
		$license_state            = $request->get_param( 'license_state' );
		$state                    = $request->get_param( 'state' );
		$license_number           = $request->get_param( 'license_number' );
		$name_of_agency           = $request->get_param( 'name_of_agency' );
		$city                     = $request->get_param( 'city' );
		$zip_code                 = $request->get_param( 'zip_code' );
		$name_of_principal_broker = $request->get_param( 'name_of_principal_broker' );
		$email                    = $request->get_param( 'email' );
		$phone                    = $request->get_param( 'phone' );
		$phone_landline           = $request->get_param( 'phone_landline' );
		$password                 = $request->get_param( 'password' );
		$recatpcha_token          = $request->get_param( 'recatpcha_token' );

		// Verify recaptcha.
		$admin_settings = hre_get_admin_settings();
		$secrete_key    = $admin_settings['google_captcha_secret_key'];
		$url            = 'https://www.google.com/recaptcha/api/siteverify';
		$args           = array(
			'secret'   => $secrete_key,
			'response' => $recatpcha_token,
		);

		$response = wp_remote_post( $url, array(
			'method' => 'POST',
			'body'   => $args,
		) );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( 'recaptcha-error',
				__( 'Error while verifying recaptcha . ', 'hre-addon' ),
				array(
					'status'  => 500,
					'message' => $response->get_error_message(),
					'data'    => $response->get_error_data(),
				)
			);
		}

		$body = json_decode( $response['body'], true );

		if ( ! $body['success'] ) {
			return new WP_Error(
				'recaptcha-error',
				__( 'Error while verifying recaptcha . ', 'hre-addon' ),
				array(
					'status'  => 500,
					'message' => $response->get_error_message(),
					'data'    => $response->get_error_data(),
					'body'    => $body
				)
			);
		};

		if ( $body['score'] < 0.5 ) {
			return new WP_Error(
				'recaptcha-error',
				__( 'It looks lke you are behaving like a bot. Please reload the page and try again.', 'hre-addon' ),
				array( 'status' => 500 )
			);
		}

		$user_service = new User_Service();
		$signup       = $user_service
			->agent_signup(
				$first_name,
				$last_name,
				$licensed_agent,
				$username,
				$license_state,
				$state,
				$license_number,
				$name_of_agency,
				$city,
				$zip_code,
				$name_of_principal_broker,
				$email,
				$phone,
				$password,
				$phone_landline
			);

		if ( is_wp_error( $signup ) ) {
			return $signup;
		}

		return new WP_REST_Response(
			__( "User created successfully", "hre-addon" ),
			201
		);
	}

	/**
	 * Login a user.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function login( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );
		$password = $request->get_param( 'password' );

		// Try to get user by email.
		$user = get_user_by( 'email', $username );
		if ( ! ( $user instanceof \WP_User ) ) {
			// Try to get user by username.
			$user_by_login = get_user_by( 'login', $username );
			if ( ! ( $user_by_login instanceof \WP_User ) ) {
				return new WP_Error(
					'cant-login',
					__( 'Invalid email or username or password', 'hre-addon' ),
				);
			}
			$user = $user_by_login;
		}

		// Log the user in.
		$user_login = $user->data->user_login;
		$signin     = wp_signon(
			array(
				'user_login'    => $user_login,
				'user_password' => $password,
				'remember'      => true,
			)
		);

		if ( $signin instanceof \WP_User ) {
			return new WP_REST_Response(
				__( 'Logged in successfully.', 'hre-addon' ),
				200
			);
		}

		if ( is_wp_error( $signin ) ) {
			return new WP_Error(
				'cant-login',
				__( 'Invalid email or username or password', 'hre-addon' ),
			);
		}

		return new WP_Error( 'cant-create', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}

	/**
	 * Save form and generate product.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function save_form_generate_product_with_signup( WP_REST_Request $request ) {
		$username  = $request->get_param( 'username' );
		$email     = $request->get_param( 'email' );
		$phone     = $request->get_param( 'phone' );
		$state     = $request->get_param( 'state' );
		$password  = $request->get_param( 'password' );
		$full_name = $request->get_param( 'full_name' );
		$for       = $request->get_param( 'for' );
		// seller agent.
		$seller_agent_id            = $request->get_param( 'seller_agent_id' );
		$seller_agent_doesnt_exists = $request->get_param( 'seller_agent_doesnt_exists' );
		$seller_agent_first_name    = $request->get_param( 'seller_agent_first_name' );
		$seller_agent_last_name     = $request->get_param( 'seller_agent_last_name' );
		$seller_agent_phone         = $request->get_param( 'seller_agent_phone' );
		$seller_agent_state         = $request->get_param( 'seller_agent_state' );

		if ( 'seller' === $for ) {
			if ( 'no' === $seller_agent_doesnt_exists ) {
				// Check if the agent exists.
				$seller_agent = get_user_by( 'id', $seller_agent_id );
				if ( ! ( $seller_agent instanceof \WP_User ) ) {
					return new WP_Error(
						'agent-not-a-user',
						__( 'Your agent is not a user here.', 'hre-addon' ),
					);
				}

				// confirm role.
				$seller_agent = $seller_agent->roles[0];
				if ( Settings::USER_ROLE_AGENT !== $seller_agent ) {
					return new WP_Error(
						'agent-not-a-agent',
						__( 'Your agent is not an agent here.', 'hre-addon' ),
					);
				}
			} else {
				// confirm that agent's first, last and phone is provided.
				if ( empty( $seller_agent_first_name ) || empty( $seller_agent_last_name ) || empty( $seller_agent_phone || empty( $seller_agent_state ) ) ) {
					return new WP_Error(
						'agent-details-missing',
						__( "Please provide your agent first, last phone and your agent's state of residence", 'hre-addon' ),
					);
				}
			}
		}

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
			__( 'Elite access fee for %s', 'hre-addon' ),
			$full_name
		);

		$product_helper = new Product_Helper();

		$admin_settings = hre_get_admin_settings();
		$fee            = $for === 'seller' ? $admin_settings['seller_elite_access_fee'] : $admin_settings['buyer_elite_access_fee'];
		$product_id     = $product_helper->create_product(
			array(
				'title'    => $product_title,
				'price'    => $fee,
				'quantity' => 1,
			),
			array(
				'username'                  => $username,
				'email'                     => $email,
				'phone'                     => $phone,
				'state'                     => $state,
				'password'                  => $password,
				'full_name'                 => $full_name,
				'for'                       => $for,
				// seller agent.
				'seller_agent_id'           => $seller_agent_id,
				'seller_agent_doesnt_exist' => $seller_agent_doesnt_exists,
				'seller_agent_first_name'   => $seller_agent_first_name,
				'seller_agent_last_name'    => $seller_agent_last_name,
				'seller_agent_phone'        => $seller_agent_phone,
				'seller_agent_state'        => $seller_agent_state,
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

	/**
	 * Save form and generate product.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function save_form_generate_product_without_signup( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'not_logged_in',
				__( 'You are not logged in.', 'hre-addon' ),
			);
		}

		$is_buyer_or_seller = hre_is_buyer_or_seller();
		if ( ! $is_buyer_or_seller ) {
			return new WP_Error(
				'not_buyer_or_seller',
				__( 'You are not a buyer or seller.', 'hre-addon' ),
			);
		}

		$user_id   = get_current_user_id();
		$user      = get_user_by( 'id', $user_id );
		$full_name = $user->first_name;

		$product_title = sprintf(
		/* translators: %s: The full name of the user. */
			__( 'Elite access fee renew for %s', 'hre-addon' ),
			$full_name
		);

		$product_helper = new Product_Helper();

		$admin_settings = hre_get_admin_settings();
		$fee            = $is_buyer_or_seller === 'seller' ?
			$admin_settings['seller_elite_access_fee']
			: $admin_settings['buyer_elite_access_fee'];
		$product_id     = $product_helper->create_product(
			array(
				'title'    => $product_title,
				'price'    => $fee,
				'quantity' => 1,
			),
			array(
				'username'  => $user->user_login,
				'email'     => $user->user_email,
				'phone'     => '',
				'state'     => '',
				'password'  => '',
				'full_name' => $full_name,
				'for'       => $is_buyer_or_seller
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

	/**
	 * Password recovery send otp.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function password_recovery_send_otp( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );

		$user_service = new User_Service();
		$send_otp     = $user_service
			->send_otp( $username, 5, 10 );

		if ( $send_otp instanceof User_Service ) {
			return new WP_REST_Response(
				__( 'OTP sent successfully.', 'hre-addon' ),
				200
			);
		}

		if ( is_wp_error( $send_otp ) ) {
			return $send_otp;
		}

		return new WP_Error( 'unknown_error', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}

	/**
	 * Password recovery verify otp.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function password_recovery_verify_otp( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );
		$otp      = $request->get_param( 'otp' );

		$user_service = new User_Service();
		$send_otp     = $user_service
			->verify_otp( $username, $otp, 10 );

		if ( $send_otp instanceof User_Service ) {
			return new WP_REST_Response(
				__( 'OTP Verified.', 'hre-addon' ),
				201
			);
		}

		if ( is_wp_error( $send_otp ) ) {
			return $send_otp;
		}

		return new WP_Error( 'unknown_error', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}

	/**
	 * Password recovery Reset password.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function password_recovery_reset_password( WP_REST_Request $request ) {
		$username = $request->get_param( 'username' );
		$otp      = $request->get_param( 'otp' );
		$password = $request->get_param( 'password' );

		$user_service = new User_Service();
		$send_otp     = $user_service
			->reset_password( $username, $otp, $password );

		if ( $send_otp instanceof User_Service ) {
			return new WP_REST_Response(
				__( 'Password Reset Successful.', 'hre-addon' ),
				201
			);
		}

		if ( is_wp_error( $send_otp ) ) {
			return $send_otp;
		}

		return new WP_Error( 'unknown_error', __( 'Error unknown . ', 'hre-addon' ), array( 'status' => 500 ) );
	}

	/**
	 * Save buyer preference
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function save_buyer_preference( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'cant-save',
				__( 'You need to login to save your preference.', 'hre-addon' ),
			);
		}
		$user_id              = get_current_user_id();
		$first_name           = $request->get_param( 'first_name' );
		$last_name            = $request->get_param( 'last_name' );
		$email                = $request->get_param( 'email' );
		$phone                = $request->get_param( 'phone' );
		$state                = $request->get_param( 'state' );
		$preferred_budget     = $request->get_param( 'preferred_budget' );
		$no_of_1_8th_interest = $request->get_param( 'no_of_1_8th_interest' );

		$do_you_need_recommendation_for_buyer_agent = $request->get_param( 'do_you_need_recommendation_for_buyer_agent' );

		$comment      = $request->get_param( 'comment' );
		$first_choice = $request->get_param( 'first_choice' );

		$user_service = new User_Service();
		$user_service->save_buyer_preference(
			$user_id,
			$first_name,
			$last_name,
			$email,
			$phone,
			$state,
			$preferred_budget,
			$no_of_1_8th_interest,
			$do_you_need_recommendation_for_buyer_agent,
			$comment,
			$first_choice
		);

		return new WP_REST_Response(
			__( 'Saved successfully.', 'hre-addon' ),
			201
		);
	}

	/**
	 * Get buyer preference
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_buyer_preference( WP_REST_Request $request ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error(
				'cant-save',
				__( 'You need to login to save your preference.', 'hre-addon' ),
			);
		}
		$user_id = $request->get_param( 'user_id' );

		// User must be a buyer.
		if ( ! User_Service::is_buyer( $user_id ) ) {
			return new WP_Error(
				'cant-save',
				__( 'User is not a buyer', 'hre-addon' ),
			);
		}

		// If current user is not admin, then only allow to get the preference of the current user.
		if ( ! current_user_can( 'manage_options' ) ) {
			$user_id = get_current_user_id();
		}

		$preference = User_Service::get_buyer_preference( $user_id );

		return new WP_REST_Response(
			$preference,
			201
		);
	}

	/**
	 * Get buyer preference
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_agent_detail( WP_REST_Request $request ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'user-not-allowed',
				__( 'You must be an admin to read agents details', 'hre-addon' ),
			);
		}
		$agent_id = $request->get_param( 'agent_id' );

		$agent_details = User_Service::get_agent_details( $agent_id );

		if ( is_wp_error( $agent_details ) ) {
			return $agent_details;
		}

		return new WP_REST_Response(
			$agent_details,
			201
		);
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$data = array();

		$args = array(
			'officiant_id'   => null,
			'ids'            => array(),
			'per_page'       => 10,
			'page'           => 1,
			'wedding_status' => 'all',
		);

		$ids            = $request->get_param( 'ids' );
		$officiant_id   = $request->get_param( 'officiant_id' );
		$per_page       = $request->get_param( 'per_page' );
		$page           = $request->get_param( 'page' );
		$wedding_status = $request->get_param( 'wedding_status' );

		$args = wp_parse_args( $args, $request->get_params() );

		$args['officiant_id'] = $officiant_id;

		if ( ! empty( $ids ) ) {
			$args['ids'] = explode( ',', $ids );
		}

		if ( ! empty( $wedding_status ) ) {
			$args['wedding_status'] = $wedding_status;
		}

		if ( ! empty( $per_page ) ) {
			$args['per_page'] = $per_page;
		}

		if ( ! empty( $page ) ) {
			$args['page'] = $page;
		}

		$wedding_collection = ( new Wedding_Collection() )
			->batch_get( $args );

		$items = $wedding_collection->get_weddings();
		foreach ( $items as $item ) {
			$itemdata = $this->prepare_item_for_response( $item, $request );
			$data[]   = $this->prepare_response_for_collection( $itemdata );
		}

		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Get a collection of items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function count_items( $request ) {
		// todo, later move this to a prepare_for_db, and use the same function as in the get_items.
		$data = array();

		$args = array(
			'officiant_id'   => null,
			'ids'            => array(),
			'per_page'       => 10,
			'page'           => 1,
			'wedding_status' => 'all',
		);

		$ids            = $request->get_param( 'ids' );
		$officiant_id   = $request->get_param( 'officiant_id' );
		$per_page       = $request->get_param( 'per_page' );
		$page           = $request->get_param( 'page' );
		$wedding_status = $request->get_param( 'wedding_status' );

		$args = wp_parse_args( $args, $request->get_params() );

		$args['officiant_id'] = $officiant_id;

		if ( ! empty( $ids ) ) {
			$args['ids'] = explode( ',', $ids );
		}

		if ( ! empty( $wedding_status ) ) {
			$args['wedding_status'] = $wedding_status;
		}

		if ( ! empty( $per_page ) ) {
			$args['per_page'] = $per_page;
		}

		if ( ! empty( $page ) ) {
			$args['page'] = $page;
		}

		$wedding_collection = new Wedding_Collection();
		$count              = $wedding_collection
			->count( $args );

		return new WP_REST_Response( $count, 200 );
	}

	/**
	 * Get one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$session_id      = $request->get_param( 'session_id' );
		$item_collection = ( new Eyp_Cart_Collection() )
			->batch_get(
				array(
					'session_id' => $session_id,
				)
			);

		if ( $item_collection instanceof Eyp_Cart_Collection ) {
			return new WP_REST_Response(
				$item_collection->to_array(),
				200
			);
		}

		if ( is_wp_error( $item_collection ) ) {
			return $item_collection;
		}

		return new WP_Error( 'cant-create', __( 'Error unknown. 8472', 'text-domain' ), array( 'status' => 500 ) );
	}


	/**
	 * Create one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function create_item( $request ) {
		$item     = $this->prepare_item_for_database( $request );
		$new_item = $item->save();
		if ( $new_item instanceof Eyp_Cart ) {
			return new WP_REST_Response(
				( new Eyp_Cart() )
					->set_id( $new_item->get_id() )
					->read_from_db()
					->to_array(),
				201
			);
		}

		if ( is_wp_error( $new_item ) ) {
			return $new_item;
		}

		return new WP_Error( 'cant-create', __( 'Error unknown. 8472', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Save user debug info.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function save_user_debug_info( $request ) {
		$user_id                 = get_current_user_id();
		$custom_current_datetime = $request->get_param( 'custom_current_datetime' );

		hre_save_user_debug_info( $user_id, $custom_current_datetime );

		return new WP_REST_Response(
			__( 'Saved successfully.', 'hre-addon' ),
			201
		);
	}


	/**
	 * Get user debug info.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_user_debug_info( $request ) {
		$user_id = get_current_user_id();

		return new WP_REST_Response(
			hre_get_user_debug_info( $user_id ),
			201
		);
	}

	/**
	 * Authenticate a user.
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function authenticate_user( $request ) {
		$user_name = $request->get_param( 'user_name' );
		$password  = $request->get_param( 'user_password' );

		$user = wp_authenticate( $user_name, $password );

		// Try to authenticate by email.
		if ( is_wp_error( $user ) ) {
			$user = wp_authenticate_email_password( null, $user_name, $password );
		}

		if ( $user instanceof WP_User ) {
			return new WP_REST_Response(
				array(
					'user_id' => $user->ID,
				),
				200
			);
		}
		if ( is_wp_error( $user ) ) {
			return new WP_Error(
				'cant-authenticate',
				__( 'Invalid username or password', 'text-domain' ),
				array( 'status' => 401 )
			);
		}

		return new WP_Error( 'cant-create', __( 'Error unknown. 8472', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Update one item from the collection
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function update_item( $request ) {
		// $eyp_cart          = new Eyp_Cart();
		// $parsed_attributes = $eyp_cart->parse_bvariation_attributes( [] );

		$item = $this->prepare_item_for_database( $request );

		// Check for required fields.
		if ( empty( $item->get_id() ) ) {
			return new WP_Error(
				'cant-update',
				__( 'The id is required to update an item.', 'eyippee-web-app' ),
				array( 'status' => 500 )
			);
		}

		$new_item = $item->save();

		if ( $new_item instanceof Eyp_Cart ) {
			$new_item = ( new Eyp_Cart() )
				->set_id( $new_item->get_id() )
				->read_from_db();

			return new WP_REST_Response(
				$new_item
					->to_array(),
				201
			);
		}

		if ( is_wp_error( $new_item ) ) {
			return $new_item;
		}

		return new WP_Error( 'cant-update', __( 'Error unknown. 8473', 'text-domain' ), array( 'status' => 500 ) );
	}

	/**
	 * Check if a given request has access to get items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function get_items_permissions_check( $request ) {
		return $this->permission_check( $request );
	}

	/**
	 * Check if a given request has access to get a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function get_item_permissions_check( $request ) {
		return true; // Since the couples can view the weddings during preview, we don't need to check for permissions.
		// return $this->get_items_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to create items
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function create_item_permissions_check( $request ) {
		return $this->permission_check( $request );
	}

	/**
	 * Check if a given request has access to update a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function update_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Check if a given request has access to delete a specific item
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 *
	 * @return WP_Error|bool
	 */
	public function delete_item_permissions_check( $request ) {
		return $this->create_item_permissions_check( $request );
	}

	/**
	 * Prepare the item for create or update operation.
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return Eyp_Cart $item
	 */
	protected function prepare_item_for_database( $request ): Eyp_Cart {
		$item = new Eyp_Cart();

		if ( isset( $request['id'] ) ) {
			$item->set_id( (int) $request['id'] );
		}

		if ( isset( $request['session_id'] ) ) {
			$item->set_session_id( sanitize_text_field( $request['session_id'] ) );
		}

		if ( isset( $request['product_id'] ) ) {
			$item->set_product_id( (int) $request['product_id'] );
		}

		if ( isset( $request['quantity'] ) ) {
			$item->set_quantity( (int) $request['quantity'] );
		}

		if ( isset( $request['add_to_quantity'] ) ) {
			if ( 'no' === $request['add_to_quantity'] ) {
				$item->set_add_to_quantity( false );
			}
		}

		if ( isset( $request['variation_attributes'] ) ) {
			$item->set_variation_attributes( $request['variation_attributes'] );
		}

		return $item;
	}

	/**
	 * Prepare the item for the REST response
	 *
	 * @param Eyp_Cart $item WordPress representation of the item.
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return mixed
	 */
	public function prepare_item_for_response( $item, $request ) {
		return $item->to_array();
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	public function get_collection_params() {
		return array(
			'page'     => array(
				'description'       => 'Current page of the collection.',
				'type'              => 'integer',
				'default'           => 1,
				'sanitize_callback' => 'absint',
			),
			'per_page' => array(
				'description'       => 'Maximum number of items to be returned in result set.',
				'type'              => 'integer',
				'default'           => 10,
				'sanitize_callback' => 'absint',
			),
			'search'   => array(
				'description'       => 'Limit results to those matching a string.',
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			),
		);
	}
}
