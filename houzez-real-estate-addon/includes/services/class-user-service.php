<?php
/**
 * User Service.
 *
 * @package HRE_Addon\Includes\Rest_Api;
 */

namespace HRE_Addon\Includes\Rest_Api;

use HRE_Addon\Libs\Settings;
use WP_Error;
use WP_User;

if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

/**
 * Class User_Service.
 */
class User_Service {
	/**
	 * The ID of the job offer.
	 *
	 * @var int $id The ID of the job offer.
	 */
	private int $id = 0;

	/**
	 * The user;
	 *
	 * @var Wp_User $user The user;
	 */
	private Wp_User $user;

	/**
	 * Constructor
	 *
	 * @return void
	 */
	public function __construct() {
	}

	public function init(): self {
		return $this;
	}

	/**
	 * Read the item from the database.
	 *
	 * @return self | \WP_Error The item or an error.
	 */
	public function read_from_db() {
		$user = get_user_by( 'ID', $this->get_id() );

		if ( ! $user ) {
			return new \WP_Error(
				'user_not_found',
				__( 'User not found.', 'hre-addon' ),
				array(
					'id' => $this->get_id(),
				)
			);
		}

		$this->set_id( $user->ID );
		$this->set_user( $user );

		return $this;
	}

	/**
	 * Get all cities for all agents.
	 *
	 * @return array
	 */
	public static function get_all_cities_for_all_agents(): array {
		$all = array();

		// Get all users who has the role of agent.
		$users = get_users( array(
			'role' => Settings::USER_ROLE_AGENT,
		) );
		foreach ( $users as $user ) {
			$city = get_user_meta( $user->ID, Settings::UM_AGENT_CITY, true );
			if ( ! in_array( $city, $all, true ) ) {
				$all[] = $city;
			}
		}

		return $all;
	}

	/**
	 * Get all state for all agents.
	 *
	 * @return array
	 */
	public static function get_all_states_for_all_agents(): array {
		$all = array();

		// Get all users who has the role of agent.
		$users = get_users( array(
			'role' => Settings::USER_ROLE_AGENT,
		) );
		foreach ( $users as $user ) {
			$state = get_user_meta( $user->ID, Settings::UM_AGENT_STATE, true );
			if ( ! in_array( $state, $all, true ) ) {
				$all[] = $state;
			}
		}

		return $all;
	}

	/**
	 * Get all zip code for all agents.
	 *
	 * @return array
	 */
	public static function get_all_zip_codes_for_all_agents(): array {
		$all = array();

		// Get all users who has the role of agent.
		$users = get_users( array(
			'role' => Settings::USER_ROLE_AGENT,
		) );
		foreach ( $users as $user ) {
			$zip_code = get_user_meta( $user->ID, Settings::UM_AGENT_ZIP_CODE, true );
			if ( ! in_array( $zip_code, $all, true ) ) {
				$all[] = $zip_code;
			}
		}

		return $all;
	}

	/**
	 * Get buyer details.
	 *
	 * @param int $buyer_id The ID of the buyer.
	 *
	 * @return array|WP_Error Buyer details if found, WP_Error otherwise.
	 */
	public function get_buyer_details( int $buyer_id ) {
		// Check if the buyer ID is valid
		if ( ! $buyer_id ) {
			return new WP_Error( 'invalid_buyer_id', 'Invalid buyer ID.' );
		}

		// Retrieve the buyer data
		$buyer_data = get_userdata( $buyer_id );

		// Check if buyer data exists
		if ( ! $buyer_data ) {
			return new WP_Error( 'buyer_not_found', 'Buyer not found.' );
		}

		// Return the buyer details
		return array(
			'username'                            => $buyer_data->user_login,
			'email'                               => $buyer_data->user_email,
			'first_name'                          => $buyer_data->first_name,
			'last_name'                           => $buyer_data->last_name,
			'was_referred'                        => get_user_meta( $buyer_id, Settings::UM_BUYER_WAS_REFERRED, true ),
			'referrer_full_name'                  => get_user_meta( $buyer_id, Settings::UM_BUYER_REFERRER_FULL_NAME, true ),
			'referrer_email'                      => get_user_meta( $buyer_id, Settings::UM_BUYER_REFERRER_EMAIL, true ),
			'referrer_phone'                      => get_user_meta( $buyer_id, Settings::UM_BUYER_REFERRER_PHONE, true ),
			'referrer_is_agent_or_broker'         => get_user_meta( $buyer_id, Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER, true ),
			'referrer_is_agent_or_broker_confirm' => get_user_meta( $buyer_id, Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER_CONFIRMATION, true ),
		);
	}


