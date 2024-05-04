<?php
/**
 * Common functions.
 */

namespace HRE_Addon;

use DateTime;
use Exception;
use HRE_Addon\Libs\Common;
use HRE_Addon\Libs\Settings;

/** ========================================================= */
/** ====================== Default functions ====================== */
/** ========================================================= */


/**
 * Get the absolute path of a file in the plugin.
 *
 * @param string $file The file name, from the plugin root. DONT prefix with a slash.
 * @param string $ext The file extension.
 *
 * @return string
 */
function mp_get_absolute_path( $file, $ext = 'php' ) {
	return Initializer::$plugin_dir . '/' . $file . '.' . $ext;
}

/**
 * Load template.
 *
 * @param string $template The template.
 *
 * @return void
 */
function mp_load_template( $template ) {
	require mp_get_template_path( $template );
}

/**
 * Return template path.
 *
 * @param string $template The template.
 * @param string $ext The extension.
 *
 * @return string
 */
function mp_get_template_path( $template, $ext = '.php' ) {
	return __DIR__ . '/templates/' . $template . $ext;
}

/**
 * Get the script url or path.
 *
 * @param string $script The script's path (without the extension)(with leading slash and no trailing slash).
 * @param string $path_or_url The 'path' or 'url'.
 *
 * @return string        The script url or path.
 */
function mp_get_script( $script, $path_or_url = 'url' ) {
	$version = mp_get_plugin_version( 'beauty-and-loyalty-point.php' );
	if ( 'url' === $path_or_url ) {
		return Initializer::$plugin_url . '/assets/build' . $script . '-'
		       . $version . '.js';
	} elseif ( 'path' === $path_or_url ) {
		return Initializer::$plugin_dir . '/assets/build' . $script . '-'
		       . $version . '.js';
	}

	return '';
}

/**
 * Get the style url or path.
 *
 * @param string $style The style (without the extention)(with leading slash and no trailing slash).
 * @param string $path_or_url The path or url.
 *
 * @return string             The style url or path.
 */
function mp_get_style( $style, $path_or_url = 'url' ) {
	$version = mp_get_plugin_version( 'beauty-and-loyalty-point.php' );
	if ( 'url' === $path_or_url ) {
		return Initializer::$plugin_url . '/assets/build' . $style . '-'
		       . $version . '.css';
	} elseif ( 'path' === $path_or_url ) {
		return Initializer::$plugin_dir . '/assets/build' . $style . '-'
		       . $version . '.css';
	}

	return '';
}

