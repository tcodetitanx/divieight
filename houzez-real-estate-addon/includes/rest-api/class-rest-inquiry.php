<?php
/**
 * REST controller for Inquiry form.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use HRE_Addon\Includes\helpers\Product_Helper;
use HRE_Addon\Libs\Inquiry;
use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use function HRE_Addon\hre_get_admin_settings;
use function HRE_Addon\hre_get_area_options;
use function HRE_Addon\hre_get_cities;
use function HRE_Addon\hre_get_property_type_options;
use function HRE_Addon\hre_get_us_states;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Admin_Settings.
 */
class Rest_Inquiry extends WP_REST_Controller {

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
		$this->resource_name = 'inquiry';
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

		// Create Inquiry.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/create-inquiry',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_inquiry' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Allow anyone to create an inquiry.
					return true;
				},
				'args'                => array(
					'inquiry_type'    => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'Info on Exclusivity' ),
					),
					'information'     => array(
						'required'   => true,
						'type'       => 'object',
						'properties' => array(
							'i_am'       => array(
								'required'          => true,
								'type'              => 'string',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_string( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'first_name' => array(
								'required'          => true,
								'type'              => 'string',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_string( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'last_name'  => array(
								'required'          => true,
								'type'              => 'string',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_string( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'email'      => array(
								'required'          => true,
								'type'              => 'string',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_email( $param );
								},
								'sanitize_callback' => 'sanitize_email',
							),
						),
					),
					'location'        => array(
						'required'   => true,
						'type'       => 'object',
						'properties' => array(
							'country'  => array(
								'required' => true,
								'type'     => 'string',
								'enum'     => array(
									'United States of America',
								),
							),
							'state'    => array(
								'required' => true,
								'type'     => 'string',
								'enum'     => hre_get_us_states(),
							),
							'city'     => array(
								'required' => false,
								'type'     => 'string',
								'enum'     => hre_get_cities(),
							),
							'area'     => array(
								'required' => false,
								'type'     => 'string',
								'enum'     => hre_get_area_options(),
							),
							'zip_code' => array(
								'required'          => true,
								'type'              => 'string',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_string( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
					'property'        => array(
						'required'   => true,
						'type'       => 'object',
						'properties' => array(
							'type'                => array(
								'required' => true,
								'type'     => 'string',
								'enum'     => hre_get_property_type_options(),
							),
							'max_price'           => array(
								'required'          => false,
								'type'              => 'integer',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'min_size'            => array(
								'required'          => false,
								'type'              => 'integer',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'number_of_bedrooms'  => array(
								'required'          => false,
								'type'              => 'integer',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
							'number_of_bathrooms' => array(
								'required'          => false,
								'type'              => 'integer',
								'validate_callback' => function ( $param, $request, $key ) {
									return is_numeric( $param );
								},
								'sanitize_callback' => 'sanitize_text_field',
							),
						),
					),
					'message'         => array(
						'required'          => false,
						'type'              => 'string',
						'sanitize_callback' => 'sanitize_text_field',
					),
					'agreed_to_terms' => array(
						'required' => true,
						'type'     => 'string',
						'enum'     => array( 'yes', 'no' )
					),
				),
			)
		);

		// Get Inquiry.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_inquiry' ),
				'permission_callback' => function () {
					return true;
				},
				'args'                => array(),
			)
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
	 * Create inquiry.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_inquiry( WP_REST_Request $request ) {

		$inquiry_data = array(
			'inquiry_type'    => $request->get_param( 'inquiry_type' ),
			'information'     => array(
				'i_am'       => $request->get_param( 'information' )['i_am'],
				'first_name' => $request->get_param( 'information' )['first_name'],
				'last_name'  => $request->get_param( 'information' )['last_name'],
				'email'      => $request->get_param( 'information' )['email'],
			),
			'location'        => array(
				'country'  => $request->get_param( 'location' )['country'],
				'state'    => $request->get_param( 'location' )['state'],
				'city'     => $request->get_param( 'location' )['city'],
				'area'     => $request->get_param( 'location' )['area'],
				'zip_code' => $request->get_param( 'location' )['zip_code'],
			),
			'property'        => array(
				'type'                => $request->get_param( 'property' )['type'],
				'max_price'           => $request->get_param( 'property' )['max_price'],
				'min_size'            => $request->get_param( 'property' )['min_size'],
				'number_of_bedrooms'  => $request->get_param( 'property' )['number_of_bedrooms'],
				'number_of_bathrooms' => $request->get_param( 'property' )['number_of_bathrooms'],
			),
			'message'         => $request->get_param( 'message' ),
			'agreed_to_terms' => $request->get_param( 'agreed_to_terms' ),
		);

		$inquiry = Inquiry::create(
			$inquiry_data,
			new Inquiry()
		);

		if ( is_wp_error( $inquiry ) ) {
			return $inquiry;
		}

		return new WP_REST_Response(
			$inquiry,
			200
		);
	}

	/**
	 * Get inquiry.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_inquiry( WP_REST_Request $request ) {
		$id = $request->get_param( 'id' );

		$inquiry = Inquiry::get_by_id( $id, new Inquiry() );

		if ( is_wp_error( $inquiry ) ) {
			return $inquiry;
		}

		return new WP_REST_Response(
			$inquiry,
			200
		);
	}


}

