<?php
/**
 * REST controller for elementor agent form.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use ElementorPro\Modules\Forms\Submissions\Database\Query;
use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_REST_Controller;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Rest_Elementor_Agent_form.
 */
class Rest_Elementor_Agent_form extends WP_REST_Controller {

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
		$this->resource_name = 'elementor-agent-form';
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

		// approve-agent.
		// is-agent-approved
		// Get submission details
		// Save admin settings.
		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/approve-agent',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'approve_agent' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'form-submission-id' => array(
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $param ) {
							if ( $param < 0 ) {
								return new WP_Error( 'rest_invalid_param', __( 'Invalid parameter(s)', 'hre-addon' ), array( 'status' => 400 ) );
							}

							return true;
						},
					),
				),
			),
		);

		register_rest_route(
			$this->namespace,
			'/' . $this->resource_name . '/is-agent-approved',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'is_agent_approved' ),
				'permission_callback' => function ( WP_REST_Request $request ) {
					// Only allow admins to access this endpoint.
					return current_user_can( 'manage_options' );
				},
				'args'                => array(
					'form-submission-id' => array(
						'required'          => true,
						'type'              => 'number',
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => function ( $param ) {
							if ( $param < 0 ) {
								return new WP_Error( 'rest_invalid_param', __( 'Invalid parameter(s)', 'hre-addon' ), array( 'status' => 400 ) );
							}

							return true;
						},
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
	 * Check if agent is approved.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function is_agent_approved( WP_REST_Request $request ) {
		$submission_id = $request->get_param( 'form-submission-id' );
		$submission    = $this->get_submission( $submission_id );
		$email         = '';
		$username      = '';

		foreach ( $submission['convert'] as $item ) {
			if ( 'email' === $item['key'] ) {
				$email = $item['value'];
			} elseif ( 'username' === $item['key'] ) {
				$username = $item['value'];
			}
		}

		// Get user by email and make sure they have the role of Agent.
		$user = get_user_by( 'email', $email );
		if ( ! $user ) {
			$user = get_user_by( 'login', $username );

			if ( ! $user ) {
				return new WP_REST_Response(
					array(
						'approved' => false,
						'more'     => array(
							'reason'        => 'agent not found',
							'email'         => $email,
							'agent_details' => $submission['convert'],
							'user'          => $user,
						),
					),
					200
				);
			}
		}

		$roles = $user->roles;
		if ( ! in_array( 'houzez_agent', $roles, true ) ) {
			return new WP_REST_Response(
				array(
					'approved' => false,
					'more'     => array(
						'reason'        => 'not-agent',
						'agent'         => $user->roles,
						'agent_details' => $submission['convert'],
					),
				),
				200
			);
		}

		return new WP_REST_Response(
			array(
				'approved' => true,
				'more'     => array(
					'agent_details' => $submission['convert'],
				),
			),
			200
		);

	}

	/**
	 * Approve agent.
	 *
	 * @param WP_REST_Request $request The request object.
	 *
	 * @return WP_REST_Response|WP_Error
	 */
	public function approve_agent( WP_REST_Request $request ) {
		$submission_id = $request->get_param( 'form-submission-id' );
		$submission    = $this->get_submission( $submission_id );

		$first_name = '';
		$last_name  = '';
		$email      = '';
		$username   = '';
		$password   = '';
		foreach ( $submission['convert'] as $item ) {
			if ( 'first_name' === $item['key'] ) {
				$first_name = $item['value'];
			}
			if ( 'last_name' === $item['key'] ) {
				$last_name = $item['value'];
			}
			if ( 'email' === $item['key'] ) {
				$email = $item['value'];
			}
			if ( 'username' === $item['key'] ) {
				$username = $item['value'];
			}
			if ( 'password' === $item['key'] ) {
				$password = $item['value'];
			}
		}

		// Create user.
		// $user_id = wp_create_user( $username, $password, $email );
		$user_id = wp_insert_user(
			array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'role'       => 'houzez_agent',
			)
		);
		if ( is_wp_error( $user_id ) ) {
			return new WP_REST_Response(
				array(
					'error' => $user_id->get_error_message(),
				),
				200
			);
		}

		// Add user meta.
		update_user_meta( $user_id, 'first_name', $first_name );
		update_user_meta( $user_id, 'last_name', $last_name );

		return new WP_REST_Response(
			array(
				'user_id'    => $user_id,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'email'      => $email,
				'username'   => $username,
			),
			200
		);
	}

	/**
	 * Get the schema, conforming to JSON Schema.
	 *
	 * @param int $submission_id The submission id.
	 *
	 * @return array
	 */
	public function get_submission( int $submission_id ): array {
		$query      = Query::get_instance();
		$submission = null;
		if ( null !== $query ) {
			$submission = $query->get_submission( $submission_id );
		}

		return array(
			'submissions' => $submission,
			'convert'     => $this->pick_values(
				array(
					'first_name',
					'last_name',
					'email',
					'username',
					'password',
				),
				$submission
			),
		);
	}

	/**
	 * Convert.
	 *
	 * @param array $keys The labels.
	 * @param array $data The data.
	 *
	 * @return array
	 */
	public function pick_values( array $keys, array $data ): array {
		$result = array();
		$values = $data['data']['values'];

		foreach ( $keys as $key ) {
			foreach ( $values as $value ) {
				$value_key = $value['key'];
				$value     = $value['value'];
				if ( $key === $value_key ) {
					$result[] = array(
						'key'   => $value_key,
						'label' => $key,
						'value' => $value,
					);
				}
			}
		}

		return $result;
	}
}
