<?php
/**
 * The settings file.
 *
 * @package HRE_Addon
 */

namespace HRE_Addon\Libs;

/**
 * Class Settings
 */
class Settings {


	// Rest API.
	public const REST_NAMESPACE = 'hre-addon/v1';

	// Shortcodes.

	// Const.
	public const DEBUG_MODE = true;

	// User roles.
	public const USER_ROLE_BUYER = 'houzez_buyer';
	public const USER_ROLE_SELLER = 'houzez_seller';
	public const USER_ROLE_AGENT = 'houzez_agent';

	// Options.
	public const OPTION_BUYER_ELITE_ACCESS_FEE = 'hre_buyer_application_fee';
	public const OPTION_SELLER_ELITE_ACCESS_FEE = 'hre_seller_application_fee';
	public const OPTION_BUYER_ELITE_SIGNUP_PAGE_ID = 'hre_elite_signup_page';
	public const OPTION_BUYER_ELITE_LOGIN_PAGE_ID = 'hre_elite_buyer_login_page_id';
	public const OPTION_CREATE_LISTING_PAGE_ID = 'hre_create_listing_page_id';
	public const OPTION_BUYER_PREFERENCE_PAGE_ID = 'hre_buyer_preference_page_id';
	public const OPTION_SELLER_ELITE_SIGNUP_PAGE_ID = 'hre_elite_seller_signup_page';
	public const OPTION_SELLER_ELITE_LOGIN_PAGE_ID = 'hre_elite_seller_login_page_id';
	public const OPTION_SEARCH_BY_MAP_PAGE_ID = 'hre_search_by_map_page_id';
	public const OPTION_PROPERTY_AGREEMENT_PAGE_ID = 'hre_property_agreement_page_id';
	public const OPTION_BUYER_DASHBOARD_PAGE_ID = 'hre_buyer_dashboard_page_id';
	public const OPTION_TERMS_AND_CONDITIONS_PAGE_ID = 'hre_terms_and_conditions_page_id';
	public const OPTION_BUYER_ONBOARDING_PROCESS_1_PAGE_ID = 'hre_buyer_onboarding_process_1';
	public const OPTION_BUYER_ONBOARDING_PROCESS_2_PAGE_ID = 'hre_buyer_onboarding_process_2';

	public const OPTION_SELLER_ONBOARDING_PROCESS_1_PAGE_ID = 'hre_buyer_onboarding_process_1';
	public const OPTION_DEFAULT_AGREEMENT_1 = 'hre_default_agreement';
	public const OPTION_DEFAULT_AGREEMENT_2 = 'hre_default_agreement_2';
	public const OPTION_ELITE_MEMBERSHIP_DURATION_MONTHS = 'hre_elite_membership_duration_months';
	public const OPTION_SELLER_ELITE_PAGE_ID = 'hre_seller_elite_page_id';
	public const OPTION_BUYER_ELITE_PAGE_ID = 'hre_buyer_elite_page_id';
	public const OPTION_AGENT_LOGIN_PAGE_ID = 'hre_agent_login_page_id';
	public const OPTION_AGENT_SIGNUP_PAGE_ID = 'hre_agent_signup_page_id';
	public const OPTION_GOOGLE_CAPTCHA_SITE_KEY = 'hre_google_captcha_site_key';
	public const OPTION_GOOGLE_CAPTCHA_SECRET_KEY = 'hre_google_captcha_secret_key';

	/**
	 * Can be 'buyer' or 'seller'.
	 */
	public const PM_IS_BUYER_APPLICATION_FOR = 'hre_is_buyer_application_for';
	// Cookies.

	// Taxonomies.

	// Term Meta.

	// Transient keys.

	// Array keys.

	// Menu Slugs.

	// Post types.
	public const POST_TYPE_BUYER_APPLICATION = 'hre_buyer_appl';
	public const POST_TYPE_INQUIRY = 'hre_inquiry';

	// Status.

	// Post meta.
	public const PM_PROPERTY_AGREEMENT = 'hre_property_agreement';
	public const PM_PROPERTY_AGREEMENT_2 = 'hre_property_agreement_2';
	/**
	 * Can be 'simple' or 'complex'.
	 */
	public const PM_PROPERTY_SIGN_MODE = 'hre_property_sign_mode';