function mp_get_plugin_version( $plugin_slug ) {
	if ( ! function_exists( 'get_plugin_data' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$current_parent_folder = basename( dirname( __FILE__, 1 ) );
	$main_plugin_file      = __DIR__ . '/' . $current_parent_folder . '.php';
	$plugin_data           = get_plugin_data( $main_plugin_file );

	return $plugin_data['Version'];
}

/**
 * Sends json error.
 *
 * @param string $message The error message.
 * @param array $data The data to send.
 *
 * @return void
 */
function mp_send_ajax_error( $message = '', $data = array() ) {
	$default = array(
		'message' => '',
		'data'    => array(),
	);
	$args    = wp_parse_args(
		array(
			'message' => $message,
			'data'    => $data,
		),
		$default
	);

	/**
	 * Filters the json error.
	 *
	 * @since 1.0.0
	 */
	if ( ! apply_filters( 'hre_send_json_error', true ) ) {
		return;
	}

	/**
	 * Filters the json error message.
	 *
	 * @since 1.0.0
	 */
	$args = apply_filters( 'hre_send_json_error_args', $args );

	/**
	 * About to send json error.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hre_send_json_error', $args );

	wp_send_json_error( $args );
}

/**
 * Sends json success.
 *
 * @param string $message The success message.
 * @param array $data The data to send.
 *
 * @return void
 */
function mp_send_ajax_success( $message = '', $data = array() ) {
	$default = array(
		'message' => '',
		'data'    => array(),
	);
	$args    = wp_parse_args(
		array(
			'message' => $message,
			'data'    => $data,
		),
		$default
	);

	/**
	 * Filters the json success.
	 *
	 * @since 1.0.0
	 */
	if ( ! apply_filters( 'hre_send_json_success', true ) ) {
		return;
	}

	/**
	 * Filters the json success message.
	 *
	 * @since 1.0.0
	 */
	$args = apply_filters( 'hre_send_json_success_args', $args );

	/**
	 * About to send json success.
	 *
	 * @since 1.0.0
	 */
	do_action( 'hre_send_json_success', $args );

	wp_send_json_success( $args );
}

/**
 * Whether a date is valid.
 *
 * @param string $date The date.
 *
 * @return bool
 */
function mp_is_valid_date( string $date ): bool {
	$d = \DateTime::createFromFormat( 'Y-m-d', $date );

	return $d && $d->format( 'Y-m-d' ) === $date;
}

/**
 * Get the logger.
 *
 * @return Mp_Logger
 */

/**
 * Add minutes to time.
 *
 * @param int $time The time.
 * @param int $minutes The minutes to add.
 *
 * @return int The new time.
 */
function add_minutes_to_time( int $time, int $minutes = 0 ): int {
	// Extract the hours and minutes from the input time
	$hours         = floor( $time / 100 );
	$minutesInTime = $time % 100;

	// Add the specified minutes
	$totalMinutes = $hours * 60 + $minutesInTime + $minutes;

	// Calculate the new hours and minutes
	$newHours   = floor( $totalMinutes / 60 );
	$newMinutes = $totalMinutes % 60;

	// Format the new time
	return $newHours * 100 + $newMinutes;
}

/**
 * Get all wp pages.
 *
 * @return array
 */
function mp_get_all_wp_pages(): array {
	$pages = get_pages();

	$pages_array = array();

	foreach ( $pages as $page ) {
		$pages_array[] = array(
			'id'    => $page->ID,
			'url'   => get_page_link( $page->ID ),
			'title' => $page->post_title,
		);
	}

	return $pages_array;
}

/**
 * Get debug data if debug mode is on.
 *
 * @param array $data The data. If debug mode is off, this will be an empty array.
 */
function mp_debug( array $data ): array {
	if ( Settings::DEBUG_MODE ) {
		return $data;
	}

	return array();
}

/** ========================================================= */
/** ====================== Custom Functions ====================== */
/** ========================================================= */

/**
 * Get client settings.
 *
 * @return array
 */
function hre_get_client_settings(): array {
	$user_id                             = get_current_user_id();
	$buyer_elite_login_page_id           = get_option( Settings::OPTION_BUYER_ELITE_LOGIN_PAGE_ID, '' );
	$create_listing_page_url             = get_permalink( get_option( Settings::OPTION_CREATE_LISTING_PAGE_ID, '' ) );
	$buyer_preference_page_url           = get_permalink( get_option( Settings::OPTION_BUYER_PREFERENCE_PAGE_ID, 0 ) );
	$buyer_elite_login_page_url          = get_permalink( $buyer_elite_login_page_id );
	$buyer_elite_signup_page_url         = get_permalink(
		get_option(
			Settings::OPTION_BUYER_ELITE_SIGNUP_PAGE_ID,
			''
		)
	);
	$seller_elite_login_page_url         = get_permalink(
		get_option(
			Settings::OPTION_SELLER_ELITE_LOGIN_PAGE_ID,
			''
		)
	);
	$seller_elite_signup_page_url        = get_permalink(
		get_option(
			Settings::OPTION_SELLER_ELITE_SIGNUP_PAGE_ID,
			''
		)
	);
	$buyer_dashboard_url                 = get_permalink( get_option( Settings::OPTION_BUYER_DASHBOARD_PAGE_ID, '' ) );
	$buyer_elite_access_fee_number       = (float) get_option( Settings::OPTION_BUYER_ELITE_ACCESS_FEE, 0 );
	$buyer_elite_access_fee              = htmlentities( wc_price( $buyer_elite_access_fee_number ) );
	$seller_elite_access_fee_number      = (float) get_option( Settings::OPTION_SELLER_ELITE_ACCESS_FEE, '' );
	$seller_elite_access_fee             = htmlentities( wc_price( $seller_elite_access_fee_number ) );
	$woo_currency_symbol                 = get_woocommerce_currency_symbol();
	$agreement_page_url                  = get_permalink(
		get_option(
			Settings::OPTION_PROPERTY_AGREEMENT_PAGE_ID,
			''
		)
	);
	$terms_and_conditions_page_url       = get_permalink(
		get_option(
			Settings::OPTION_TERMS_AND_CONDITIONS_PAGE_ID,
			''
		)
	);
	$search_by_map_page_url              = get_permalink( get_option( Settings::OPTION_SEARCH_BY_MAP_PAGE_ID, '' ) );
	$elite_membership_duration           = (int) get_option( Settings::OPTION_ELITE_MEMBERSHIP_DURATION_MONTHS, '' );
	$seller_elite_page_url               = get_permalink( get_option( Settings::OPTION_SELLER_ELITE_PAGE_ID, '' ) );
	$buyer_elite_page_url                = get_permalink( get_option( Settings::OPTION_BUYER_ELITE_PAGE_ID, '' ) );
	$agent_login_page_url                = get_permalink( get_option( Settings::OPTION_AGENT_LOGIN_PAGE_ID, '' ) );
	$agent_signup_page_url               = get_permalink( get_option( Settings::OPTION_AGENT_SIGNUP_PAGE_ID, '' ) );
	$user_elite_fee_has_expired          = hre_user_elite_access_fee_has_expired( $user_id );
	$buyer_onboarding_process_2_page_url = get_permalink(
		get_option(
			Settings::OPTION_BUYER_ONBOARDING_PROCESS_2_PAGE_ID,
			''
		)
	);
	$google_captcha_site_key             = get_option( Settings::OPTION_GOOGLE_CAPTCHA_SITE_KEY, '' );
	$elite_role                          = 'none';
	$user_data                           = get_userdata( $user_id );
	if ( $user_data instanceof \WP_User ) {
		if ( in_array( Settings::USER_ROLE_BUYER, $user_data->roles, true ) ) {
			$elite_role = 'buyer';
		} elseif ( in_array( Settings::USER_ROLE_SELLER, $user_data->roles, true ) ) {
			$elite_role = 'seller';
		}
	}

	return array(
		'buyer_preference_page_url'           => $buyer_preference_page_url,
		'buyer_elite_login_page_url'          => $buyer_elite_login_page_url,
		'buyer_elite_signup_page_url'         => $buyer_elite_signup_page_url,
		'seller_elite_login_page_url'         => $seller_elite_login_page_url,
		'seller_elite_signup_page_url'        => $seller_elite_signup_page_url,
		'buyer_dashboard_url'                 => $buyer_dashboard_url,
		'buyer_elite_access_fee'              => $buyer_elite_access_fee,
		'buyer_elite_access_fee_number'       => $buyer_elite_access_fee_number,
		'seller_elite_access_fee'             => $seller_elite_access_fee,
		'seller_elite_access_fee_number'      => $seller_elite_access_fee_number,
		'currency_symbol'                     => $woo_currency_symbol,
		'agreement_page_url'                  => $agreement_page_url,
		'terms_and_conditions_page_url'       => $terms_and_conditions_page_url,
		'search_by_map_page_url'              => $search_by_map_page_url,
		'create_listing_page_url'             => $create_listing_page_url,
		'elite_membership_duration'           => $elite_membership_duration,
		'seller_elite_page_url'               => $seller_elite_page_url,
		'buyer_elite_page_url'                => $buyer_elite_page_url,
		'user_elite_fee_has_expired'          => $user_elite_fee_has_expired,
		'elite_role'                          => $elite_role,
		'buyer_onboarding_process_2_page_url' => $buyer_onboarding_process_2_page_url,
		'agent_signup_page_url'               => $agent_signup_page_url,
		'agent_login_page_url'                => $agent_login_page_url,
		'google_captcha_site_key'             => $google_captcha_site_key,
	);
	// elite_role: 'buyer' | 'seller' | 'none';
}

/**
 * Check if user is a buyer.
 *
 * @param int $user_id The user id.
 *
 * @return bool
 */
function hre_buyer_has_paid( int $user_id ): bool {
	$paid = get_user_meta( $user_id, Settings::UM_BUYER_APPLICATION_PAID, true );

	return 'yes' === $paid;
}

/**
 * Check if the buyer or seller did purchase elite access but it has expired.
 *
 * @param int $buyer_id The user id of the buyer .
 *
 * @return bool
 */
function hre_user_elite_access_fee_has_expired( int $buyer_id ): bool {
	if ( 'yes' !== hre_buyer_has_paid( $buyer_id ) ) {
		return false;
	}

	$paid_date = get_user_meta( $buyer_id, Settings::UM_ELITE_MEMBERSHIP_PAID_DATE, true );
	if ( empty( $paid_date ) ) {
		$today = Common::get_date_time();
		update_user_meta( $buyer_id, Settings::UM_ELITE_MEMBERSHIP_PAID_DATE, $today );
		$paid_date = $today;
	}

	$months_buyer_payment_lasts = (int) get_option( Settings::OPTION_ELITE_MEMBERSHIP_DURATION_MONTHS, 0 );
	if ( empty( $months_buyer_payment_lasts ) ) {
		$months_buyer_payment_lasts = 6;
	}

	return _date_expired( $paid_date, $months_buyer_payment_lasts );
}

/**
 * Check if the start date + months has expired.
 *
 * @param string $start_datetime The start date.
 * @param int $month The number of months.
 *
 * @return bool
 */
function _date_expired( string $start_datetime, int $month ): bool {
	try {
		$start_datetime_datetime = new DateTime( $start_datetime );
		$start_datetime_datetime->modify( '+' . $month . ' month' );

		$today_datetime = new DateTime( Common::get_date_time() );
		$diff           = $start_datetime_datetime->diff( $today_datetime );
		$days           = $diff->format( '%a' );
		if ( $days < 0 ) {
			return true;
		}
	} catch ( Exception $e ) {
		// empty.
	}

	return false;
}

/**
 * Get admin settings.
 *
 * @return array
 */
function hre_get_admin_settings(): array {
	$buyer_elite_access_fee              = (float) get_option( Settings::OPTION_BUYER_ELITE_ACCESS_FEE, 0 );
	$seller_elite_access_fee             = (float) get_option( Settings::OPTION_SELLER_ELITE_ACCESS_FEE, 0 );
	$buyer_elite_signup_page_id          = (int) get_option( Settings::OPTION_BUYER_ELITE_SIGNUP_PAGE_ID, 0 );
	$buyer_elite_login_page_id           = (int) get_option( Settings::OPTION_BUYER_ELITE_LOGIN_PAGE_ID, 0 );
	$seller_elite_signup_page_id         = (int) get_option( Settings::OPTION_SELLER_ELITE_SIGNUP_PAGE_ID, 0 );
	$seller_elite_login_page_id          = (int) get_option( Settings::OPTION_SELLER_ELITE_LOGIN_PAGE_ID, 0 );
	$agent_login_page_id                 = (int) get_option( Settings::OPTION_AGENT_LOGIN_PAGE_ID, 0 );
	$agent_signup_page_id                = (int) get_option( Settings::OPTION_AGENT_SIGNUP_PAGE_ID, 0 );
	$property_agreement_page_id          = (int) get_option( Settings::OPTION_PROPERTY_AGREEMENT_PAGE_ID, 0 );
	$buyer_dashboard_page_id             = (int) get_option( Settings::OPTION_BUYER_DASHBOARD_PAGE_ID, 0 );
	$create_listing_page_id              = (int) get_option( Settings::OPTION_CREATE_LISTING_PAGE_ID, 0 );
	$default_agreement                   = get_option( Settings::OPTION_DEFAULT_AGREEMENT_1, '' );
	$default_agreement2                  = get_option( Settings::OPTION_DEFAULT_AGREEMENT_2, '' );
	$search_by_map_page_id               = (int) get_option( Settings::OPTION_SEARCH_BY_MAP_PAGE_ID, 0 );
	$terms_and_conditions_page_id        = (int) get_option( Settings::OPTION_TERMS_AND_CONDITIONS_PAGE_ID, 0 );
	$elite_membership_duration_months    = (int) get_option( Settings::OPTION_ELITE_MEMBERSHIP_DURATION_MONTHS, 0 );
	$buyer_onboarding_process_1_page_id  = (int) get_option( Settings::OPTION_BUYER_ONBOARDING_PROCESS_1_PAGE_ID, 0 );
	$seller_onboarding_process_1_page_id = (int) get_option( Settings::OPTION_SELLER_ONBOARDING_PROCESS_1_PAGE_ID, 0 );
	$buyer_elite_page_id                 = (int) get_option( Settings::OPTION_BUYER_ELITE_PAGE_ID, '' );
	$seller_elite_page_id                = (int) get_option( Settings::OPTION_SELLER_ELITE_PAGE_ID, '' );
	$buyer_onboarding_process_2_page_id  = (int) get_option( Settings::OPTION_BUYER_ONBOARDING_PROCESS_2_PAGE_ID, '' );
	$google_captcha_site_key             = get_option( Settings::OPTION_GOOGLE_CAPTCHA_SITE_KEY, '' );
	$google_captcha_secret_key           = get_option( Settings::OPTION_GOOGLE_CAPTCHA_SECRET_KEY, '' );
	$buyer_preference_page_id            = (int) get_option( Settings::OPTION_BUYER_PREFERENCE_PAGE_ID, '' );

	return array(
		'buyer_elite_access_fee'              => $buyer_elite_access_fee,
		'seller_elite_access_fee'             => $seller_elite_access_fee,
		'buyer_elite_signup_page_id'          => $buyer_elite_signup_page_id,
		'buyer_elite_login_page_id'           => $buyer_elite_login_page_id,
		'seller_elite_signup_page_id'         => $seller_elite_signup_page_id,
		'seller_elite_login_page_id'          => $seller_elite_login_page_id,
		'agent_login_page_id'                 => $agent_login_page_id,
		'agent_signup_page_id'                => $agent_signup_page_id,
		'property_agreement_page_id'          => $property_agreement_page_id,
		'buyer_dashboard_page_id'             => $buyer_dashboard_page_id,
		'default_agreement'                   => $default_agreement,
		'default_agreement2'                  => $default_agreement2,
		'search_by_map_page_id'               => $search_by_map_page_id,
		'terms_and_conditions_page_id'        => $terms_and_conditions_page_id,
		'create_listing_page_id'              => $create_listing_page_id,
		'elite_membership_duration_months'    => $elite_membership_duration_months,
		'buyer_onboarding_process_1_page_id'  => $buyer_onboarding_process_1_page_id,
		'seller_onboarding_process_1_page_id' => $seller_onboarding_process_1_page_id,
		'seller_elite_page_id'                => $seller_elite_page_id,
		'buyer_elite_page_id'                 => $buyer_elite_page_id,
		'buyer_onboarding_process_2_page_id'  => $buyer_onboarding_process_2_page_id,
		'google_captcha_site_key'             => $google_captcha_site_key,
		'google_captcha_secret_key'           => $google_captcha_secret_key,
		'buyer_preference_page_id'            => $buyer_preference_page_id,
	);
}

/**
 * Save admin settings.
 *
 * @param array $settings The settings.
 *
 * @return void
 */
function hre_save_admin_settings( array $settings ) {
	update_option( Settings::OPTION_BUYER_ELITE_ACCESS_FEE, $settings['buyer_elite_access_fee'] );
	update_option( Settings::OPTION_SELLER_ELITE_ACCESS_FEE, $settings['seller_elite_access_fee'] );
	update_option( Settings::OPTION_BUYER_ELITE_SIGNUP_PAGE_ID, $settings['buyer_elite_signup_page_id'] );
	update_option( Settings::OPTION_BUYER_ELITE_LOGIN_PAGE_ID, $settings['buyer_elite_login_page_id'] );
	update_option( Settings::OPTION_SELLER_ELITE_SIGNUP_PAGE_ID, $settings['seller_elite_signup_page_id'] );
	update_option( Settings::OPTION_SELLER_ELITE_LOGIN_PAGE_ID, $settings['seller_elite_login_page_id'] );
	update_option( Settings::OPTION_AGENT_LOGIN_PAGE_ID, $settings['agent_login_page_id'] );
	update_option( Settings::OPTION_AGENT_SIGNUP_PAGE_ID, $settings['agent_signup_page_id'] );
	update_option( Settings::OPTION_AGENT_LOGIN_PAGE_ID, $settings['agent_login_page_id'] );
	update_option( Settings::OPTION_AGENT_SIGNUP_PAGE_ID, $settings['agent_signup_page_id'] );
	update_option( Settings::OPTION_CREATE_LISTING_PAGE_ID, $settings['create_listing_page_id'] );
	update_option( Settings::OPTION_PROPERTY_AGREEMENT_PAGE_ID, $settings['property_agreement_page_id'] );
	update_option( Settings::OPTION_BUYER_DASHBOARD_PAGE_ID, $settings['buyer_dashboard_page_id'] );
	update_option( Settings::OPTION_DEFAULT_AGREEMENT_1, $settings['default_agreement'] );
	update_option( Settings::OPTION_DEFAULT_AGREEMENT_2, $settings['default_agreement2'] );
	update_option( Settings::OPTION_SEARCH_BY_MAP_PAGE_ID, $settings['search_by_map_page_id'] );
	update_option( Settings::OPTION_TERMS_AND_CONDITIONS_PAGE_ID, $settings['terms_and_conditions_page_id'] );
	update_option( Settings::OPTION_ELITE_MEMBERSHIP_DURATION_MONTHS, $settings['elite_membership_duration_months'] );
	update_option( Settings::OPTION_BUYER_PREFERENCE_PAGE_ID, $settings['buyer_preference_page_id'] );
	update_option(
		Settings::OPTION_BUYER_ONBOARDING_PROCESS_1_PAGE_ID,
		$settings['buyer_onboarding_process_1_page_id']
	);
	update_option(
		Settings::OPTION_SELLER_ONBOARDING_PROCESS_1_PAGE_ID,
		$settings['seller_onboarding_process_1_page_id']
	);
	update_option( Settings::OPTION_SELLER_ELITE_PAGE_ID, $settings['seller_elite_page_id'] );
	update_option( Settings::OPTION_BUYER_ELITE_PAGE_ID, $settings['buyer_elite_page_id'] );
	update_option(
		Settings::OPTION_BUYER_ONBOARDING_PROCESS_2_PAGE_ID,
		$settings['buyer_onboarding_process_2_page_id']
	);
	update_option( Settings::OPTION_GOOGLE_CAPTCHA_SITE_KEY, $settings['google_captcha_site_key'] );
	update_option( Settings::OPTION_GOOGLE_CAPTCHA_SECRET_KEY, $settings['google_captcha_secret_key'] );
}

/**
 * Save buyer application product user form.
 *
 * @param int $product_id The product id.
 * @param string $username The username.
 * @param string $email The email.
 * @param string $phone The phone.
 * @param string $state The state.
 * @param string $password The password.
 * @param string $full_name The full name.
 * @param string $for The for. 'buyer'|'seller'.
 * @param int $seller_agent_id The seller agent id.
 * @param string $seller_agent_doesnt_exist The seller agent doesnt exist.
 * @param string $seller_agent_first_name The seller agent first name.
 * @param string $seller_agent_last_name The seller agent last name.
 * @param string $seller_agent_phone The seller agent phone.
 * @param string $seller_agent_state The seller agent state.
 *
 * @return void
 */
function hre_save_buyer_application_product_user_form(
	int $product_id,
	string $username,
	string $email,
	string $phone,
	string $state,
	string $password,
	string $full_name,
	string $for,
	int $seller_agent_id,
	string $seller_agent_doesnt_exist,
	string $seller_agent_first_name,
	string $seller_agent_last_name,
	string $seller_agent_phone,
	string $seller_agent_state
) {
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_PRODUCT, 'yes' );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_NAME, $username );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_EMAIL, $email );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_PHONE, $phone );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_STATE, $state );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_PASSWORD, $password );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FULL_NAME, $full_name );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FOR, $for );
	// seller agent.
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_ID, $seller_agent_id );
	update_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_DOESNT_EXIST,
		$seller_agent_doesnt_exist
	);
	update_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_FIRST_NAME,
		$seller_agent_first_name
	);
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_LAST_NAME, $seller_agent_last_name );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_PHONE, $seller_agent_phone );
	update_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_STATE_OF_RESIDENCE, $seller_agent_state );
}