	/**
	 * Signup a buyer.
	 *
	 * @param string $username The username.
	 * @param string $email The email.
	 * @param string $first_name The first name.
	 * @param string $last_name The last name.
	 * @param string $password The password.
	 *
	 * @return self | \WP_Error The item or an error.
	 */
	public function signup(
		string $username,
		string $email,
		string $first_name,
		string $last_name,
		string $password,
		int $profile_picture_id,
		int $banner_picture_id,
		array $teacher_subjects,
		string $phone,
		string $bio
	) {
		$userdata = array(
			'user_login'       => $username,
			'user_email'       => $email,
			'user_pass'        => $password,
			'first_name'       => $first_name,
			'last_name'        => $last_name,
			'role'             => 'subscriber',
			'teacher_subjects' => array(),
		);

		$user_id = wp_insert_user( $userdata );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Invalidate the cache.
		$this->invalidate_cache();

		// Set the ID.
		$this->set_id( $user_id );

		update_user_meta( $user_id, Settings::UM_BANNER_PICTURE_ID, $banner_picture_id );
		update_user_meta( $user_id, Settings::UM_PROFILE_PICTURE_ID, $profile_picture_id );

		return $this;
	}


	/**
	 * Signup a buyer.
	 *
	 * @param string $username The username.
	 * @param string $email The email.
	 * @param string $full_name The full name.
	 * @param string $phone The last name.
	 * @param string $password The password.
	 * @param string $state The password.
	 * @param string $was_referred Indicates whether the user was referred.
	 * @param string $referrer_full_name The full name of the referrer.
	 * @param string $referrer_email The email of the referrer.
	 * @param string $referrer_phone The phone number of the referrer.
	 * @param string $referrer_is_agent_or_broker Indicates whether the referrer is an agent or broker.
	 * @param string $referrer_is_agent_or_broker_confirmation Confirmation of whether the referrer is an agent or broker.
	 *
	 * @return self | \WP_Error The item or an error.
	 */
	public function signup_buyer(
		string $username,
		string $email,
		string $full_name,
		string $phone,
		string $password,
		string $state,
		string $was_referred,
		string $referrer_full_name,
		string $referrer_email,
		string $referrer_phone,
		string $referrer_is_agent_or_broker,
		string $referrer_is_agent_or_broker_confirmation
	) {
		$userdata = array(
			'user_login'       => $username,
			'user_email'       => $email,
			'user_pass'        => $password,
			'first_name'       => $full_name,
			'last_name'        => '',
			'role'             => Settings::USER_ROLE_BUYER,
			'teacher_subjects' => array(),
			'meta_input'       => array(
				Settings::UM_BUYER_PHONE                                    => $phone,
				Settings::UM_BUYER_STATE                                    => $state,
				Settings::UM_BUYER_REFERRER_FULL_NAME                       => $referrer_full_name,
				Settings::UM_BUYER_WAS_REFERRED                             => $was_referred,
				Settings::UM_BUYER_REFERRER_EMAIL                           => $referrer_email,
				Settings::UM_BUYER_REFERRER_PHONE                           => $referrer_phone,
				Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER              => $referrer_is_agent_or_broker,
				Settings::UM_BUYER_REFERRER_IS_AGENT_OR_BROKER_CONFIRMATION => $referrer_is_agent_or_broker_confirmation,
			)
		);

		$user_id = wp_insert_user( $userdata );
		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		// Invalidate the cache.
		$this->invalidate_cache();

		// Set the ID.
		$this->set_id( $user_id );

		return $this;
	}


