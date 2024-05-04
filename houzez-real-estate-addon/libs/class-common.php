<?php
/**
 *  Plugin. Common functions.
 *
 * @class   Common
 * @package HRE_Addon/Includes/Libs
 */

namespace HRE_Addon\Libs;

use function HRE_Addon\hre_get_user_debug_info;

defined( 'ABSPATH' ) || exit;

/**
 * Common Processing class
 */
class Common {

	/**
	 * Holds the footer scripts.
	 *
	 * @var array $hold_footer_script
	 */
	public static $hold_footer_script = array();
	/**
	 * Holds the plugin's name.
	 *
	 * @var string
	 */
	public static $plugin_name = '';
	/**
	 * The data key.
	 *
	 * @var string $data
	 */
	public static $data = 'data';
	/**
	 * The message key.
	 *
	 * @var string $message
	 */
	public static $message = 'message';
	/**
	 * The status key.
	 *
	 * @var string $status
	 */
	public static $status = 'status';
	/**
	 * The status error key.
	 *
	 * @var string $status_error
	 */
	public static $status_error = '1';
	/**
	 * The only instance of the class.
	 *
	 * @var null|self $instance
	 */
	private static $instance = null;

	/**
	 * Constructor.
	 */
	private function __construct() {

	}

	/**
	 * GetContents Obstart & Loads file & Obclean
	 *
	 * @param string $filename The file name.
	 *
	 * @return string file content
	 */
	public static function get_contents( $filename ) {
		ob_start();
		require $filename;

		return ob_get_clean();
	}

	/**
	 * Return the only instance of this class.
	 *
	 * @return static
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new Common();
		}

		return self::$instance;
	}

	/**
	 * Add to the console or output during ajax.
	 *
	 * @param array $data_to_assign The data to assign.
	 * @param bool $ignore_and_return Ignore and return.
	 *
	 * @return void
	 */
	public static function in_script_or_send_error( $data_to_assign, $ignore_and_return = false ) {
		if ( $ignore_and_return ) {
			return;
		}
		$var_name = 'mppr_' . wp_rand( 443, 4857839 );
		$script   = '<script>';

		$script .= ' var ' . $var_name . ' = ' . wp_json_encode( $data_to_assign ) . '; ';
		$script .= ' console.log(' . $var_name . '); ';
		$script .= '</script>';
		echo $script; // phpcs:ignore

		return;
		if ( ! wp_doing_ajax() ) {
			// echo $script; // phpcs:ignore
			wp_add_inline_script( 'jquery', $script );
		} else {
			self::send_out_static( self::$status_error, $data_to_assign, '' ); // phpcs:ignore
		}
	}

	/**
	 * Whether in wp rest api.
	 *
	 * @return bool
	 */
	public static function is_rest() {
		return ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_WP_NONCE'] ) ), 'wp_rest' ) );
	}

	/**
	 * Add the script to the console.
	 *
	 * @param array $data_to_assign The data to assign.
	 * @param string $var_name The variable name.
	 *
	 * @return void
	 */
	public static function add_script( $data_to_assign, $var_name ) {
		if ( wp_doing_ajax() || self::is_rest() ) {
			return;
		}
		$json = wp_json_encode( $data_to_assign );

		?>
        <script>var <?php echo esc_html( $var_name ); ?> = <?php echo wp_kses( $json, 'post' ); // phpcs:ignore ?>;</script>
		<?php
	}

	/**
	 * Add the script to the console.
	 *
	 * @param mixed $status The status.
	 * @param array $data The data.
	 * @param string $message The message.
	 * @param array $extra The extra.
	 * @param array $use The use.
	 *
	 * @return void
	 */
	public static function send_out_static( $status, $data, $message, array $extra = array(), array $use = array() ) {
		$args = array();

		$args[ self::$status ]                         = $status;
		$args[ self::$data ]                           = $data;
		$args[ self::$message ]                        = $message;
		$args[ 'ghi30' . wp_rand( 20, 483 ) . time() ] = $extra;
		$args[ 'use_' . wp_rand( 20, 764 ) . time() ]  = $use;
		wp_send_json( $args );
		die;
	}

	/**
	 * Get datetime considering custom time set.
	 *
	 * @param int $days The days to add or subtract.
	 * @param int $hours The hours to add or subtract.
	 *
	 * @return string
	 */
	public static function get_date_time( int $days = 0, int $hours = 0 ): string {
		$user_debug_info = hre_get_user_debug_info( get_current_user_id() );
		$date            = gmdate( 'Y-m-d H:i:s' );
		if ( ! empty( $user_debug_info['custom_current_date'] ) ) {
			$date = $user_debug_info['custom_current_date'];
		}

		if ( 0 !== $days ) {
			$_days = "$days days";
			// Add days to $date.
			$date = gmdate( 'Y-m-d H:i:s', strtotime( $date . ' + ' . $_days ) );
		}
		if ( 0 !== $hours ) {
			$date = gmdate( 'Y-m-d H:i:s', strtotime( $date ) + 60 * 60 );
		}

		return $date;
	}

	/**
	 * Get date.
	 *
	 * @param int $days How many days to add or subtract.
	 * @param int $hours How many hours to add or subtract.
	 *
	 * @return string
	 */
	public static function get_date( int $days = 0, int $hours = 0 ): string {
		return date( 'Y-m-d', strtotime( self::get_date_time( $days, $hours ) ) );
	}

}