/**
 * Get buyer application product user form.
 *
 * @param int $product_id The product id.
 *
 * @return array
 */
function hre_get_elite_application_product_user_form( int $product_id ): array {
	$username                  = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_NAME, true );
	$email                     = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_EMAIL, true );
	$phone                     = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_PHONE, true );
	$state                     = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_STATE, true );
	$password                  = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_USER_PASSWORD, true );
	$full_name                 = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FULL_NAME, true );
	$for                       = get_post_meta( $product_id, Settings::PM_IS_BUYER_APPLICATION_FOR, true );
	$seller_agent_id           = (int) get_post_meta( $product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_ID,
		true );
	$seller_agent_doesnt_exist = get_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_DOESNT_EXIST,
		true
	);
	$seller_agent_first_name   = get_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_FIRST_NAME,
		true
	);
	$seller_agent_last_name    = get_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_LAST_NAME,
		true
	);
	$seller_agent_phone        = get_post_meta(
		$product_id,
		Settings::PM_IS_BUYER_APPLICATION_SELLER_AGENT_PHONE,
		true
	);

	return array(
		'username'                  => $username,
		'email'                     => $email,
		'phone'                     => $phone,
		'state'                     => $state,
		'password'                  => $password,
		'full_name'                 => $full_name,
		'for'                       => $for,
		'seller_agent_id'           => $seller_agent_id,
		'seller_agent_doesnt_exist' => $seller_agent_doesnt_exist,
		'seller_agent_first_name'   => $seller_agent_first_name,
		'seller_agent_last_name'    => $seller_agent_last_name,
		'seller_agent_phone'        => $seller_agent_phone,
	);
}

