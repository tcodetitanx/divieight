import { ConvertedAgreement } from "./Services/AgreementService";

export namespace CalendarNamespace {
  export type Calendar = {
    year: string;
    month: string;
    days: CalendarDay[];
    yearsAndMonths: CalendarYearAndMonth[];
    topWeekDays: CalendarTopWeekDay[];
  };

  export type CalendarDay = {
    date: string;
    availableDay: AvailableDay;
    position: number;
    inThisMonth: boolean;
    dateDay: number;
    weekDay: number;
    weekDayLongName: string;
    weekDayShortName: string;
  };

  export type CalendarYearAndMonth = {
    year: number;
    months: CalendarMonth[];
  };

  export type CalendarMonth = {
    monthNumber: number;
    monthName: string;
  };

  export type CalendarTopWeekDay = {
    weekDay: number;
    weekDayLongName: string;
    weekDayShortName: string;
  };

  export interface FetchCalendarQuery {
    year: number;
    month: number;
    noOfYears: number;
  }
}

export type AvailableDay = {
  date: string;
  services: Service[];
  timeRanges: TimeRange[];
  allServicesAvailable: boolean;
};

export type CreateAvailableDayPayload = {
  date: string;
  service_ids: number[];
  time_ranges: TimeRange[];
  all_services_available: boolean;
};

export type Service = {
  id: number;
  timingInMinutes: number;
};

export type TimeRange = {
  id: number;
  start: number;
  stop: number;
};

export type WpService = {
  term_id: number;
  name: string;
  timingInMinutes: number;
};

export type Booking = {
  id: number;
  created: string;
  status: BookingStatus;
  qrcode_url: string;
  booking_services: BookingService[];
  remarks: BookingRemark[];
  loyalty_points: LoyaltyPoint[];
  signature_url?: string;
};

export type BookingService = {
  id: number;
  service_id: number;
  service_name: string;
  date: string;
  start_time: number;
  stop_time: number;
  service_timing_in_minutes: number;
};

export type BookingRemark = {
  id: number;
  date: string;
  content: string;
  booking_id: number;
};

export interface CreateBookingRemarkPayload {
  bookingId: number;
  content: string;
}

export type BookingStatus = "all" | "pending" | "handled" | "complete";

export interface FetchBookingsQueryPayload extends BatchQueryArgs {
  status: BookingStatus;
}

export type FetchBookingsQueryResponse = {
  bookings: Booking[];
  found_posts: number;
};

export type LoyaltyPoint = {
  id: number;
  date: string;
  amount_html: string;
  amount: number;
  description: string;
  status: LoyaltyPointStatus;
};

export type FetchLoyaltyPointsQueryResponse = {
  loyalty_points: LoyaltyPoint[];
  found_posts: number;
};

export interface FetchLoyaltyPointsQueryPayload extends BatchQueryArgs {
  status: LoyaltyPointStatus | "all";
  user_id: number;
}

export type LoyaltyPointStatus = "available" | "used" | "all";

export type LoyaltyPointStats = {
  total: number;
  total_html: string;
  used: number;
  used_html: string;
  available: number;
  available_html: string;
};

export interface BatchQueryArgs {
  posts_per_page: number;
  page: number;
}

export interface FetchAttachSignatureToBookingPayload {
  bookingId: number;
  signatureUrl: string;
}

export interface FetchSaveBookingSettingsPayload {
  status: BookingStatus;
  id: number;
}

export interface FetchCreateLoyaltyPointPayload {
  amount: number;
  bookingId: number;
  description: string;
  user_id: number;
}

export interface JapUser {
  id: number;
}

export interface SignupData {
  email: string;
  username: string;
  first_name: string;
  last_name: string;
  password: string;
  password_confirm: string;
}

export interface BuyerRefererDetails {
  was_referred: "yes" | "no" | "other_means";
  referrer_full_name: string;
  referrer_email: string;
  referrer_phone: string;
  referrer_is_agent_or_broker: "yes" | "no";
  referrer_is_agent_or_broker_confirmation: "yes" | "no";
}

export interface EliteSignupData {
  email: string;
  username: string;
  full_name: string;
  phone: string;
  password: string;
  state: string;
  for: "buyer" | "seller" | "agent";
  // agent info.
  seller_agent_id: number;
  seller_agent_doesnt_exists: "yes" | "no";
  seller_agent_first_name: string;
  seller_agent_last_name: string;
  seller_agent_phone: string;
  seller_agent_state: string;
}

export interface AgentSignupData {
  first_name: string;
  last_name: string;
  licensed_agent: "yes" | "no";
  username: string;
  license_state: string;
  state: string;
  license_number: string;
  name_of_agency: string;
  city: string;
  zip_code: string;
  name_of_principal_broker: string;
  email: string;
  phone: string;
  phone_landline: string;
  password: string;
  confirm_password: string;
  recatpcha_token: string;
}

export interface ResetPasswordPayload {
  username: string;
  otp: string;
  password: string;
}

export type BuyerApplication = {
  id: number;
  property_id: number;
  buyer_id: number;
  property_name: string;
  created: string;
  updated: string;
  property_url: string;
};

export interface CreateBuyerApplicationPayload {
  property_id: number;
  signature_url: string;
  agreement_1_inputs: ConvertedAgreement["inputs"];
  agreement_2_inputs: ConvertedAgreement["inputs"];
}

export type BuyerPreference = {
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  first_choice: string;
  state: string;
  preferred_budget: string;
  no_of_1_8th_interest: string;
  do_you_need_recommendation_for_buyer_agent: string;
  comment: string;
};

export type AgentDetails = {
  first_name: string;
  last_name: string;
  licensed_agent: "yes" | "no";
  username: string;
  license_state: string;
  state: string;
  license_number: string;
  name_of_agency: string;
  city: string;
  zip_code: string;
  name_of_principal_broker: string;
  email: string;
  phone: string;
};

export type UserDebugInfoType = {
  custom_current_datetime: string;
};

export type Inquiry = {
  id: number;
  created_at: string;
  updated_at: string;
  inquiry_data: InquiryData;
};

export type InquiryData = {
  inquiry_type: string;
  information: {
    i_am: string;
    first_name: string;
    last_name: string;
    email: string;
    phone: string;
  };
  location: {
    country: string;
    state: string;
    city: string;
    area: string;
    zip_code: string;
  };
  property: {
    type: string;
    max_price: string;
    min_size: string;
    number_of_bedrooms: string;
    number_of_bathrooms: string;
  };
  message: string;
  agreed_to_terms: "yes" | "no";
};