	/**
	 * The meta key for is buyer application.
	 * Can be 'yes' or 'no'.
	 *
	 * @var string
	 */
	public const PM_IS_BUYER_APPLICATION_PRODUCT = 'hre_is_buyer_application_product';
	public const PM_IS_BUYER_APPLICATION_USER_NAME = 'hre_is_buyer_application_user_name';
	public const PM_IS_BUYER_APPLICATION_USER_EMAIL = 'hre_is_buyer_application_user_email';
	public const PM_IS_BUYER_APPLICATION_USER_PHONE = 'hre_is_buyer_application_user_phone';
	public const PM_IS_BUYER_APPLICATION_USER_STATE = 'hre_is_buyer_application_user_state';
	public const PM_IS_BUYER_APPLICATION_USER_PASSWORD = 'hre_is_buyer_application_user_password';
	public const PM_IS_BUYER_APPLICATION_FULL_NAME = 'hre_is_buyer_application_full_name';
	/**
	 * Can be 'simple' or 'complex'.
	 */
	public const PM_IS_BUYER_APPLICATION_SIGN_MODE = 'hre_is_buyer_application_sign_mode';
	/**
	 * Holds the current agreement 1 at the point the user is applying.
	 */
	public const PM_IS_BUYER_APPLICATION_AGREEMENT_1 = 'hre_is_buyer_application_agreement_1';
	/**
	 * Holds the current agreement 2 at the point the user is applying.
	 */
	public const PM_IS_BUYER_APPLICATION_AGREEMENT_2 = 'hre_is_buyer_application_agreement_2';
	/**
	 * An object or associative array of the agreement 1 inputs. Keys are input names and values are input values.
	 */
	public const PM_IS_BUYER_APPLICATION_AGREEMENT_1_INPUTS = 'hre_is_buyer_application_agreement_1_inputs';
	/**
	 * An object or associative array of the agreement 2 inputs. Keys are input names and values are input values.
	 */
	public const PM_IS_BUYER_APPLICATION_AGREEMENT_2_INPUTS = 'hre_is_buyer_application_agreement_2_inputs';
	/**
	 * Holds the buyer id if the buyer account is created.
	 *
	 * @var string
	 */
	public const PM_IS_ELITE_ACCOUNT_CREATED_USER_ID = 'hre_is_elite_account_created_user_id';
	/**
	 * Stores the inquiry for data as array.
	 *
	 * @var string
	 */
	public const PM_INQUIRY_DATA = 'hre_inquiry_data';

	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_ID = 'hre_is_buyer_application_seller_agent_id';
	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_DOESNT_EXIST = 'hre_is_buyer_application_seller_agent_doesnt_exist';
	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_FIRST_NAME = 'hre_is_buyer_application_seller_agent_first_name';
	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_LAST_NAME = 'hre_is_buyer_application_seller_agent_last_name';
	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_PHONE = 'hre_is_buyer_application_seller_agent_phone';
	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_STATE_OF_RESIDENCE = 'hre_is_buyer_application_seller_agent_state_of_residence';


	// Cart item meta.

	// Order Item meta.

	// Term meta.

	// Transient keys.

	// User meta.
	public const UM_PROFILE_PICTURE_ID = 'hre_profile_picture_id';
	public const UM_BANNER_PICTURE_ID = 'hre_banner_picture_id';
	public const UM_PASSWORD_RESET_OTP = 'hre_password_reset_otp';
	public const UM_PASSWORD_RESET_ATTEMPTS = 'hre_password_reset_attempts';