/**
 * Save user debug info.
 *
 * @param int $user_id The user id.
 * @param string $custom_current_datetime The custom datetime.
 *
 * @return void
 */
function hre_save_user_debug_info( int $user_id, string $custom_current_datetime ): void {
	update_user_meta( $user_id, Settings::UM_USER_DEBUG_CUSTOM_CURRENT_DATETIME, $custom_current_datetime );
}

/**
 * Get user debug info.
 *
 * @param int $user_id The user id.
 *
 * @return array
 */
function hre_get_user_debug_info( int $user_id ): array {
	$custom_datetime = get_user_meta( $user_id, Settings::UM_USER_DEBUG_CUSTOM_CURRENT_DATETIME, true );

	return array(
		'custom_current_datetime' => $custom_datetime,
	);
}

/**
 * Check if current user is a buyer or a seller.
 *
 * @return string 'buyer'|'seller'|false
 */
function hre_is_buyer_or_seller( $user_id = null ): string {
	$user_id   = $user_id ?? get_current_user_id();
	$user_data = get_userdata( $user_id );
	if ( in_array( Settings::USER_ROLE_BUYER, $user_data->roles, true ) ) {
		return 'buyer';
	}

	if ( in_array( Settings::USER_ROLE_SELLER, $user_data->roles, true ) ) {
		return 'seller';
	}

	return false;
}

