<?php
/**
 * The initializer file
 *
 * @package HRE_Addon
 */

namespace HRE_Addon;

use HRE_Addon\Includes\CPT\CPT_Buyer_Applications;
use HRE_Addon\Includes\CPT\CPT_Inquiry;
use HRE_Addon\Includes\CPT\Meta_Box_Agreement;
use HRE_Addon\Includes\Pages\Admin_Page_Info;
use HRE_Addon\Includes\Pages\Admin_Page_Settings;
use HRE_Addon\Includes\Rest_Api\Redirect_Service;
use HRE_Addon\Includes\Rest_Api\Rest_Admin_Settings;
use HRE_Addon\Includes\Rest_Api\Rest_Auth;
use HRE_Addon\Includes\Rest_Api\Rest_Buyer;
use HRE_Addon\Includes\Rest_Api\Rest_Buyer_Application;
use HRE_Addon\Includes\Rest_Api\Rest_Elementor_Agent_form;
use HRE_Addon\Includes\Rest_Api\Rest_Inquiry;
use HRE_Addon\Includes\Rest_Api\User_Service;
use HRE_Addon\Includes\services\Cart_Services;
use HRE_Addon\Includes\services\Checkout_Services;
use HRE_Addon\Includes\services\Property_Apply_Sections;
use HRE_Addon\Includes\services\Script_Service;
use HRE_Addon\Includes\Shortcodes\Shortcode_Agent_Login;
use HRE_Addon\Includes\Shortcodes\Shortcode_Agent_Signup;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Dashboard;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Login;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Onboarding_1;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Onboarding_2;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Preference;
use HRE_Addon\Includes\Shortcodes\Shortcode_Buyer_Signup;
use HRE_Addon\Includes\Shortcodes\Shortcode_Inquiry;
use HRE_Addon\Includes\Shortcodes\Shortcode_Property_Agreement;
use HRE_Addon\Includes\Shortcodes\Shortcode_Seller_Login;
use HRE_Addon\Includes\Shortcodes\Shortcode_Seller_Onboarding_1;
use HRE_Addon\Includes\Shortcodes\Shortcode_Seller_Signup;
use HRE_Addon\Includes\Users\Custom_User_Columns;
use HRE_Addon\Libs\Common;
use HRE_Addon\Libs\Settings;

use function HRE_Addon\I18n\mp_client_translations;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // exit if accessed directly.
}

/**
 * Class Initializer
 *
 * Initializes the plugin
 *
 * @package HRE_Addon
 */
class Initializer {
	/**
	 * The plugin version.
	 *
	 * @var string $version
	 */
	public const PLUGIN_VERSION = '7.0.1';

	/**
	 * The plugin directory.
	 *
	 * @var string $plugin_dir
	 */
	public static string $plugin_dir;
	/**
	 * Holds the plugin url.
	 *
	 * @var string $plugin_url
	 */
	public static string $plugin_url;
	/**
	 * Holds whether in local development.
	 *
	 * @var bool $template_dir
	 */
	public static bool $in_local_server = false;
	/**
	 * Holds the script version.
	 *
	 * @var string $script_version
	 */
	public static string $script_version = self::PLUGIN_VERSION;
	/**
	 * The nonce action key.
	 *
	 * @var string $nonce
	 */
	public static string $nonce_action = 'hr3GeneralNoncePereereDotCom';
	/**
	 * The client data key.
	 *
	 * @var string $client_date_key
	 */
	public static string $client_date_key = 'hr3GeneralClientDataPereereDotCom';
	/**
	 * Holds the client data.
	 *
	 * @var array $client_data
	 */
	public static array $client_data = array();
	/**
	 * Holds the only instance of this class.
	 *
	 * @var self|null $instance
	 */
	public static ?Initializer $instance = null;

	// Custom properties.
	/**
	 * Holds an instance of the admin page settings.
	 *
	 * @var ?Admin_Page_Settings $admin_page_settings
	 */
	public ?Admin_Page_Settings $admin_page_settings = null;
	/**
	 * Holds an instance of the admin page info.
	 *
	 * @var ?Admin_Page_Info $admin_page_info
	 */
	public ?Admin_Page_Info $admin_page_info = null;

	/**
	 * Initializer constructor.
	 */
	private function __construct() {
		$this->init_variables();
		$this->initialize();
	}

