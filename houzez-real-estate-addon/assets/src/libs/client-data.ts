import { BuyerPreference } from "../my-types";

declare let hr3GeneralClientDataPereereDotCom;

export type ClientData = {
  ajax_url: string;
  nonce: string;
  rest_nonce: string;
  isAdmin: boolean;
  userId: number;
  admin_settings: {
    buyer_elite_access_fee: number;
    seller_elite_access_fee: number;
    buyer_elite_signup_page_id: number;
    buyer_elite_login_page_id: number;
    seller_elite_signup_page_id: number;
    seller_elite_login_page_id: number;
    agent_login_page_id: number;
    agent_signup_page_id: number;
    property_agreement_page_id: number;
    buyer_dashboard_page_id: number;
    buyer_onboarding_process_1_page_id: number;
    default_agreement: string; // Assuming default_agreement is a string, you can adjust the type accordingly
    default_agreement2: string; // Assuming default_agreement2 is a string, you can adjust the type accordingly
    search_by_map_page_id: number;
    terms_and_conditions_page_id: number;
    elite_membership_duration_months: number;
    seller_onboarding_process_1_page_id: number;
    seller_elite_page_id: number;
    buyer_elite_page_id: number;
    buyer_onboarding_process_2_page_id: number;
    google_captcha_site_key: string;
    google_captcha_secret_key: string;
    create_listing_page_id: number;
    buyer_preference_page_id: number;
  };
  client_settings: {
    buyer_preference_page_url: string;
    buyer_elite_login_page_url: string;
    buyer_elite_signup_page_url: string;
    seller_elite_login_page_url: string;
    create_listing_page_url: string;
    seller_elite_signup_page_url: string;
    buyer_dashboard_url: string;
    buyer_elite_access_fee: number;
    buyer_elite_access_fee_number: number;
    seller_elite_access_fee: number;
    seller_elite_access_fee_number: number;
    currency_symbol: string;
    agreement_page_url: string;
    terms_and_conditions_page_url: string;
    search_by_map_page_url: string;
    elite_membership_duration: number;
    seller_elite_page_url: string;
    buyer_elite_page_url: string;
    user_elite_fee_has_expired: boolean;
    elite_role: "buyer" | "seller" | "none";
    buyer_onboarding_process_2_page_url: string;
    agent_signup_page_url: string;
    agent_login_page_url: string;
    google_captcha_site_key: string;
  };
  wp_pages: {
    id: number;
    title: string;
    url: string;
  }[];
  isLoggedIn: boolean;
  inAdmin: boolean;
  clientTranslations: {
    [key: string]: string;
  };
  woo_checkout_url: string;
  site_url: string;
  buyer_preference: BuyerPreference;
  all_agents: { id: number; name: string }[];
};

export function getClientData(): ClientData {
  return hr3GeneralClientDataPereereDotCom;
}