/**
 * Get cities.
 *
 * @return string[]
 */
function hre_get_cities(): array {
	return array(
		'Chicago',
		'Faisalabad',
		'Los Angeles',
		'Miami',
		'New York',
		'Winfield',
	);
}

/**
 * Get area options.
 *
 * @return string[]
 */
function hre_get_area_options(): array {
	return array(
		'Albany Park',
		'Altgeld Gardens',
		'Andersonville',
		'Beverly',
		'Brickel',
		'Brooklyn',
		'Brookside',
		'Central City',
		'Coconut Grove',
		'Hyde Park',
		'Manhattan',
		'Midtown',
		'Northeast Los Angeles',
		'University Roadmap',
		'West Flagger',
		'Wynwood',
	);
}

/**
 * Get US states.
 *
 * @return string[]
 */
function hre_get_us_states(): array {
	return array(
		'Alabama',
		'Alaska',
		'Arizona',
		'Arkansas',
		'California',
		'Colorado',
		'Connecticut',
		'Delaware',
		'Florida',
		'Georgia',
		'Hawaii',
		'Idaho',
		'Illinois',
		'Indiana',
		'Iowa',
		'Kansas',
		'Kentucky',
		'Louisiana',
		'Maine',
		'Maryland',
		'Massachusetts',
		'Michigan',
		'Minnesota',
		'Mississippi',
		'Missouri',
		'Montana',
		'Nebraska',
		'Nevada',
		'New Hampshire',
		'New Jersey',
		'New Mexico',
		'New York',
		'North Carolina',
		'North Dakota',
		'Ohio',
		'Oklahoma',
		'Oregon',
		'Pennsylvania',
		'Rhode Island',
		'South Carolina',
		'South Dakota',
		'Tennessee',
		'Texas',
		'Utah',
		'Vermont',
		'Virginia',
		'Washington',
		'West Virginia',
		'Wisconsin',
		'Wyoming',
	);
}

