<?php
/**
 * Registers custom post type Buyer applications.
 *
 * @package    HRE_Addon\Includes\CPT
 */

namespace HRE_Addon\Includes\CPT;

use HRE_Addon\Initializer;
use HRE_Addon\Libs\Buyer_Application;
use HRE_Addon\Libs\Settings;
use WP_Post;
use WP_Query;
use WP_User;
use function HRE_Addon\mp_get_script;
use function HRE_Addon\mp_get_style;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class CPT_Buyer_Applications
 */
class CPT_Buyer_Applications {


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
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'init', array( $this, 'on_init' ) );
		// add_action( 'init', array( $this, 'add_custom_columns' ) );
		//
		// Add filters.
		// add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_by_user' ) );
		// add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_by_service_type' ) );
		// add_action( 'restrict_manage_posts', array( $this, 'add_custom_filter_service_item_status' ) );
		//
		// add_filter( 'pre_get_posts', array( $this, 'filter_by_custom_meta' ), 1000 );

		$post_type = Settings::POST_TYPE_BUYER_APPLICATION;
		add_action( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_custom_columns' ) );
		add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'render_custom_columns' ), 10, 2 );


		// Display the root element.
		add_action(
			'admin_footer',
			static function () {
				?>
                <div class="hre-admin-agreement-form-root"></div>
				<?php
			}
		);
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
	 * @param Wp_Query $query the query.
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
		// $post_type = Settings::POST_TYPE_JOB_OFFER;
		// $filter    = filter_input( INPUT_GET, 'filter', FILTER_SANITIZE_STRING );

		// $meta_query = $query->get( 'meta_query' );
		// if ( 'edit.php' === $pagenow && $post_type === $query->query['post_type'] ) {
		// if ( ! is_array( $meta_query ) ) {
		// $meta_query = array();
		// }
		//
		// if ( empty( $filter ) ) {
		// $meta_query['relation'] = 'AND';
		// $meta_query[]           = array(
		// 'relation' => 'OR',
		// array(
		// 'key'     => Settings::PM_WEDDING_STATUS,
		// 'value'   => Settings::CONST_WEDDING_STATUS_ACTIVE,
		// 'compare' => '=',
		// ),
		// Active if it does not exist.
		// array(
		// 'key'     => Settings::PM_WEDDING_STATUS,
		// 'compare' => 'NOT EXISTS',
		// ),
		// );
		// } else {
		// $meta_query[] =
		// array(
		// 'key'     => Settings::PM_WEDDING_STATUS,
		// 'value'   => Settings::CONST_WEDDING_STATUS_ARCHIVED,
		// 'compare' => '=',
		// );
		// }
		// $query->set( 'meta_query', $meta_query );
		// }
	}

	/**
	 * Add custom columns to the banner post type.
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function add_custom_columns( array $columns ): array {
		// remove taxonomy-hre_service.
		unset( $columns['taxonomy-hre_service'] );

		// Add after index 1.
		return array(
			'property'       => __( 'Property', 'hre-addon' ),
			'buyer'          => __( 'Buyer', 'hre-addon' ),
			'sign_mode'      => __( 'Sign Mode', 'hre-addon' ),
			'signature'      => __( 'Signature', 'hre-addon' ),
			'agreement_form' => __( 'Agreement Form', 'hre-addon' ),
			'time_applied'   => __( 'Time Applied', 'hre-addon' ),
		);
	}

	/**
	 * Change the title column value.
	 *
	 * @param string $title The title.
	 * @param int    $id The id.
	 *
	 * @return string
	 */
	public function change_title_column_value( string $title, int $id ): string {
		$post = get_post( $id );
		if ( $post->post_type === Settings::POST_TYPE_SERVICE_ITEM ) {
			$args = $this->get_service_item_details( $id );

			return $args['title'];
		}

		return $post->post_title;
	}

	/**
	 * Render the custom columns.
	 *
	 * @param array $column The column.
	 * @param int   $post_id The post id.
	 *
	 * @return void
	 */
	public function render_custom_columns( $column, $post_id ) {

		$args = $this->get_service_item_details( $post_id );
		switch ( $column ) {
			case 'property':
				echo $args['property'];
				break;
			case 'buyer':
				echo $args['buyer'];
				break;
			case 'signature':
				echo $args['signature'];
				break;
			case 'sign_mode':
				echo $args['sign_mode'];
				break;
			case 'agreement_form':
				echo $args['agreement_form'];
				break;
			case 'time_applied':
				echo $args['time_applied'];
				break;

		}

		// Enqueue the scripts.
		do_action( 'hre_enqueue_default_admin_view_agreement_form' );


	}

	private array $args = array();

	/**
	 * Get the service item details.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array
	 */
	public function get_service_item_details( int $post_id ): array {
		if ( ! empty( $this->args ) ) {
			return $this->args;
		}

		// return array(
		// 'property'     => '-',
		// 'buyer'        => '-',
		// 'signature'    => '-',
		// 'time_applied' => '-',
		// );

		$buyer_application = ( new Buyer_Application() )->get( $post_id );
		$property          = '-';
		$buyer             = '-';
		$signature         = '-';
		$time_applied      = '-';
		$signed_mode       = '-';
		$agreement_form    = '-';

		if ( $buyer_application instanceof Buyer_Application ) {
			$property_link = get_permalink( $buyer_application->get_property_id() );
			$property_post = get_post( $buyer_application->get_property_id() );
			$signed_mode   = $buyer_application->get_sign_mode();
			if ( $property_post instanceof WP_Post ) {
				$property = sprintf(
					'<a style="font-weight: 700;" href="%s">%s</a>',
					$property_link,
					$property_post->post_title
				);
			}

			$buyer_link = get_edit_user_link( $buyer_application->get_buyer_id() );
			$user       = get_user_by( 'id', $buyer_application->get_buyer_id() );
			$buyer      = sprintf(
				'<a href="%s">%s</a>',
				$buyer_link,
				$user instanceof WP_User ? $user->user_email : ''
			);

			if ( 'simple' === $signed_mode ) {
				$signature_base64 = $buyer_application->get_post()->post_content;
				$signature        = sprintf(
					'<img src="%s" alt="signature" style="width: 100px; height: 100px;" />',
					$signature_base64
				);
			} elseif ( 'complex' === $signed_mode ) {
				$agreement_form = sprintf(
					'<div class="hre-wrapper-agreement-form"  >
						<div class="hre-agreement-1" data-inputs="%3$s" %6$s >%1$s</div>
						<div class="hre-agreement-2" data-inputs="%4$s" %6$s>%2$s</div>
						<button type="button" class="button button-primary hre-btn-view-agreement-form">%5$s</button>
					</div>',
					$buyer_application->get_agreement_1(),
					$buyer_application->get_agreement_2(),
					htmlentities( wp_json_encode( $buyer_application->get_agreement_1_inputs() ) ),
					htmlentities( wp_json_encode( $buyer_application->get_agreement_2_inputs() ) ),
					__( 'View', 'hre-addon' ),
					'style="display: none;"'
				);
			}

			$time         = $buyer_application->get_post() instanceof WP_Post ? $buyer_application->get_post()->post_date : '';
			$time_ago     = human_time_diff( strtotime( $time ), current_time( 'timestamp' ) ) . ' ago';
			$time_applied = sprintf(
				'<span >%s</span><br/><span style="font-size: 10px;">%s</span>',
				$time,
				$time_ago
			);
		}

		return array(
			'property'       => $property,
			'buyer'          => $buyer,
			'signature'      => $signature,
			'time_applied'   => $time_applied,
			'sign_mode'      => $signed_mode,
			'agreement_form' => $agreement_form,
		);
	}

	/**
	 * Register custom post type.
	 *
	 * @return void
	 */
	public function register_custom_post_type(): void {
		$post_type = Settings::POST_TYPE_BUYER_APPLICATION;
		register_post_type(
			$post_type,
			array(
				'labels'              => array(
					'name'          => __( 'Buyer Applications', 'hre-addon' ),
					'singular_name' => __( 'Buyer Applications', 'hre-addon' ),
				),
				'public'              => false,
				'has_archive'         => false,
				'show_in_rest'        => true,
				'supports'            => array( 'title' ),
				'exclude_from_search' => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				// 'show_in_menu'        => 'hre-addon',
				'capability_type'     => 'post',
				'capabilities'        => array(
					'create_posts' => false,
					// Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
				),
				'map_meta_cap'        => true, // Set to `false`, if users are not allowed to edit/delete existing posts
			)
		);
	}

	public function register_scripts(): void {

		$css = mp_get_style( '/admin/admin-view-agreement-form' );
		$js  = mp_get_script( '/admin/admin-view-agreement-form' );

		wp_enqueue_editor();
		wp_register_style( 'hre-admin-view-agreement-form', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-admin-view-agreement-form',
			$js,
			array( 'jquery', 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ),
			Initializer::$script_version,
			true
		);

		/**
		 * Enqueue the admin settings scripts.
		 *
		 * @since 1.0.0
		 */
		add_action(
			'hre_enqueue_default_admin_view_agreement_form',
			function () {
				wp_enqueue_style( 'hre-admin-view-agreement-form' );
				wp_enqueue_script( 'hre-admin-view-agreement-form' );
			}
		);
	}

}