	/**
	 * Login a user.
	 *
	 * @param string $username The username or email.
	 * @param string $password The password.
	 *
	 * @return self | \WP_Error The item or an error.
	 */
	public function login( string $username, string $password ) {
		$user = get_user_by( 'email', $username );
		if ( ! ( $user instanceof \WP_User ) ) {
			$user_by_login = get_user_by( 'login', $username );
			if ( ! ( $user_by_login instanceof \WP_User ) ) {
				return new WP_Error(
					'user_not_found',
					__( 'User not found.', 'hre-addon' ),
					array(
						'username' => $username,
					)
				);
			}
			$user = $user_by_login;
		}

		$user_login = $user->data->user_login;
		$sign_in    = wp_signon(
			array(
				'user_login'    => $user_login,
				'user_password' => $password,
				'remember'      => true,
			)
		);

		if ( is_wp_error( $sign_in ) ) {
			return $sign_in;
		}

		wp_clear_auth_cookie();
		wp_set_current_user( $user->ID ); // Set the current user detail
		wp_set_auth_cookie( $user->ID ); // Set auth details in cookie

		// Set the user id.
		$this->set_id( $user->ID );

		return $this;
	}

	/**
	 * Send OTP to user.
	 *
	 * @param string $username The username or email.
	 * @param int $max_attempts_allowed The allowed verify attempts in MINUTES.
	 * @param int $max_minutes_for_the_attempts The minutes allowed. Default 10 minutes.
	 *
	 * @return self | \WP_Error The item or an error.
	 */
	public function send_otp(
		string $username,
		int $max_attempts_allowed = 1,
		int $max_minutes_for_the_attempts = 10
	) {
		$user = get_user_by( 'email', $username );

		if ( ! ( $user instanceof \WP_User ) ) {
			$user_by_login = get_user_by( 'login', $username );
			if ( ! ( $user_by_login instanceof \WP_User ) ) {
				return new WP_Error(
					'user_not_found',
					__( 'User not found.', 'hre-addon' ),
					array(
						'username' => $username,
					)
				);
			}
			$user = $user_by_login;
		}

		$check_otp_limit = $this->check_otp_limit(
			$user->ID,
			$max_attempts_allowed,
			$max_minutes_for_the_attempts
		);

		if ( is_wp_error( $check_otp_limit ) ) {
			return $check_otp_limit;
		}

		$otp = wp_rand( 1000, 9999 ) . '';
		update_user_meta( $user->ID, Settings::UM_PASSWORD_RESET_OTP, (string) $otp );

		$to      = $user->user_email;
		$subject = __( 'OTP for login', 'hre-addon' );
		$body    = sprintf(
			'<div>
					<h3>' . __( 'Hi %1$s, Please use the code below to reset your password.', 'hre-addon' ) . '</h3>
					<br />
					<br />
					<b><h2><code>%2$s</code></h2></b>
					<br />
					<p><b>' . __(
				'Note',
				'hre-addon'
			) . ':</b> ' . __(
				'This code will expire within 10 minutes.',
				'hre-addon'
			) . '</p>
         </div>',
			$user->user_login,
			$otp
		);

		$sent = wp_mail(
			$to,
			$subject,
			$body,
			array(
				'Content-Type: text/html; charset=UTF-8',
			)
		);

		if ( ! $sent ) {
			return new WP_Error(
				'otp_not_sent',
				__( 'We could not send OTP to your email. Please try again later.', 'hre-addon' ),
				array(
					'username' => $username,
				)
			);
		}

		// Save the password reset attempts.
		$this->save_password_reset_attempts( $user->ID );

