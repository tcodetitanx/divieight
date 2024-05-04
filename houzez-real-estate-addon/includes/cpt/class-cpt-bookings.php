<?php
	/**
	 * Registers custom post type Bookings.
	 *
	 * @package    HRE_Addon\Includes\CPT
	 */

	namespace HRE_Addon\Includes\CPT;

	use HRE_Addon\Includes\Model\Booking;
	use HRE_Addon\Initializer;
	use HRE_Addon\Libs\Settings;
	use WP_Query;
	use WP_User;
	use function HRE_Addon\mp_get_script;

	if ( ! defined( 'ABSPATH' ) ) {
		exit(); // Exit if accessed directly.
	}

	/**
	 * Class CPT_Bookings
	 */
	class CPT_Bookings {

		/**
		 * constructor.
		 */
		public function __construct() {
		}

		/**
		 * Initialize this class.
		 *
		 * @return $this
		 */
		public function init() {
			add_action( 'init', array( $this, 'on_init' ) );
//		add_action( 'init', array( $this, 'add_custom_columns' ) );
//
//		// Add filters.
//		add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_by_user' ) );
//		add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_by_service_type' ) );
//		add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_status' ) );
//
//		add_filter( 'pre_get_posts', array( $this, 'filter_by_custom_meta' ), 1000 );

			$post_type = Settings::POST_TYPE_BOOKING;
			add_action( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_custom_columns' ) );
			add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );

			return $this;
		}

		/**
		 * Run on init.
		 *
		 * @return void
		 */
		public function on_init() {
			$this->register_custom_post_type();
		}

		/**
		 * Register the custom filter
		 *
		 * @return void
		 */
		public function add_custom_filter_service_item_status() {
			global $typenow;
			if ( Settings::POST_TYPE_SERVICE_ITEM === $typenow ) {

				$statuses = array(
					'' => 'By Status',
				);
				foreach ( Settings::get_service_item_statuses() as $status ) {
					$statuses[ $status ] = str_replace( '_', ' ', ucfirst( $status ) );
				}

				$filter  = filter_input( INPUT_GET, 'filter_by_status', FILTER_SANITIZE_STRING );
				$value   = $filter ?? '';
				$options = $statuses;
				echo '<select name="filter_by_status">';
				foreach ( $options as $option_value => $option_label ) {
					$selected = $value === $option_value ? 'selected' : '';
					//@codingStandardsIgnoreLine
					echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
				}
				echo '</select>';
			}
		}

		/**
		 * Register the custom filter
		 *
		 * @return void
		 */
		public function add_custom_filter_service_item_by_user() {

			global $typenow;
			if ( Settings::POST_TYPE_SERVICE_ITEM === $typenow ) {

				$users         = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
				$filter_detail = array(
					'' => 'By User',
				);
				foreach ( $users as $user ) {
					$filter_detail[ $user->ID ] = str_replace( '_', ' ', ucfirst( $user->display_name ) );
				}

				$filter  = (int) filter_input( INPUT_GET, 'filter_by_user', FILTER_SANITIZE_STRING );
				$value   = $filter ?? '';
				$options = $filter_detail;
				echo '<select name="filter_by_user">';
				foreach ( $options as $option_value => $option_label ) {
					$selected = $value === $option_value ? 'selected' : '';
					//@codingStandardsIgnoreLine
					echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
				}
				echo '</select>';
			}
		}

		/**
		 * Register the custom filter
		 *
		 * @return void
		 */
		public function add_custom_filter_service_item_by_service_type() {

			global $typenow;
			if ( Settings::POST_TYPE_SERVICE_ITEM === $typenow ) {

				$service_types = Settings::get_service_item_types();
				$filter_detail = array(
					'' => 'By Service Type',
				);
				foreach ( $service_types as $type ) {
					$filter_detail[ $type ] = str_replace( '_', ' ', ucfirst( $type ) );
				}

				$filter  = filter_input( INPUT_GET, 'filter_by_service_type', FILTER_SANITIZE_STRING );
				$value   = $filter ?? '';
				$options = $filter_detail;
				echo '<select name="filter_by_service_type">';
				foreach ( $options as $option_value => $option_label ) {
					$selected = $value === $option_value ? 'selected' : '';
					//@codingStandardsIgnoreLine
					echo '<option value="' . $option_value . '" ' . $selected . '>' . $option_label . '</option>';
				}
				echo '</select>';
			}
		}

		/**
		 * Register the custom post type.
		 *
		 * @param  Wp_Query  $query  the query.
		 *
		 * @return void
		 */
		public function filter_by_custom_meta( $query ) {
			global $pagenow;
			$post_type = Settings::POST_TYPE_SERVICE_ITEM;


			$meta_query = $query->get( 'meta_query' );
			if ( 'edit.php' === $pagenow && $post_type === $query->query['post_type'] ) {

				$filter_by_service_type = filter_input( INPUT_GET, 'filter_by_service_type', FILTER_SANITIZE_STRING );
				$filter_by_user         = filter_input( INPUT_GET, 'filter_by_user', FILTER_SANITIZE_STRING );
				$filter_by_status       = filter_input( INPUT_GET, 'filter_by_status', FILTER_SANITIZE_STRING );

				if ( ! is_array( $meta_query ) ) {
					$meta_query = array();
				}

				if ( ! empty( $filter_by_service_type ) ) {
					$meta_query[] = array(
						'key'     => Settings::PM_EYP_SERVICE_TYPE,
						'value'   => $filter_by_service_type,
						'compare' => '=',
					);
				}

				if ( ! empty( $filter_by_user ) ) {
					$meta_query[] = array(
						'key'     => Settings::PM_EYP_SERVICE_ITEM_USER_ID,
						'value'   => $filter_by_user,
						'compare' => '=',
					);
				}

				if ( ! empty( $filter_by_status ) ) {
					$meta_query[] = array(
						'key'     => Settings::PM_EYP_SERVICE_ITEM_STATUS,
						'value'   => $filter_by_status,
						'compare' => '=',
					);
				}

				if ( ! empty( $meta_query ) ) {
					$meta_query['relation'] = 'AND';
				}

				$query->set( 'meta_query', $meta_query );
			}

		}

		public function _filter_by_custom_meta( $query ) {
			global $pagenow;
			//		$post_type = Settings::POST_TYPE_JOB_OFFER;
			//		$filter    = filter_input( INPUT_GET, 'filter', FILTER_SANITIZE_STRING );

			//		$meta_query = $query->get( 'meta_query' );
			//		if ( 'edit.php' === $pagenow && $post_type === $query->query['post_type'] ) {
			//			if ( ! is_array( $meta_query ) ) {
			//				$meta_query = array();
			//			}
			//
			//			if ( empty( $filter ) ) {
			//				$meta_query['relation'] = 'AND';
			//				$meta_query[]           = array(
			//					'relation' => 'OR',
			//					array(
			//						'key'     => Settings::PM_WEDDING_STATUS,
			//						'value'   => Settings::CONST_WEDDING_STATUS_ACTIVE,
			//						'compare' => '=',
			//					),
			//					// Active if it does not exist.
			//					array(
			//						'key'     => Settings::PM_WEDDING_STATUS,
			//						'compare' => 'NOT EXISTS',
			//					),
			//				);
			//			} else {
			//				$meta_query[] =
			//					array(
			//						'key'     => Settings::PM_WEDDING_STATUS,
			//						'value'   => Settings::CONST_WEDDING_STATUS_ARCHIVED,
			//						'compare' => '=',
			//					);
			//			}
			//			$query->set( 'meta_query', $meta_query );
			//		}

		}

		/**
		 * Add custom columns to the banner post type.
		 */
		public function add_custom_columns( array $columns ) {
			// remove taxonomy-hre_service
			unset( $columns['taxonomy-hre_service'] );
			// Add after index 1;
			return array_slice( $columns, 0, 2, true ) +
			       array(
				       'user'   => __( 'User', 'hre-addon'),
				       'status' => __( 'Status', 'hre-addon' ),
				       'booking_services' => __( 'Services', 'hre-addon' ),
			       ) +
			       array_slice( $columns, 2, count( $columns ) - 1, true );
		}

		/**
		 * Change the title column value.
		 *
		 * @param  string  $title  The title.
		 * @param  int     $id     The id.
		 *
		 * @return string
		 */
		public function change_title_column_value( string $title, int $id ) : string {
			$post = get_post( $id );
			if ( $post->post_type === Settings::POST_TYPE_SERVICE_ITEM ) {
				$args = $this->get_service_item_details( $id );

				return $args['title'];
			}


			return $post->post_title;
		}

		/**
		 * Set the custom columns.
		 *
		 * @param  array  $columns  The columns.
		 *
		 * @return array
		 */
		public function set_custom_columns( array $columns ) : array {
			$count = 0;
			foreach ( $columns as $key => $value ) {
				if ( 2 === $count ) {
					$new_columns['user']          = __( 'Image', 'hre-addon' );
					$new_columns['added_by']      = __( 'Added By', 'hre-addon' );
					$new_columns['type']          = __( 'Type', 'hre-addon' );
					$new_columns['products']      = __( 'Products', 'hre-addon' );
					$new_columns['status']        = __( 'Status', 'hre-addon' );
					$new_columns['order']         = __( 'Order', 'hre-addon' );
					$new_columns['created_date']  = __( 'Created Date', 'hre-addon' );
					$new_columns['approved_date'] = __( 'Approved Date', 'hre-addon' );
				}
				$new_columns[ $key ] = $value;

				$count ++;
			}

			return $new_columns;
		}

		/**
		 * Render the custom columns.
		 *
		 * @param  array  $column   The column.
		 * @param  int    $post_id  The post id.
		 *
		 * @return void
		 */
		public function render_custom_columns( $column, $post_id ) {

			$args = $this->get_service_item_details( $post_id );

			switch ( $column ) {
				case 'user':
					echo wp_kses_post( $args['user'] );
					break;
				case 'booking_services':
					echo $args['booking_services'];
					break;
				case 'status':
					echo $args['status'];
					break;
			}
		}

		private $args = array();

		public function get_service_item_details( int $post_id ) {
			if ( ! empty( $this->args ) ) {
				return $this->args;
			}

			$args    = array(
				'user'             => 'Service Item',
				'services'         => '-',
				'status'           => '-',
				'booking_services' => '-',
			);
			$booking = ( new Booking() )
				->set_id( $post_id )
				->read( $post_id );

			if ( $booking instanceof Booking ) {
				$user_id = $booking->get_user_id();
				$user    = get_user_by( 'id', $user_id );
				if ( $user instanceof WP_User ) {
					$user_edit_link = get_edit_user_link( $user_id );
					$username       = $user->data->user_login . ' (' . $user->data->user_email . ')';
					$args['user']   = sprintf(
						'<a href="%1$s" target="_blank">%2$s</a>',
						esc_url( $user_edit_link ),
						esc_html( $username )
					);
				}

				$status         = $booking->get_status();
				$args['status'] = $this->get_html_booking_status( $status );

				$services_count           = count( $booking->get_booking_services() );
				$args['booking_services'] = sprintf(
				// translators: %1$s is the number of services.
					_nx(
						'%1$s Service',
						'%1$s Services',
						$services_count,
						'Number of services',
						'hre-addon'
					),
					$services_count
				);

				return $args;
			}
		}

		/**
		 * Get the html for the booking status.
		 *
		 * @param  string  $status  The status.
		 *
		 * @return string
		 */
		private function get_html_booking_status( string $status ) : string {
			$css = array(
				'text-align'     => 'center',
				'border-radius'  => '5px',
				'padding'        => '5px 10px',
				'font-weight'    => 'bold',
				'font-size'      => '12px',
				'text-transform' => 'uppercase',
			);

			switch ( $status ) {
				case Settings::BOOKING_STATUS_PENDING:
					$css['background-color'] = "#f1efc7";
					break;
				case Settings::BOOKING_STATUS_HANDLED:
					$css['background-color'] = "#c7e9f1";
					break;
				case Settings::BOOKING_STATUS_COMPLETE:
					$css['background-color'] = "#c8f1c7";
					break;
			}

			// convert css array to style string
			$styles = '';
			foreach ( $css as $key => $value ) {
				$styles .= $key . ':' . $value . ';';
			}

			return sprintf(
				'<span style="%1$s">%2$s</span>',
				esc_attr( $styles ),
				esc_html( $status )
			);
		}

		/**
		 * Register custom post type.
		 *
		 * @return void
		 */
		public function register_custom_post_type() {
			$post_type = Settings::POST_TYPE_BOOKING;
			register_post_type(
				$post_type,
				array(
					'labels'              => array(
						'name'          => __( 'Bookings', 'hre-addon' ),
						'singular_name' => __( 'Bookings', 'hre-addon' ),
					),
					'public'              => false,
					'has_archive'         => false,
					'show_in_rest'        => true,
					'supports'            => array( 'title' ),
					'exclude_from_search' => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
//					'show_in_menu'        => 'hre-addon',
				)
			);
		}


	}
