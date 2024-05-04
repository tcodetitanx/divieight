<?php
/**
 * REST controller for buyer application features.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use HRE_Addon\Includes\helpers\Product_Helper;
use HRE_Addon\Includes\Model\Buyer_Application_Collection;
use HRE_Addon\Libs\Buyer_Application;
use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function HRE_Addon\hre_buyer_has_paid;
use function HRE_Addon\hre_get_admin_settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Buyer_Application.
 */
class Rest_Buyer_Application extends WP_REST_Controller {


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
		$this->resource_name = 'buyer-application';
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

		// Create property application.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/create-application',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_application' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return true;
				},
				'args'                => array(
					'property_id'        => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'signature_url'      => array(
						'required' => true,
						'type'     => 'string',
					),
					'agreement_1_inputs' => array(
						'required' => true,
						'type'     => 'object',
					),
					'agreement_2_inputs' => array(
						'required' => true,
						'type'     => 'object',
					),

				),
			),
		);

		// Already buyer already applied.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/has-buyer-already-applied/',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'has_buyer_already_applied' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					return true;
				},
				'args'                => array(
					'property_id' => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
				),
			),
		);

		// Can user apply ?
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/can-buyer-apply',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'can_buyer_apply' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					return true;
				},
				'args'                => array(
					'property_id' => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
				),
			),
		);

		// Batch get buyer applications.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/batch-get',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'batch_get' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					return true;
				},
				'args'                => array(
					'posts_per_page' => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
					),
					'page'           => array(
						'required'          => true,
						'type'              => 'integer',
						'sanitize_callback' => 'absint',
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
	 * Check if buyer can apply.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function can_buyer_apply( WP_REST_Request $request ) {
		$logged_in_and_paid = $this->permission_logged_in_and_paid();

		if ( is_wp_error( $logged_in_and_paid ) ) {
			return $logged_in_and_paid;
		}

		$property_id = $request->get_param( 'property_id' );
		$property    = get_post( $property_id );

		if ( ! ( $property instanceof \WP_Post ) ) {
			return new WP_Error( 'invalid_property', 'Invalid property' );
		}

		// Prevent users from applying 2 times in a day to the same property.
		$old_buyer_application = ( new Buyer_Application() )
			->get_by_id_and_buyer_id(
				$property_id,
				get_current_user_id()
			);

		if ( $old_buyer_application instanceof Buyer_Application ) {
			$today    = gmdate( 'Y-m-d' );
			$old_post = $old_buyer_application->get_post();
			if ( $old_post instanceof \WP_Post ) {
				$old_date = $old_post->post_date;
				$old_date = gmdate( 'Y-m-d', strtotime( $old_date ) );
				if ( $old_date === $today ) {
					return new WP_Error( 'already_applied', 'You have already applied to this property today' );
				}
			}
		}

		return new WP_REST_Response(
			true,
			201
		);
	}

	/**
	 * Create buyer application.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_application( WP_REST_Request $request ) {
		$logged_in_and_paid = $this->permission_logged_in_and_paid();

		if ( is_wp_error( $logged_in_and_paid ) ) {
			return $logged_in_and_paid;
		}

		$property_id        = $request->get_param( 'property_id' );
		$signature_url      = $request->get_param( 'signature_url' );
		$agreement_1_inputs = $request->get_param( 'agreement_1_inputs' );
		$agreement_2_inputs = $request->get_param( 'agreement_2_inputs' );
		$property           = get_post( $property_id );

		if ( ! ( $property instanceof \WP_Post ) ) {
			return new WP_Error( 'invalid_property', 'Invalid property' );
		}

		$sign_mode = get_post_meta( $property_id, Settings::PM_PROPERTY_SIGN_MODE, true );
		if ( ! in_array( $sign_mode, array( 'simple', 'complex' ), true ) ) {
			$sign_mode = 'simple';
		}

		if ( 'simple' === $sign_mode ) {
			if ( empty( $signature_url ) ) {
				return new WP_Error( 'invalid_signature_url', 'Invalid signature ' );
			}
		} elseif ( 'complex' === $sign_mode ) {
			if ( empty( $agreement_1_inputs ) ) {
				return new WP_Error( 'invalid_agreement_1_inputs', 'Invalid agreement 1 inputs' );
			}
			if ( empty( $agreement_2_inputs ) ) {
				return new WP_Error( 'invalid_agreement_2_inputs', 'Invalid agreement 2 inputs' );
			}
		}

		// Prevent users from applying 2 times in a day to the same property.
		$old_buyer_application = ( new Buyer_Application() )
			->get_by_id_and_buyer_id(
				$property_id,
				get_current_user_id()
			);

		if ( $old_buyer_application instanceof Buyer_Application ) {
			$today    = gmdate( 'Y-m-d' );
			$old_post = $old_buyer_application->get_post();
			if ( $old_post instanceof \WP_Post ) {
				$old_date = $old_post->post_date;
				$old_date = gmdate( 'Y-m-d', strtotime( $old_date ) );
				if ( $old_date === $today ) {
					return new WP_Error( 'already_applied', 'You have already applied to this property today' );
				}
			}
		}

		$agreement_1 = get_post_meta( $property_id, Settings::PM_PROPERTY_AGREEMENT, true );
		$agreement_2 = get_post_meta( $property_id, Settings::PM_PROPERTY_AGREEMENT_2, true );

		$buyer_application = ( new Buyer_Application() )
			->create(
				array(
					'property_id'        => $property_id,
					'buyer_id'           => get_current_user_id(),
					'signature_url'      => $signature_url,
					'sign_mode'          => $sign_mode,
					'agreement_1'        => $agreement_1,
					'agreement_2'        => $agreement_2,
					'agreement_1_inputs' => $agreement_1_inputs,
					'agreement_2_inputs' => $agreement_2_inputs,
				)
			);

		if ( is_wp_error( $buyer_application ) ) {
			return $buyer_application;
		}

		return new WP_REST_Response(
			$buyer_application->to_array(),
			201
		);
	}

	/**
	 * Has buyer already applied?
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function has_buyer_already_applied( WP_REST_Request $request ) {
		$logged_in_and_paid = $this->permission_logged_in_and_paid();

		if ( is_wp_error( $logged_in_and_paid ) ) {
			return $logged_in_and_paid;
		}

		$property_id = $request->get_param( 'property_id' );
		$property    = get_post( $property_id );

		if ( ! ( $property instanceof \WP_Post ) ) {
			return new WP_Error( 'invalid_property', 'Invalid property' );
		}

		$old_buyer_application = ( new Buyer_Application() )
			->get_by_id_and_buyer_id(
				$property_id,
				get_current_user_id()
			);

		if ( is_wp_error( $old_buyer_application ) ) {
			return $old_buyer_application;
		}

		return new WP_REST_Response(
			$old_buyer_application->to_array(),
			201
		);
	}

	/**
	 * Batch get buyer applications.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function batch_get( WP_REST_Request $request ) {

		$logged_in_and_paid = $this->permission_logged_in_and_paid();
		if ( is_wp_error( $logged_in_and_paid ) ) {
			return $logged_in_and_paid;
		}

		$user_id = get_current_user_id();

		$page     = $request->get_param( 'page' );
		$per_page = $request->get_param( 'posts_per_page' );

		$buyer_application_collection = ( new Buyer_Application_Collection() )
			->batch_get(
				array(
					'posts_per_page' => $per_page,
					'paged'          => $page,
					'buyer_id'       => $user_id,
				)
			);

		return new WP_REST_Response(
			$buyer_application_collection->to_array(),
			201
		);
	}

	/**
	 * Validate that the user is logged in and has paid.
	 *
	 * @return true|WP_Error True if the user is logged in and has paid, WP_Error otherwise.
	 */
	private function permission_logged_in_and_paid() {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'not_logged_in', 'You are not logged in. Please login as a buyer to apply' );
		}

		$user_has_paid = hre_buyer_has_paid( get_current_user_id() );
		if ( ! $user_has_paid ) {
			return new WP_Error( 'already_paid', 'You have NOT paid for the buyer application. Please go to the "Become a buyer" page.' );
		}

		return true;
	}


}