		return $this;
	}

	/**
	 * Verify OTP.
	 *
	 * @param string $username The username or email.
	 * @param string $otp The OTP.
	 * @param int $max_minute_before_expiration The minutes before expiration. Default 10 minutes.
	 *
	 * @return $this|\WP_Error
	 */
	public function verify_otp( string $username, string $otp, int $max_minute_before_expiration = 10 ) {
		$user = $this->get_user_by_username_or_email( $username );
		if ( is_wp_error( $user ) ) {
			return $user;
		}

		$otp_saved = get_user_meta( $user->ID, Settings::UM_PASSWORD_RESET_OTP, true );

		// Make sure the user does not use this OTP again after resetting the password.
		if ( empty( $otp_saved ) ) {
			return new WP_Error(
				'go_through_the_right_channel_to_change_your_password',
				__( 'You should go through the right channel', 'hre-addon' )
			);
		}

		// Make sure the OTP is correct.
		if ( $otp_saved !== $otp ) {
			return new WP_Error(
				'otp_not_matched',
				__( 'OTP does not match.', 'hre-addon' ),
				array(
					'username' => $username,
				)
			);
		}

		// Ensure the OTP has not expired.
		$otp_expiration = $this->check_otp_expiry( $user->ID, $otp, $max_minute_before_expiration );
		if ( is_wp_error( $otp_expiration ) ) {
			return $otp_expiration;
		}

		return $this;
	}

	/**
	 * Set new password.
	 * After the password is reset, the user's otp will be deleted.
	 *
	 * @param string $username The username.
	 * @param string $otp The otp.
	 * @param string $password The password.
	 *
	 * @return self|WP_Error
	 */
	public function reset_password( string $username, string $otp, string $password ) {
		$verify_otp = $this->verify_otp( $username, $otp );
		if ( is_wp_error( $verify_otp ) ) {
			return $verify_otp;
		}

		$user = $this->get_user_by_username_or_email( $username );

		$user_id = $user->ID;
		wp_set_password( $password, $user_id );

		// Delete the otp.
		update_user_meta( $user_id, Settings::UM_PASSWORD_RESET_OTP, '' );

		return $this;
	}

	/**
	 * Invalidate the cache.
	 *
	 * @return void
	 */
	public function invalidate_cache(): void {
		// Invalidate the cache.
	}

	/**
	 * Get the item as an array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		return array(
			'id'           => $this->get_id(),
			// Others.
			'user_details' => $this->get_user_details(),
		);
	}


	/**
	 * Get the buyer's preference .
	 *
	 * @param int $buyer_id The buyer's ID.
	 *
	 * @return array
	 */
	public static function get_buyer_preference( int $buyer_id ): array {
		$need = get_user_meta( $buyer_id, Settings::UM_BP_NEED_RECOMMENDATION_FOR_BUYER_AGENT, true );
		if ( ! in_array( $need, array( 'yes', 'no' ) ) ) {
			$need = 'no';
		}

		return array(
			'first_name'                                 => get_user_meta( $buyer_id,
				Settings::UM_BP_FIRST_NAME,
				true ),
			'last_name'                                  => get_user_meta( $buyer_id, Settings::UM_BP_LAST_NAME, true ),
			'email'                                      => get_user_meta( $buyer_id, Settings::UM_BP_EMAIL, true ),
			'phone'                                      => get_user_meta( $buyer_id, Settings::UM_BP_PHONE, true ),
			'state'                                      => get_user_meta( $buyer_id, Settings::UM_BP_STATE, true ),
			'preferred_budget'                           => get_user_meta( $buyer_id, Settings::UM_BP_PREFERRED, true ),
			'no_of_1_8th_interest'                       => get_user_meta( $buyer_id,
				Settings::UM_BP_NO_OF_1_8TH_INTEREST,
				true ),
			'do_you_need_recommendation_for_buyer_agent' => $need,
			'comment'                                    => get_user_meta( $buyer_id, Settings::UM_BP_COMMENT, true ),
			'first_choice'                               => get_user_meta( $buyer_id,
				Settings::UM_BP_FIRST_CHOICE,
				true ),
		);
	}

	/**
	 * Get the buyer's preference .
	 *
	 * @param int $agent_id The buyer's ID.
	 *
	 * @return array|WP_Error
	 */
	public static function get_agent_details( int $agent_id ): array {
		$agent = get_user_by( 'ID', $agent_id );
		if ( ! ( $agent instanceof WP_User ) ) {
			return new WP_Error(
				'agent_not_found',
				__( 'Agent not found.', 'hre-addon' ),
			);
		}

		// User must be a seller role.
		if ( Settings::USER_ROLE_AGENT !== $agent->roles[0] ) {
			return new WP_Error(
				'agent_not_agent',
				__( 'Agent is not an agent.', 'hre-addon' ),
			);
		}

		// update_user_meta( $user_id, Settings::UM_AGENT_IS_LICENSED, $licensed_agent );
		//		update_user_meta( $user_id, Settings::UM_AGENT_LICENSE_STATE, $license_state );
		//		update_user_meta( $user_id, Settings::UM_AGENT_STATE, $state );
		//		update_user_meta( $user_id, Settings::UM_AGENT_LICENSE_NUMBER, $license_number );
		//		update_user_meta( $user_id, Settings::UM_AGENT_NAME_OF_AGENCY, $name_of_agency );
		//		update_user_meta( $user_id, Settings::UM_AGENT_CITY, $city );
		//		update_user_meta( $user_id, Settings::UM_AGENT_ZIP_CODE, $zip_code );
		//		update_user_meta( $user_id, Settings::UM_AGENT_NAME_OF_PRINCIPAL_BROKER, $name_of_principal_broker );
		//		update_user_meta( $user_id, Settings::UM_AGENT_PHONE, $phone );
		return array(
			'first_name'               => $agent->first_name,
			'last_name'                => $agent->last_name,
			'email'                    => $agent->user_email,
			'phone'                    => get_user_meta( $agent_id, Settings::UM_AGENT_PHONE, true ),
			'phone_landline'           => get_user_meta( $agent_id, Settings::UM_AGENT_PHONE_LANDLINE, true ),
			'state'                    => get_user_meta( $agent_id, Settings::UM_AGENT_STATE, true ),
			'licensed_agent'           => get_user_meta( $agent_id, Settings::UM_AGENT_IS_LICENSED, true ),
			'username'                 => $agent->user_login,
			'license_state'            => get_user_meta( $agent_id, Settings::UM_AGENT_LICENSE_STATE, true ),
			'license_number'           => get_user_meta( $agent_id, Settings::UM_AGENT_LICENSE_NUMBER, true ),
			'name_of_agency'           => get_user_meta( $agent_id, Settings::UM_AGENT_NAME_OF_AGENCY, true ),
			'city'                     => get_user_meta( $agent_id, Settings::UM_AGENT_CITY, true ),
			'zip_code'                 => get_user_meta( $agent_id, Settings::UM_AGENT_CITY, true ),
			'name_of_principal_broker' => get_user_meta( $agent_id, Settings::UM_AGENT_NAME_OF_PRINCIPAL_BROKER, true ),
		);

	}

	/**
	 * Save the buyer's preference .
	 *
	 * @param int $buyer_id The buyer's ID.
	 * @param string $first_name The first name.
	 * @param string $last_name The last name.
	 * @param string $email The email.
	 * @param string $phone The phone.
	 * @param string $state The state.
	 * @param string $preferred_budget The preferred budget.
	 * @param string $no_of_1_8th_interest The number of 1/8th interest.
	 * @param string $need_recommendation_for_buyer_agent The need recommendation for buyer agent.
	 * @param string $comment The comment.
	 *
	 * @return void
	 */
	public function save_buyer_preference(
		int $buyer_id,
		string $first_name,
		string $last_name,
		string $email,
		string $phone,
		string $state,
		string $preferred_budget,
		string $no_of_1_8th_interest,
		string $need_recommendation_for_buyer_agent,
		string $comment,
		string $first_choice
	): void {
		// Update first name and last name in user table.
		$user             = get_user_by( 'ID', $buyer_id );
		$user->first_name = $first_name;
		$user->last_name  = $last_name;
		wp_update_user( $user );

		update_user_meta( $buyer_id, Settings::UM_BP_FIRST_NAME, $first_name );
		update_user_meta( $buyer_id, Settings::UM_BP_LAST_NAME, $last_name );
		update_user_meta( $buyer_id, Settings::UM_BP_EMAIL, $email );
		update_user_meta( $buyer_id, Settings::UM_BP_PHONE, $phone );
		update_user_meta( $buyer_id, Settings::UM_BP_STATE, $state );
		update_user_meta( $buyer_id, Settings::UM_BP_PREFERRED, $preferred_budget );
		update_user_meta( $buyer_id, Settings::UM_BP_NO_OF_1_8TH_INTEREST, $no_of_1_8th_interest );
		update_user_meta( $buyer_id,
			Settings::UM_BP_NEED_RECOMMENDATION_FOR_BUYER_AGENT,
			$need_recommendation_for_buyer_agent );
		update_user_meta( $buyer_id, Settings::UM_BP_COMMENT, $comment );
		update_user_meta( $buyer_id, Settings::UM_BP_FIRST_CHOICE, $first_choice );
	}

	/**
	 * Check whether the user is a buyer.
	 *
	 * @param int $user_id The buyer's ID.
	 *
	 * @return array
	 */
	public static function is_buyer( int $user_id ): bool {
		$role = get_user_meta( $user_id, 'wp_capabilities', true );
		if ( ! is_array( $role ) ) {
			return false;
		}

		return array_key_exists( Settings::USER_ROLE_BUYER, $role );
	}

	/**
	 * Signup agent.
	 *
	 * @param string $first_name The first name of the user.
	 * @param string $last_name The last name of the user.
	 * @param string $licensed_agent Specifies whether the user is a licensed agent ('yes' or 'no').
	 * @param string $username The username for the user.
	 * @param string $license_state The license state of the user.
	 * @param string $state The state of the user.
	 * @param string $license_number The license number of the user.
	 * @param string $name_of_agency The name of the agency of the user.
	 * @param string $city The city of the user.
	 * @param string $zip_code The ZIP code of the user.
	 * @param string $name_of_principal_broker The name of the principal broker of the user.
	 * @param string $email The email address of the user.
	 * @param string $phone The phone number of the user.
	 * @param string $password The password for the user.
	 * @param string $phone_landline The phone landline of the user.
	 *
	 * @return int|WP_Error The ID of the created user or a WP_Error object if the user creation fails.
	 */
	public function agent_signup(
		string $first_name,
		string $last_name,
		string $licensed_agent,
		string $username,
		string $license_state,
		string $state,
		string $license_number,
		string $name_of_agency,
		string $city,
		string $zip_code,
		string $name_of_principal_broker,
		string $email,
		string $phone,
		string $password,
		string $phone_landline,
	) {
		// make sure user doesn't exist yet.
		$user_by_email = get_user_by( 'email', $email );
		$user_by_login = get_user_by( 'login', $username );
		if ( $user_by_login instanceof \WP_User ) {
			return new WP_Error( 'username_exists', 'Username already exists' );
		}
		if ( $user_by_email instanceof \WP_User ) {
			return new WP_Error( 'email_exists', 'Email already exists' );
		}

		// Determine the user role based on the licensed agent status.
		$user_role = Settings::USER_ROLE_AGENT;

		// Insert the user into the WordPress database.
		$user_id = wp_insert_user(
			array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $email,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'role'       => $user_role,
			)
		);

		// Check if user creation was successful.
		if ( is_wp_error( $user_id ) ) {
			// Return the WP_Error object if user creation fails.
			return $user_id;
		}

		// Update user meta information.
		update_user_meta( $user_id, Settings::UM_AGENT_IS_LICENSED, $licensed_agent );
		update_user_meta( $user_id, Settings::UM_AGENT_LICENSE_STATE, $license_state );
		update_user_meta( $user_id, Settings::UM_AGENT_STATE, $state );
		update_user_meta( $user_id, Settings::UM_AGENT_LICENSE_NUMBER, $license_number );
		update_user_meta( $user_id, Settings::UM_AGENT_NAME_OF_AGENCY, $name_of_agency );
		update_user_meta( $user_id, Settings::UM_AGENT_CITY, $city );
		update_user_meta( $user_id, Settings::UM_AGENT_ZIP_CODE, $zip_code );
		update_user_meta( $user_id, Settings::UM_AGENT_NAME_OF_PRINCIPAL_BROKER, $name_of_principal_broker );
		update_user_meta( $user_id, Settings::UM_AGENT_PHONE, $phone );
		update_user_meta( $user_id, Settings::UM_AGENT_PHONE_LANDLINE, $phone_landline );

		// Return the ID of the created user.
		return $user_id;
	}


	// <editor-fold desc="SETTERS BEGIN">.

	/**
	 * Set the job offer's ID.
	 *
	 * @param int $id The job offer's ID.
	 *
	 * @return self
	 */
	public function set_id( int $id ): self {
		$this->id = $id;

		return $this;
	}

	/**
	 * Set the user.
	 *
	 * @param \WP_User $user The user.
	 *
	 * @return self
	 */
	public function set_user( \WP_User $user ): self {
		$this->user = $user;

		return $this;
	}

	// </editor-fold desc="SETTERS END">.

	// <editor-fold desc="GETTERS BEGIN">.

	/**
	 * Get the ID of the user.
	 *
	 * @return int
	 */
	public function get_id(): int {
		return $this->id;
	}

	/**
	 * Get user.
	 *
	 * @return null|\WP_User
	 */
	public function get_user() {
		return $this->user;
	}

	/**
	 * Get user details.
	 *
	 * @return array
	 */
	public function get_user_details(): array {
		$user_details = array(
			'username'   => '',
			'email'      => '',
			'first_name' => '',
			'last_name'  => '',
		);

		if ( $this->get_user() instanceof \WP_User ) {
			$user_details['username']   = $this->get_user()->user_login;
			$user_details['email']      = $this->get_user()->user_email;
			$user_details['first_name'] = $this->get_user()->first_name;
			$user_details['last_name']  = $this->get_user()->last_name;
		}

		return $user_details;
	}

	// </editor-fold desc="GETTERS END">.

	// <editor-fold desc="COUNTS BEGIN">.

	// </editor-fold desc="COUNTS BEGIN">.

	// <editor-fold desc="MISCELLANEOUS BEGIN">.

	/**
	 * Get the user by username or email.
	 *
	 * @param string $username The username or email.
	 *
	 * @return \WP_User|\WP_Error
	 */
	protected function get_user_by_username_or_email( string $username ) {
		$user = get_user_by( 'email', $username );

		if ( ! ( $user instanceof \WP_User ) ) {
			$user_by_login = get_user_by( 'login', $username );
			if ( ! ( $user_by_login instanceof \WP_User ) ) {
				return new WP_Error(
					'user_not_found',
					__( 'User not found.', 'hre-addon' ),
					array(
						'username' => $username,
					)
				);
			}
			$user = $user_by_login;
		}

		return $user;
	}

	/**
	 * Set password reset attempts.
	 *
	 * @param int $user_id The user ID.
	 *
	 * @return void
	 */
	protected function save_password_reset_attempts( int $user_id ): void {
		// Get the current utc timestamp.
		$current_time = strtotime( gmdate( 'Y-m-d H:i:s' ) );

		// Get the user meta for password reset attempts.
		$reset_attempts = get_user_meta( $user_id, Settings::UM_PASSWORD_RESET_ATTEMPTS, true );

		// Clear out any reset attempts that are older than 1 year.
		foreach ( $reset_attempts as $timestamp => $count ) {
			if ( $timestamp < strtotime( '-2 year' ) ) {
				unset( $reset_attempts[ $timestamp ] );
			}
		}

		// If the user meta doesn't exist or is not an array, initialize it.
		if ( empty( $reset_attempts ) || ! is_array( $reset_attempts ) ) {
			$reset_attempts = array();
		}

		// Increment the reset attempt count for the current timestamp.
		if ( isset( $reset_attempts[ $current_time ] ) ) {
			$reset_attempts[ $current_time ] ++;
		} else {
			$reset_attempts[ $current_time ] = 1;
		}

		// Save the updated reset attempts user meta.
		update_user_meta( $user_id, Settings::UM_PASSWORD_RESET_ATTEMPTS, $reset_attempts );
	}

	/**
	 * Count the number of password reset attempts made by a user within a specified time frame.
	 *
	 * @param int $user_id The ID of the user.
	 * @param int $minutes The number of hours to consider for counting the attempts.
	 *
	 * @return int The total number of password reset attempts.
	 */
	protected function count_password_reset_attempts( int $user_id, int $minutes ): int {
		// Get the current UTC timestamp.
		$current_time = strtotime( gmdate( 'Y-m-d H:i:s' ) );

		// Calculate the timestamp for the specified number of hours ago.
		$time_frame = $minutes * MINUTE_IN_SECONDS;
		$start_time = $current_time - $time_frame;

		// Get the user meta for password reset attempts.
		$reset_attempts = $this->get_password_reset_attempts( $user_id );

		// If the user meta doesn't exist or is not an array, return 0 attempts.
		if ( empty( $reset_attempts ) ) {
			return 0;
		}

		// Remove reset attempts older than the start time.
		$reset_attempts = array_filter(
			$reset_attempts,
			function ( $timestamp ) use ( $start_time ) {
				return $timestamp >= $start_time;
			},
			ARRAY_FILTER_USE_KEY
		);

		// Calculate the total number of reset attempts.
		return array_sum( $reset_attempts );
	}

	/**
	 * Get password reset attempts.
	 *
	 * @param int $user_id The ID of the user.
	 *
	 * @return array
	 */
	protected function get_password_reset_attempts( int $user_id ): array {
		// Get the user meta for password reset attempts.
		$reset_attempts = (array) get_user_meta( $user_id, Settings::UM_PASSWORD_RESET_ATTEMPTS, true );

		// If the user meta doesn't exist or is not an array, return 0 attempts.
		if ( empty( $reset_attempts ) ) {
			return array();
		}

		return $reset_attempts;
	}

	/**
	 * Check whether the maximum otp limit has been reached.
	 *
	 * @param int $user_id The ID of the user.
	 * @param int $max_attempts_allowed The maximum number of attempts allowed.
	 * @param int $max_minutes_for_the_attempts The number of minutes to consider for counting the attempts.
	 *
	 * @return bool|\WP_Error True if the limit has not been reached, otherwise a WP_Error object.
	 */
	protected function check_otp_limit( int $user_id, int $max_attempts_allowed, int $max_minutes_for_the_attempts ) {
		$verify_attempts = $this->count_password_reset_attempts( $user_id, $max_minutes_for_the_attempts );

		if ( $verify_attempts >= $max_attempts_allowed ) {
			return new WP_Error(
				'otp_limit_exceeded',
				__(
					'You have exceeded the limit of OTP verification attempts. Please try again after 10 minutes.',
					'hre-addon'
				),
			);
		}

		return true;
	}

	/**
	 * Check whether the otp has expired.
	 *
	 * @param int $user_id The ID of the user.
	 * @param string $otp The otp.
	 * @param int $minutes The number of minutes to consider for counting the attempts.
	 *
	 * @return bool|\WP_Error True if the otp has not expired, otherwise a WP_Error object.
	 */
	protected function check_otp_expiry( int $user_id, string $otp, int $minutes ) {
		// Get the user meta for password reset attempts.
		$otp_meta = get_user_meta( $user_id, Settings::UM_PASSWORD_RESET_OTP, true );

		// If the user meta doesn't exist or is not an array, return 0 attempts.
		if ( empty( $otp_meta ) ) {
			return new WP_Error(
				'otp_expired',
				__( 'The OTP has expired. Please try again.', 'hre-addon' ),
			);
		}

		$password_reset_attempts = $this->get_password_reset_attempts( $user_id );

		// Get last password reset attempt.
		$last_attempt_date_timestamp = array_key_last( $password_reset_attempts );

		if ( ! empty( $last_attempt_date_timestamp ) ) {
			$current_time = strtotime( gmdate( 'Y-m-d H:i:s' ) );

			$minutes_passed = ( $current_time - $last_attempt_date_timestamp ) / MINUTE_IN_SECONDS;

			if ( $minutes_passed > $minutes ) {
				return new WP_Error(
					'otp_invalid_or_expired',
					__( 'This OTP is invalid or has expired.' ),
					array(
						'minutes_parsed' => $minutes_passed,
					),
				);
			}
		}

		return true;
	}

	// </editor-fold desc="MISCELLANEOUS END">.

	// <editor-fold desc="VALIDATIONS BEGIN">.

	// </editor-fold desc="VALIDATIONS END">.

}