	public const UM_BP_FIRST_NAME = 'hre_buyer_preference_first_name';
	public const UM_BP_LAST_NAME = 'hre_buyer_preference_last_name';
	public const UM_BP_EMAIL = 'hre_buyer_preference_email';
	public const UM_BP_PHONE = 'hre_buyer_preference_phone';
	public const UM_BP_STATE = 'hre_buyer_preference_state';
	public const UM_BP_PREFERRED = 'hre_buyer_preference_preferred_budget';
	public const UM_BP_NO_OF_1_8TH_INTEREST = 'hre_buyer_preference_no_of_1_8th_interest';
	public const UM_BP_FIRST_CHOICE = 'hre_buyer_preference_first_choice';
	public const UM_BP_NEED_RECOMMENDATION_FOR_BUYER_AGENT = 'hre_buyer_preference_need_recommendation_for_buyer_agent';
	public const UM_BP_COMMENT = 'hre_buyer_preference_comment';


	public const UM_BUYER_APPLICATION_FEE_PRODUCT_ID = 'hre_buyer_application_fee_product_id';
	/**
	 * The meta key for is buyer application.
	 * Can be 'yes' or 'no'.
	 *
	 * @var string
	 */
	public const UM_BUYER_APPLICATION_PAID = 'hre_buyer_application_paid';
	public const UM_ELITE_MEMBERSHIP_PAID_DATE = 'hre_elite_membership_paid_date';
	public const UM_BUYER_PHONE = 'hre_buyer_phone';
	public const UM_BUYER_STATE = 'hre_buyer_state';

	public const UM_USER_DEBUG_CUSTOM_CURRENT_DATETIME = 'hre_user_debug_custom_datetime';
	public const UM_AGENT_LICENSE_STATE = 'hre_agent_license_state';
	public const UM_AGENT_LICENSE_NUMBER = 'hre_agent_license_number';
	public const UM_AGENT_NAME_OF_AGENCY = 'hre_agent_name_of_agency';
	public const UM_AGENT_CITY = 'hre_agent_city';
	public const UM_AGENT_ZIP_CODE = 'hre_agent_zip_code';
	public const UM_AGENT_NAME_OF_PRINCIPAL_BROKER = 'hre_agent_name_of_principal_broker';
	public const UM_AGENT_PHONE = 'hre_agent_phone';
	public const UM_AGENT_PHONE_LANDLINE = 'hre_agent_phone_landline';
	public const UM_AGENT_STATE = 'hre_agent_state';
	/**
	 * Can be 'yes' or 'no'.
	 */
	public const UM_AGENT_IS_LICENSED = 'hre_agent_licensed';
	public const UM_BUYER_WAS_REFERRED = 'hre_buyer_was_referred';
	public const UM_BUYER_REFERRER_FULL_NAME = 'hre_buyer_referrer_full_name';
	public const UM_BUYER_REFERRER_EMAIL = 'hre_buyer_referrer_email';
	public const UM_BUYER_REFERRER_PHONE = 'hre_buyer_referrer_phone';
	public const UM_BUYER_REFERRER_IS_AGENT_OR_BROKER = 'hre_buyer_referrer_is_agent_or_broker';
	public const UM_BUYER_REFERRER_IS_AGENT_OR_BROKER_CONFIRMATION = 'hre_buyer_referrer_is_agent_or_broker_confirmation';


// 	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_ID = 'hre_is_buyer_application_seller_agent_id';
//	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_DOESNT_EXIST = 'hre_is_buyer_application_seller_agent_id';
//	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_FIRST_NAME = 'hre_is_buyer_application_seller_agent_first_name';
//	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_LAST_NAME = 'hre_is_buyer_application_seller_agent_last_name';
//	public const PM_IS_BUYER_APPLICATION_SELLER_AGENT_PHONE = 'hre_is_buyer_application_seller_agent_phone';
	public const UM_SELLER_AGENT_ID = 'hre_seller_agent_id';
	public const UM_SELLER_AGENT_DOESNT_EXIST = 'hre_seller_agent_doesnt_exist';
	public const UM_SELLER_AGENT_FIRST_NAME = 'hre_seller_agent_first_name';
	public const UM_SELLER_AGENT_LAST_NAME = 'hre_seller_agent_last_name';
	public const UM_SELLER_AGENT_PHONE = 'hre_seller_agent_phone';
	public const UM_SELLER_AGENT_STATE_OF_RESIDENCE = 'hre_seller_agent_state_of_residence';


	// <editor-fold desc='User Meta'>

	// </editor-fold>

	// Client Side data.

}