/**
 * Get an array of property types.
 *
 * @return array
 */
function hre_get_property_type_options(): array {
	return array( 'Condo', 'Single Family', 'Townhouse' );
}

/**
 * Hide side bar everywhere on the site.
 *
 * @return void
 */
function hre_add_style_to_hide_sidebar() {
	wp_add_inline_style(
		'dashicons',
		' .bt-sidebar-wrap{ display:none !important; }'
	);
	wp_add_inline_script(
		'jquery',
		'jQuery(() => {jQuery(".bt-sidebar-wrap").siblings().removeClass("col-lg-8").removeClass("bt-content-wrap");})'
	);
}

/**
 * Add styles to hide search bar for user who are not admin, buyer or seller.
 *
 * @return void
 */
function hre_add_style_to_hide_search_bar() {
	$user_id = get_current_user_id();
	$user    = get_user_by( 'ID', $user_id );
	if ( ( $user instanceof \WP_User ) ) {
		$user_role = $user->roles[0];
		if ( current_user_can( 'manage_options' ) ) {
			return; // show for admins.
		}
		$accepted_roles = array(
			Settings::USER_ROLE_BUYER,
			Settings::USER_ROLE_SELLER,
		);
		if ( in_array( $user_role, $accepted_roles, true ) ) {
			return; // show for buyers and sellers.
		}
	}

	wp_add_inline_style(
		'dashicons',
		'section#desktop-header-search{ display:none !important; }'
	);

	// remove the search bar from the header.
	wp_add_inline_script(
		'jquery',
		'jQuery(() => {jQuery("section#desktop-header-search").remove();})'
	);
}

