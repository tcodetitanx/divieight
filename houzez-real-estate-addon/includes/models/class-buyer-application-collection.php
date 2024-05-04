<?php
/**
 * Model for Buyer application collections.
 *
 * @package namespace HRE_Addon\Includes\Model;
 */

namespace HRE_Addon\Includes\Model;

use HRE_Addon\Libs\Buyer_Application as Item;
use HRE_Addon\Libs\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class Buyer_Application_Collection.
 */
class Buyer_Application_Collection {

	/**
	 * Holds the item objects.
	 *
	 * @var Item[] $items The items objects.
	 */
	private array $items = array();

	/**
	 * Holds the arguments.
	 *
	 * @var array $args The arguments.
	 */
	private array $args = array();

	/**
	 * Get batch weddings.
	 *
	 * @param array $args The arguments.
	 *
	 * @return $this The item objects or a WP_Error object.
	 */
	public function batch_get( array $args ): self {
		$this->set_args( $args );
		$post_ids = $this->batch_get_ids( $args );

		/**
		 * The items.
		 *
		 * @var Item[] $items
		 */
		$items = array();
		foreach ( $post_ids as $post_id ) {
			$item = ( new Item() )->get( $post_id );

			if ( is_wp_error( $item ) ) {
				continue;
			}

			$items[] = $item;
		}

		// Set the items.
		$this->items = $items;

		return $this;
	}

	/**
	 * Get batch weddings with args.
	 *
	 * @return $this|\WP_Error The item objects or a WP_Error object.
	 */
	public function batch_get_with_args() {
		return $this->batch_get( $this->get_args() );
	}

	/**
	 * Get batch item ids.
	 *
	 * @param array $args The arguments.
	 *
	 * @return int[]|WP_Error The items ids or a WP_Error object.
	 */
	private function batch_get_ids() {
		$args = $this->get_args();

		$query_args = array(
			'post_type'      => Settings::POST_TYPE_BUYER_APPLICATION,
			'fields'         => 'ids',
			'posts_per_page' => $args['per_page'],
			'paged'          => $args['page'],
			'post_status'    => 'publish',
		);

		if ( ! empty( $args['ids'] ) ) {
			$query_args['post__in'] = $args['ids'];
		}

		if ( ! empty( $args['buyer_id'] ) ) {
			$query_args['author'] = $args['buyer_id'];
		}

		return ( new \WP_Query( $query_args ) )->posts;
	}

	/**
	 * Set the arguments.
	 *
	 * @param array $args The arguments.
	 *
	 * @return $this The object.
	 */
	public function set_args( array $args ): self {
		$defaults = array(
			'ids'      => array(),
			'per_page' => 10,
			'page'     => 1,
			'buyer_id' => 0,
		);

		$args = wp_parse_args( $args, $defaults );

		$this->args = $args;

		return $this;
	}

	/**
	 * Get the arguments.
	 *
	 * @return array The arguments.
	 */
	public function get_args(): array {
		return $this->args;
	}

	/**
	 * Get array from items.
	 *
	 * @return array The array.
	 */
	public function to_array(): array {
		$item_array = array();
		foreach ( $this->items as $item ) {
			$item_array[] = $item->to_array();
		}

		return $item_array;
	}

	/**
	 * Get the items.
	 *
	 * @return Item[] The weddings.
	 */
	public function get_items(): array {
		return $this->items;
	}

}
