<?php
/**
 * Registers custom post type Buyer applications.
 *
 * @package    HRE_Addon\Includes\CPT
 */

namespace HRE_Addon\Includes\CPT;

use HRE_Addon\Initializer;
use HRE_Addon\Libs\Buyer_Application;
use HRE_Addon\Libs\Inquiry;
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
 * Class CPT_Inquiry
 */
class CPT_Inquiry {

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

		$post_type = Settings::POST_TYPE_INQUIRY;
		add_action( 'manage_' . $post_type . '_posts_columns', array( $this, 'add_custom_columns' ), 9999 );
		add_action( 'manage_' . $post_type . '_posts_custom_column', array( $this, 'render_custom_columns' ), 9999, 2 );
		// render custom title.
		add_filter( 'the_title', array( $this, 'change_title_column_value' ), 9999, 2 );


		// Display the root element.
		add_action(
			'admin_footer',
			static function () {
				?>
                <div class="hre-admin-inquiry-root"></div>
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
	public function on_init(): void {
		$this->register_custom_post_type();
	}

	/**
	 * Add custom columns to the banner post type.
	 *
	 * @param array $columns The columns.
	 *
	 * @return array
	 */
	public function add_custom_columns( array $columns ): array {
		// Add after index 1.
		return array_merge(
			array_slice( $columns, 0, 2 ),
			array(
				'email' => __( 'Email', 'hre-addon' ),
				'view'  => __( 'View', 'hre-addon' ),
				'title' => __( 'Title', 'hre-addon' ),
			),
			array_slice( $columns, 2, count( $columns ) - 1 )
		);
	}

	/**
	 * Change the title column value.
	 *
	 * @param string $title The title.
	 * @param int    $id    The id.
	 *
	 * @return string
	 */
	public function change_title_column_value( string $title, int $id ): string {
		$post = get_post( $id );
		if ( $post->post_type === Settings::POST_TYPE_INQUIRY ) {
			$type = filter_input( INPUT_GET, 'post_type', FILTER_SANITIZE_STRING );
			if ( $type === Settings::POST_TYPE_INQUIRY ) {
				return '#' . $post->ID;
			}
		}

		return $title;
	}

	/**
	 * Render the custom columns.
	 *
	 * @param array $column  The column.
	 * @param int   $post_id The post id.
	 *
	 * @return void
	 */
	public function render_custom_columns( $column, $post_id ): void {

		$args = $this->get_item_details( $post_id );
		switch ( $column ) {
			case 'email':
				echo $args['email'];
				break;
			case 'view':
				echo $args['view'];
				break;
			case 'title':
				echo $this->change_title_column_value( '', $post_id );
				break;
		}

		// Enqueue the scripts.
		do_action( 'hre_enqueue_default_admin_inquiry' );
	}

	private
	array $args = array();

	/**
	 * Get the service item details.
	 *
	 * @param int $post_id The post id.
	 *
	 * @return array
	 */
	public
	function get_item_details(
		int $post_id
	): array {
//		if ( ! empty( $this->args ) ) {
//			return $this->args;
//		}

		$inquiry    = Inquiry::get_by_id( $post_id, new Inquiry() );
		$email_html = '-';
		$view_html  = '-';

		if ( is_wp_error( $inquiry ) ) {
			return array(
				'email' => $email_html,
				'view'  => $view_html,
			);
		}

		$inquiry_data = $inquiry->get_inquiry_data();

		$email = $inquiry_data['information']['email'] ?? '';
		$user  = get_user_by( 'email', $email );
		if ( $user instanceof WP_User ) {
			$user_id    = $user->ID;
			$user_link  = get_edit_user_link( $user_id );
			$email_html = sprintf(
				'<a href="%s">%s</a>',
				$user_link,
				$email
			);
		} else {
			$email_html = $email;
		}

		$view_html = sprintf(
			'<button type="button" class="button button-primary hre-btn-view-inquiry" data-inquiry-data="%1$s">%2$s</button>',
			htmlentities( wp_json_encode( $inquiry ) ),
			__( 'View', 'hre-addon' )
		);

		$this->args = array(
			'email' => $email_html,
			'view'  => $view_html,
		);

		return $this->args;
	}

	/**
	 * Register custom post type.
	 *
	 * @return void
	 */
	public function register_custom_post_type(): void {
		$post_type = Settings::POST_TYPE_INQUIRY;
		register_post_type(
			$post_type,
			array(
				'labels'              => array(
					'name'          => __( 'Inquiry', 'hre-addon' ),
					'singular_name' => __( 'Inquiry', 'hre-addon' ),
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

		$css = mp_get_style( '/admin/admin-view-inquiry' );
		$js  = mp_get_script( '/admin/admin-view-inquiry' );

		wp_enqueue_editor();
		wp_register_style( 'hre-admin-view-inquiry', $css, array(), Initializer::$script_version );
		wp_register_script(
			'hre-admin-view-inquiry',
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
			'hre_enqueue_default_admin_inquiry',
			static function () {
				wp_enqueue_style( 'hre-admin-view-inquiry' );
				wp_enqueue_script( 'hre-admin-view-inquiry' );
			}
		);
	}

}
