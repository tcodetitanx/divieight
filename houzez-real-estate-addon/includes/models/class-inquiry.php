<?php
/**
 * Inquiry Model.
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Libs;

use WP_Error;
use WP_Post;
use function HRE_Addon\mp_debug;

/**
 * Class Inquiry
 */
class Inquiry implements \JsonSerializable {

	/**
	 * The data.
	 *
	 * @var array $data
	 */
	private array $data = array(
		'id'           => 0,
		'post'         => null,
		'inquiry_data' => array(),
	);

	/**
	 * Modified Data
	 *
	 * @var array $modified_data
	 */
	private array $modified_data = array(
		'inquiry_data' => array(),
	);


	// <editor-fold desc="Actions">.

	/**
	 * Get an inquiry by id.
	 *
	 * @param int  $id            The inquiry id.
	 * @param self $empty_inquiry The empty inquiry.
	 *
	 * @return self|WP_Error The inquiry or WP_Error.
	 */
	public static function get_by_id( int $id, self $empty_inquiry ) {
		$post = get_post( $id );

		if ( ! $post instanceof WP_Post ) {
			return new WP_Error(
				'error',
				__( 'Inquiry not found.', 'hre-inquiry' ),
				mp_debug(
					array(
						'data' => $id,
					)
				)
			);
		}

		// Compare post type.
		if ( Settings::POST_TYPE_INQUIRY !== $post->post_type ) {
			return new WP_Error(
				'error',
				__( 'Inquiry not found.', 'hre-inquiry' ),
				mp_debug(
					array(
						'message'   => "Post type is not '" . Settings::POST_TYPE_INQUIRY,
						'data'      => $id,
						'post_type' => $post->post_type,
					)
				)
			);
		}

		return $empty_inquiry->read( $post );
	}

	/**
	 * Read the inquiry.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return self
	 */
	protected function read( WP_Post $post ): self {
		$inquiry_data = get_post_meta( $post->ID, Settings::PM_INQUIRY_DATA, true );
		if ( ! is_array( $inquiry_data ) ) {
			$inquiry_data = array();
		}

		$this->set_post( $post );
		$this->set_id( $post->ID );
		$this->set_inquiry_data( $inquiry_data );

		return $this;
	}

	/**
	 * Save the inquiry.
	 *
	 * @return int|WP_Error The post id or WP_Error.
	 */
	protected function save() {
		$args = array(
			'post_type'   => Settings::POST_TYPE_INQUIRY,
			'post_status' => 'publish',
			'post_title'  => 'Inquiry',
			'meta_input'  => array(),
		);

		if ( ! empty( $this->get_id() ) ) {
			$args['ID'] = $this->get_id();
		}

		if ( ! empty( $this->modified_data['inquiry_data'] ) ) {
			$args['meta_input'] = array(
				Settings::PM_INQUIRY_DATA => $this->modified_data['inquiry_data'],
			);
		}

		$post_id = wp_insert_post( $args );

		if ( is_wp_error( $post_id ) ) {
			return new WP_Error(
				'error',
				__( 'Error saving inquiry', 'hre-inquiry' ),
				mp_debug(
					array(
						'message' => $post_id->get_error_message(),
						'data'    => $post_id->get_error_data(),
					)
				)
			);
		}

		$this->modified_data = array();

		return $post_id;
	}

	/**
	 * Create a new inquiry.
	 *
	 * @param array $inquiry_data  The inquiry data.
	 * @param self  $empty_inquiry The empty query.
	 *
	 * @return self|WP_Error The inquiry or WP_Error.
	 */
	public static function create( array $inquiry_data, self $empty_inquiry ) {

		$empty_inquiry->set_inquiry_data( $inquiry_data, 'modify' );

		$id = $empty_inquiry->save();

		if ( is_wp_error( $id ) ) {
			return new WP_Error(
				'error',
				__( 'Error creating inquiry.', 'hre-inquiry' ),
				mp_debug(
					array(
						'message' => $id->get_error_message(),
						'data'    => $id->get_error_data(),
					)
				)
			);
		}

		return self::get_by_id( $id, $empty_inquiry );
	}

	// </editor-fold desc="Actions">.

	// <editor-fold desc="GETTER">.

	/**
	 * Get a property.
	 * All properties are gotten from the data array.
	 *
	 * @param string $key The key.
	 *
	 * @return mixed|null
	 */
	public function get_prop( string $key ) {

		if ( ! isset( $this->data[ $key ] ) ) {
			return null;
		}

		return apply_filters( 'hre_inquiry_get_prop_' . $key, $this->data[ $key ], $this );
	}

	/**
	 * Get details.
	 * Get inquiry details for json serialization.
	 *
	 * @return array
	 */
	public function get_details(): array {
		return array(
			'id'           => $this->get_id(),
			'created_at'   => $this->get_post()->post_date,
			'updated_at'   => $this->get_post()->post_modified,
			'inquiry_data' => $this->get_inquiry_data(),
		);
	}

	/**
	 * Get the post id.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->get_prop( 'id' );
	}

	/**
	 * Get the post.
	 *
	 * @return WP_Post|null The post.
	 */
	public function get_post(): ?WP_Post {
		return $this->get_prop( 'post' );
	}

	/**
	 * Get the inquiry data.
	 *
	 * @return array
	 */
	public function get_inquiry_data(): array {
		return $this->get_prop( 'inquiry_data' );
	}

	// </editor-fold>.

	// <editor-fold desc="SETTERS">.

	/**
	 * Set a property.
	 *
	 * @param string $key     The key.
	 * @param mixed  $value   The value.
	 * @param string $context The context. 'read' or 'modify'.
	 *
	 * @return self
	 */
	public function set_prop( string $key, $value, string $context = 'read' ): self {
		$new_value = apply_filters( 'hre_inquiry_set_prop_' . $key, $value, $this, $context );
		$new_value = apply_filters( 'hre_inquiry_set_prop_' . $key . '_' . $context, $new_value, $this );
		if ( 'read' === $context ) {
			$this->data[ $key ] = $new_value;
		} else {
			$this->modified_data[ $key ] = $new_value;
		}

		return $this;
	}

	/**
	 * Set the post id.
	 *
	 * @param int $id The post id.
	 *
	 * @return self
	 */
	public function set_id( int $id ): self {
		$this->set_prop( 'id', $id );

		return $this;
	}

	/**
	 * Set inquiry data.
	 *
	 * @param array           $inquiry_data The inquiry data.
	 * @param 'read'|'modify' $context      The context.
	 *
	 * @return self
	 */
	public function set_inquiry_data( array $inquiry_data, string $context = 'read' ): Inquiry {
		$this->set_prop( 'inquiry_data', $inquiry_data, $context );

		return $this;
	}

	/**
	 * Set post.
	 *
	 * @param WP_Post $post The post.
	 *
	 * @return self
	 */
	public function set_post( WP_Post $post ): self {
		$this->set_prop( 'post', $post );

		return $this;
	}

	// </editor-fold desc="SETTERS">.

	// <editor-fold desc="OTHERS">.

	public function jsonSerialize() {
		return $this->get_details();
	}

	// </editor-fold desc="OTHERS">.

}
