<?php
/**
 * Buyer application Model.
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Libs;

use WP_Error;
use WP_Post;

/**
 * Class Buyer_Application
 */
class Buyer_Application {


	/**
	 * The property application id.
	 *
	 * @var int $id The property application id.
	 */
	private int $id = 0;
	/**
	 * The property id.
	 *
	 * @var int $property_id The property id.
	 */
	private int $property_id = 0;

	/**
	 * The buyer id.
	 *
	 * @var int $buyer_id The buyer id.
	 */
	private int $buyer_id = 0;

	/**
	 * The post object.
	 *
	 * @var ?WP_Post $post The post object.
	 */
	private ?WP_Post $post = null;

	/**
	 * Sign Mode
	 *
	 * @var string $sign_mode The sign mode.
	 */
	private string $sign_mode = 'simple';

	/**
	 * Agreement 1
	 *
	 * @var string $agreement_1 The agreement 1.
	 */
	private string $agreement_1 = '';

	/**
	 * Agreement 2
	 *
	 * @var string $agreement_2 The agreement 2.
	 */
	private string $agreement_2 = '';

	/**
	 * Agreement 1 inputs
	 *
	 * @var array $agreement_1_inputs The agreement 1 inputs.
	 */
	private array $agreement_1_inputs = array();

	/**
	 * Agreement 2 inputs
	 *
	 * @var array $agreement_2_inputs The agreement 2 inputs.
	 */
	private array $agreement_2_inputs = array();


	/**
	 * Get property application by id.
	 *
	 * @param int $property_application_id The property application id.
	 *
	 * @return Buyer_Application|WP_Error
	 */
	public function get( int $property_application_id ) {
		$post = get_post( $property_application_id );

		if ( ! $post ) {
			return new WP_Error(
				'invalid_id',
				'Invalid property application id',
				array(
					'status' => 404,
				)
			);
		}

		if ( Settings::POST_TYPE_BUYER_APPLICATION !== $post->post_type ) {
			return new WP_Error(
				'invalid_post_type',
				'Invalid property type',
				array(
					'status'    => 404,
					'post_type' => $post->post_type,
				)
			);
		}

		$post_id = $post->ID;

		$sign_mode = get_post_meta( $post_id, Settings::PM_IS_BUYER_APPLICATION_SIGN_MODE, true );
		if ( ! in_array( $sign_mode, array( 'simple', 'complex' ), true ) ) {
			$sign_mode = 'simple';
		}

		$agreement_1       = get_post_meta( $post_id, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_1, true );
		$agreement_2       = get_post_meta( $post_id, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_2, true );
		$agreement_input_1 = get_post_meta( $post_id, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_1_INPUTS, true );
		$agreement_input_2 = get_post_meta( $post_id, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_2_INPUTS, true );

		$this->id                 = $property_application_id;
		$this->property_id        = $post->post_parent;
		$this->buyer_id           = $post->post_author;
		$this->post               = $post;
		$this->sign_mode          = $sign_mode;
		$this->agreement_1        = $agreement_1;
		$this->agreement_2        = $agreement_2;
		$this->agreement_1_inputs = is_array( $agreement_input_1 ) ? $agreement_input_1 : array();
		$this->agreement_2_inputs = is_array( $agreement_input_2 ) ? $agreement_input_2 : array();

		return $this;
	}

	/**
	 * Get property by application id and user id.
	 *
	 * @param int $id The property application id.
	 * @param int $user_id The user id.
	 *
	 * @return Buyer_Application|WP_Error
	 */
	public function get_by_id_and_buyer_id( int $id, int $user_id ) {
		$query = new \WP_Query(
			array(
				'post_type'      => Settings::POST_TYPE_BUYER_APPLICATION,
				'posts_per_page' => 1,
				'post_status'    => 'publish',
				'post_parent'    => $id,
				'author'         => $user_id,
			)
		);

		if ( empty( $query->post ) ) {
			return new WP_Error(
				'invalid_id',
				'Invalid property application id',
				array(
					'status' => 404,
				)
			);
		}

		if ( Settings::POST_TYPE_BUYER_APPLICATION === $query->post ) {
			return new WP_Error(
				'invalid_post_type',
				'Invalid property type',
				array(
					'status' => 404,
				)
			);
		}

		$post              = $query->post;
		$this->id          = $id;
		$this->property_id = $post->post_parent;
		$this->buyer_id    = $post->post_author;
		$this->post        = $post;

		return $this;
	}