function hre_add_style_to_modify_height_of_elementor_carousel_images() {
//		 .swiper-image-stretch .swiper-slide .swiper-slide-image {
//		    width: 100% !important;
//		    height: 115vh;
//		}
	wp_add_inline_style(
		'dashicons',
		' .swiper-image-stretch .swiper-slide .swiper-slide-image {
		    width: 100% !important;
		    height: 115vh;
		}'
	);
}


/**
 * Add script to scroll up to woocommerce notice.
 *
 * @return void
 */
function hre_add_script_to_scroll_up_to_woocommerce_notice() {
	wp_add_inline_script(
		'jquery',
		'jQuery(() => {jQuery("body").on("click", ".woocommerce-checkout .button", function(){jQuery("html, body").animate({scrollTop: jQuery(".woocommerce-NoticeGroup").offset().top - 100}, 1000);});})'
	);
}

/**
 * Add style to hide woocommerce coupon section.
 *
 * @return void
 */
function hre_add_style_to_hide_woocommerce_coupon_section() {
	wp_add_inline_style(
		'dashicons',
		'.woocommerce-form-coupon-toggle{display:none !important}'
	);
}


/**
 * Add style to hide shipping option in checkout page.
 *
 * @return void
 */
function hre_add_style_to_hide_shipping_option_in_checkout_page() {
	wp_add_inline_style(
		'dashicons',
		'tr.woocommerce-shipping-totals{display:none !important;}'
	);
}

function hre_add_custom_styles() {
	hre_add_styles_to_hide_phone_number_in_menu();
}

function hre_add_styles_to_hide_phone_number_in_menu() {
	wp_add_inline_style(
		'dashicons',
		'.login-register-nav span.btn-phone-number{ display:none !important; }'
	);
}

/**
 * Get a list of all the agents for display in seller's signup form.
 *
 * @return array
 */
function hre_get_all_agents_list_for_display(): array {
	$all_agents = array();

	$users = get_users(
		array(
			'role' => Settings::USER_ROLE_AGENT,
		)
	);

	foreach ( $users as $one_user ) {
		$all_agents[] = array(
			'id'   => $one_user->ID,
			'name' => $one_user->display_name,
		);
	}

	return $all_agents;
}