	/**
	 * Returns the only instance of the class.
	 *
	 * @return self
	 */
	public static function get_instance(): Initializer {
		if ( ! ( self::$instance instanceof self ) ) {
			self::$instance = new Initializer();
		}

		return self::$instance;
	}

	/**
	 * Initialize everything
	 */
	private function initialize(): void {
		add_action( 'init', array( $this, 'on_init' ) );
		add_action( 'admin_init', array( $this, 'on_admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_for_frontend' ) );

		$this->initialize_custom_post_types();
		$this->initialize_admin_pages();
		$this->initialize_rest_api();
		$this->initialize_meta_boxes();
		$this->initialize_shortcodes();
		$this->initialize_sections();
		$this->initialize_services();

		hre_add_custom_styles();


		add_action( 'init', array( $this, 'load_plugin_text_domain' ) );
	}

	/**
	 * Initialize meta boxes.
	 */
	public function initialize_meta_boxes(): void {
		( new Meta_Box_Agreement() )->init();
	}

	/**
	 *
	 * Initialize the shortcodes.
	 *
	 * @return void
	 */
	public function initialize_shortcodes(): void {
		( new Shortcode_Buyer_Signup() )->init();
		( new Shortcode_Seller_Signup() )->init();
		( new Shortcode_Buyer_Login() )->init();
		( new Shortcode_Seller_Login() )->init();
		( new Shortcode_Buyer_Dashboard() )->init();
		( new Shortcode_Property_Agreement() )->init();
		( new Shortcode_Buyer_Preference() )->init();
		( new Shortcode_Buyer_Onboarding_1() )->init();
		( new Shortcode_Seller_Onboarding_1() )->init();
		( new Shortcode_Buyer_Onboarding_2() )->init();
		( new Shortcode_Inquiry() )->init();
		( new Shortcode_Agent_Signup() )->init();
		( new Shortcode_Agent_Login() )->init();
	}


	/**
	 *
	 * Initialize the sections.
	 *
	 * @return void
	 */
	public function initialize_sections(): void {
		( new \Section_Checkout_Elite_Thank_You() )->init();
	}

	/**
	 * Initialize services.
	 *
	 * @return void
	 */
	public function initialize_services(): void {
		( new Script_Service() )->init();
		( new Cart_Services() )->init();
		( new Checkout_Services() )->init();
		( new Property_Apply_Sections() )->init();
		( new User_Service() )->init();
		( new Redirect_Service() )->init();
		( new Custom_User_Columns() )->init();
		( new Redirect_Service() )->init();
	}

	/**
	 * Initialize custom post types.
	 *
	 * @return void
	 */
	public function initialize_custom_post_types(): void {
		( new CPT_Buyer_Applications() )->init();
		( new CPT_Inquiry() )->init();
	}

	/**
	 * Initialize the pages.
	 *
	 * @return void
	 */
	public function initialize_admin_pages(): void {
		$this->admin_page_settings = ( new Admin_Page_Settings() )->init();
		$this->admin_page_info     = ( new Admin_Page_Info() )->init();
	}

	/**
	 * Initialize rest api.
	 *
	 * @return void
	 */
	public function initialize_rest_api(): void {
		( new Rest_Admin_Settings() )->init();
		( new Rest_Elementor_Agent_form() )->init();
		( new Rest_Auth() )->init();
		( new Rest_Buyer() )->init();
		( new Rest_Buyer_Application() )->init();
		( new Rest_Inquiry() )->init();
	}

	/*******************************************************************************************************************
	 * Indigenous METHODS.
	 ******************************************************************************************************************/

	/**
	 * Load the text domain.
	 *
	 * @return void
	 */
	public function load_plugin_text_domain(): void {
		$language_path = self::$plugin_dir . '/i18n/languages';
		load_plugin_textdomain(
			'hre-addon',
			false,
			$language_path
		);

		// Include Theme text translation file.
		$locale      = get_locale();
		$locale_file = self::$plugin_dir . "/i18n/languages/hre-addon-$locale.mo";
		$is_readable = is_readable( $locale_file );
		$loaded      = false;
		if ( $is_readable ) {
			$loaded = load_textdomain( 'hre-addon', $locale_file );
		}
		// Common::in_script_or_send_error( [
		// 'method'        => __METHOD__,
		// 'locale_file'   => $locale_file,
		// 'is_readable'   => $is_readable,
		// 'loaded'        => $loaded,
		// 'locale'        => $locale,
		// 'language_path' => $language_path,
		// ] );
	}