	/**
	 * Create a buyer application.
	 *
	 * @param array $args The arguments.
	 *
	 * @return self|WP_Error
	 */
	public function create( array $args ) {
		$defaults = array(
			'property_id'        => 0,
			'buyer_id'           => 0,
			'signature_url'      => '',
			'sign_mode'          => 'simple',
			'agreement_1'        => '',
			'agreement_2'        => '',
			'agreement_1_inputs' => array(),
			'agreement_2_inputs' => array(),
		);
		$args     = wp_parse_args( $args, $defaults );
		$insert   = wp_insert_post(
			array(
				'post_type'    => Settings::POST_TYPE_BUYER_APPLICATION,
				'post_title'   => 'Property ' . $args['property_id'] . ' Application',
				'post_content' => $args['signature_url'],
				'post_status'  => 'publish',
				'post_author'  => $args['buyer_id'],
				'post_parent'  => $args['property_id'],
			)
		);

		if ( is_wp_error( $insert ) ) {
			return new WP_Error(
				'insert_error',
				'Error while creating the buyer application..',
				array(
					'error' => $insert->get_error_message(),
				)
			);
		}

		update_post_meta( $insert, Settings::PM_IS_BUYER_APPLICATION_SIGN_MODE, $args['sign_mode'] );
		update_post_meta( $insert, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_1, $args['agreement_1'] );
		update_post_meta( $insert, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_2, $args['agreement_2'] );
		update_post_meta( $insert, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_1_INPUTS, $args['agreement_1_inputs'] );
		update_post_meta( $insert, Settings::PM_IS_BUYER_APPLICATION_AGREEMENT_2_INPUTS, $args['agreement_2_inputs'] );

		return $this->get( $insert );
	}

	/**
	 * Return array representation of the buyer application.
	 *
	 * @return array
	 */
	public function to_array(): array {
		$property_name = '';
		$property      = get_post( $this->property_id );
		if ( $property instanceof \WP_Post ) {
			$property_name = $property->post_title;
		}

		return array(
			'id'            => $this->get_id(),
			'property_id'   => $this->get_property_id(),
			'buyer_id'      => $this->get_buyer_id(),
			'property_name' => $property_name,
			'created'       => $this->post instanceof \WP_Post ? $this->post->post_date : '',
			'updated'       => $this->post instanceof \WP_Post ? $this->post->post_modified : '',
			'signature_url' => $this->post instanceof \WP_Post ? $this->post->post_content : '',
			'property_url'  => get_permalink( $this->get_property_id() ),
		);
	}

	/**
	 * Get the property application id.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Get the property id.
	 *
	 * @return int
	 */
	public function get_property_id(): int {
		return $this->property_id;
	}

	/**
	 * Get the buyer id.
	 *
	 * @return int
	 */
	public function get_buyer_id(): int {
		return $this->buyer_id;
	}

	/**
	 * Get the post object.
	 *
	 * @return WP_Post|null
	 */
	public function get_post(): ?WP_Post {
		return $this->post;
	}

	/**
	 * Get the sign mode.
	 *
	 * @return string
	 */
	public function get_sign_mode(): string {
		return $this->sign_mode;
	}

	/**
	 * Get the agreement 1.
	 *
	 * @return string
	 */
	public function get_agreement_1(): string {
		return $this->agreement_1;
	}

	/**
	 * Get the agreement 2.
	 *
	 * @return string
	 */
	public function get_agreement_2(): string {
		return $this->agreement_2;
	}

	/**
	 * Get the agreement 1 inputs.
	 *
	 * @return array
	 */
	public function get_agreement_1_inputs(): array {
		return $this->agreement_1_inputs;
	}

	/**
	 * Get the agreement 2 inputs.
	 *
	 * @return array
	 */
	public function get_agreement_2_inputs(): array {
		return $this->agreement_2_inputs;
	}

}