	/**
	 * Runs on admin init hook.
	 *
	 * @return void
	 */
	public function on_admin_init(): void {
	}

	/**
	 * Run after init action hook.
	 *
	 * @return void
	 */
	public function on_init(): void {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 1 );
		add_action( 'wp_footer', array( $this, 'output_client_data' ), 10 );
		add_action( 'admin_footer', array( $this, 'output_client_data' ), 10 );

		hre_add_style_to_hide_sidebar();
		hre_add_style_to_hide_search_bar();
		hre_add_script_to_scroll_up_to_woocommerce_notice();
		hre_add_style_to_modify_height_of_elementor_carousel_images();
		hre_add_style_to_hide_woocommerce_coupon_section();
		hre_add_style_to_hide_shipping_option_in_checkout_page();
	}


	/**
	 * Output the client data.
	 *
	 * @return void
	 */
	public function output_client_data() {
		self::$client_data['ajax_url'] = implode(
			'',
			array(
				get_admin_url( 'wcmv-admin.js' ),
				'admin-ajax.php',
			)
		);

		self::$client_data['nonce']              = \wp_create_nonce( self::$nonce_action );
		self::$client_data['rest_nonce']         = wp_create_nonce( 'wp_rest' );
		self::$client_data['isAdmin']            = current_user_can( 'manage_options' );
		self::$client_data['inAdmin']            = is_admin();
		self::$client_data['clientTranslations'] = mp_client_translations();

		// Custom data.
		self::$client_data['clientSettings']   = hre_get_client_settings();
		self::$client_data['client_settings']  = hre_get_client_settings();
		self::$client_data['userId']           = get_current_user_id();
		self::$client_data['isLoggedIn']       = is_user_logged_in();
		self::$client_data['woo_checkout_url'] = wc_get_checkout_url();
		self::$client_data['site_url']         = home_url();

		// Output the client data.
		Common::add_script(
			self::$client_data,
			self::$client_date_key
		);
	}

	/**
	 * Add value to the client data.
	 *
	 * @param string $key The client data key.
	 * @param mixed $value The client data value.
	 *
	 * @return void
	 */
	public static function add_to_client_data( $key, $value ) {
		if ( ! is_string( $key ) ) {
			return;
		}
		self::$client_data[ $key ] = $value;
	}

	/**
	 * Add menus.
	 */
	public function add_admin_menu(): void {
		// $hook        = add_menu_page(
		// 'Buyer Applications',
		// 'Buyer applications',
		// 'manage_options',
		// 'hre-addon',
		// array( $this->admin_page_availability, 'load_view' ),
		// 'dashicons-image-flip-horizontal'
		// );
		// $parent_slug = 'hre-addon';
		// post type booking url.
		$parent_slug = 'edit.php?post_type=' . Settings::POST_TYPE_BUYER_APPLICATION;
		$submenus    = array(
			// array(
			// 'parent_slug'                  => $parent_slug,
			// 'page_title'                   =>
			// translators: The page title.
			// __( 'Buyer Applications', 'hre-addon' ),
			// 'menu_title'                   =>
			// translators: The page title.
			// __( 'Buyer Applications', 'hre-addon' ),
			// 'menu_slug'                    => 'hre-addon-buyer-applications',
			// 'callback'                     => array(
			// $this->admin_page_availability,
			// 'load_view',
			// ),
			// 'screen_option_callback'       => array(
			// $this->admin_page_availability,
			// 'add_screen_options',
			// ),
			// 'process_bulk_action_callback' => array(
			// $this->admin_page_availability,
			// 'process_bulk_action',
			// ),
			// ),
			array(
				'parent_slug'                  => $parent_slug,
				'page_title'                   =>
				// translators: The page title.
					__( 'Settings', 'hre-addon' ),
				'menu_title'                   =>
				// translators: The page title.
					__( 'Settings', 'hre-addon' ),
				'menu_slug'                    => 'hre-addon-settings',
				'callback'                     => array(
					$this->admin_page_settings,
					'load_view',
				),
				'screen_option_callback'       => array(
					$this->admin_page_settings,
					'add_screen_options',
				),
				'process_bulk_action_callback' => array(
					$this->admin_page_settings,
					'process_bulk_action',
				),
			),
//			array(
//				'parent_slug'                  => $parent_slug,
//				'page_title'                   =>
//				// translators: The page title.
//					__( 'Info', 'hre-addon' ),
//				'menu_title'                   =>
//				// translators: The page title.
//					__( 'Info', 'hre-addon' ),
//				'menu_slug'                    => 'hre-addon',
//				'callback'                     => array(
//					$this->admin_page_info,
//					'load_view',
//				),
//				'screen_option_callback'       => array(
//					$this->admin_page_info,
//					'add_screen_options',
//				),
//				'process_bulk_action_callback' => array(
//					$this->admin_page_info,
//					'process_bulk_action',
//				),
//			),
		);

		foreach ( $submenus as $one_submenu ) {
			$submenu_hook = add_submenu_page(
				$one_submenu['parent_slug'],
				$one_submenu['page_title'],
				$one_submenu['menu_title'],
				'manage_options',
				$one_submenu['menu_slug'],
				$one_submenu['callback']
			);

			// Add screen option for admin users page.
			// if ( is_array( $one_submenu['screen_option_callback'] ) ) {
			// add_action(
			// "load-$submenu_hook",
			// $one_submenu['screen_option_callback']
			// );
			// }

			// Add bulk actions for admin users page.
			// if ( is_array( $one_submenu['process_bulk_action_callback'] ) ) {
			// add_action(
			// "load-$submenu_hook",
			// $one_submenu['process_bulk_action_callback']
			// );
			// }
		}

		// Remove the first menu item.
		remove_submenu_page( 'hre-addon', 'hre-addon' );
		remove_submenu_page( 'post-new.php?post_type=' . Settings::POST_TYPE_BUYER_APPLICATION,
			'post-new.php?post_type=' . Settings::POST_TYPE_BUYER_APPLICATION );
	}

	/**
	 * Initialize default variables.
	 *
	 * @return void
	 */
	private function init_variables(): void {
		$plugin_name = basename( __DIR__ );
		$plugin_dir  = __DIR__;
		$plugin_url  = get_site_url() . "/wp-content/plugins/$plugin_name";
		if ( str_contains( $plugin_url, 'test-site-wordpress' ) ) {
			// in local server.
			self::$in_local_server = true;
		}
		self::$plugin_dir = $plugin_dir;
		self::$plugin_url = $plugin_url;

		// Set script version.
		add_action(
			'init',
			function () {
				// set script version to  plugin version.
				self::$script_version = self::PLUGIN_VERSION;

				// Set the script versions to dynamic value when in debug mode.
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					self::$script_version = time();
				}
			}
		);
	}

	/**
	 * Register Scripts and styles.
	 *
	 * @return void
	 */
	public function register_scripts(): void {
		wp_enqueue_script( 'jquery' );

		if ( ! is_admin() ) {
			return;
		}

		$page = filter_input( INPUT_GET, 'page' );
		if ( 'e-form-submissions' !== $page ) {
			return;
		}

		// Register form submissions.
		$js  = mp_get_script( '/admin/hre-elementor-submissions' );
		$css = mp_get_style( '/admin/hre-elementor-submissions' );

		wp_register_script(
			'hre-admin-hre-elementor-submissions',
			$js,
			array( 'jquery', 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-editor' ),
			self::$script_version,
			true
		);
		wp_register_style( 'hre-admin-hre-elementor-submissions', $css, array(), self::$script_version );

		wp_enqueue_script( 'hre-admin-hre-elementor-submissions' );
		wp_enqueue_style( 'hre-admin-hre-elementor-submissions' );
	}

	/**
	 * Register frontend script.
	 */
	public function register_scripts_for_frontend(): void {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'dashicons' );
	}

	/**
	 * Run on plugin activation.
	 *
	 * @return void
	 */
	public function on_activate(): void {
		flush_rewrite_rules( true );
	}

	/**
	 * Run on plugin deactivation.
	 *
	 * @return void
	 */
	public function on_deactivate(): void {
	}

	/**
	 * Run on plugin uninstall.
	 *
	 * @return void
	 */
	public function on_uninstall(): void {
	}
}
