import {
  BaseQueryFn,
  FetchArgs,
  FetchBaseQueryError,
  createApi,
  fetchBaseQuery,
} from "@reduxjs/toolkit/query/react";
import {
  ResetPasswordPayload,
  SignupData,
  JapUser,
  EliteSignupData,
  BuyerApplication,
  CreateBuyerApplicationPayload,
  BatchQueryArgs,
  BuyerPreference,
  UserDebugInfoType,
  Inquiry,
  InquiryData,
  AgentSignupData,
  AgentDetails,
  BuyerRefererDetails,
} from "../my-types";
import { ClientData, getClientData } from "../libs/client-data";

export function restGetErrorMessage(err: any): string {
  // Fetch error, e.g. network error.
  if (typeof err === "object" && "error" in err && "status" in err) {
    return err.error;
  }

  // WordPress error, e.g. WP_Error.
  if (typeof err === "object" && "data" in err && "status" in err) {
    if (typeof err.data === "object" && "message" in err.data) {
      return err.data.message;
    }
  }

  return "Unknown Error. Please contact the developer. mpereere@gmail.com";
}

const baseQuery = fetchBaseQuery({
  //   baseUrl: process.env.NEXT_PUBLIC_API_BASE_URL,
  baseUrl: "/wp-json/hre-addon/v1",
  credentials: "include",
  prepareHeaders(headers) {
    headers.set("X-WP-Nonce", getClientData()?.rest_nonce);
    // console.log('prepareHeaders', { headers });
    // const token = getUserToken();
    // if (token) {
    //   headers.set("Authorization", `Bearer ${token}`);
    // }
    return headers;
  },
});

const baseQueryWithReauth: BaseQueryFn<
  string | FetchArgs,
  unknown,
  FetchBaseQueryError
> = async (args, api, extraOptions) => {
  const result = await baseQuery(args, api, extraOptions);

  //   console.log("api-request-after", { result, args, api, extraOptions });

  if (result.error && result.error.status === 403) {
    // setUserToken("");
    // try to get a new token
    // const refreshResult = await baseQuery( '/refreshToken', api, extraOptions );
    // if ( refreshResult.data ) {
    // 	// store the new token
    // 	api.dispatch( tokenReceived( refreshResult.data ) );
    // 	// retry the initial query
    // if (canReloadWhen403()) {
    //   window.location.href = window.location.href;
    // }
    // } else {
    // 	api.dispatch( loggedOut() );
    // }
  }
  return result;
};
export const myApi = createApi({
  reducerPath: "api",
  baseQuery: baseQueryWithReauth,
  tagTypes: ["buyer_applications"],
  endpoints: (builder) => ({
    // Auth.
    getUser: builder.query<JapUser, { userId: number }>({
      query: ({ userId }) => ({
        url: `/auth/user/${userId}`,
      }),
    }),
    login: builder.mutation<string, { username: string; password: string }>({
      query: ({ username, password }) => ({
        url: "/auth/login",
        method: "POST",
        body: {
          username,
          password,
        },
      }),
    }),
    signup: builder.mutation<string, SignupData>({
      query: (signupData) => ({
        url: "/auth/signup",
        method: "POST",
        body: signupData,
      }),
    }),
    buyerSignup: builder.mutation<
      string,
      EliteSignupData & BuyerRefererDetails
    >({
      query: (signupData) => ({
        url: "/auth/signup-buyer",
        method: "POST",
        body: signupData,
      }),
    }),
    __becomeABuyer: builder.mutation<string, EliteSignupData>({
      query: (signupData) => ({
        url: "/buyer/become-a-user/save-form/generate-product",
        method: "POST",
        body: signupData,
      }),
    }),
    eliteSignup: builder.mutation<
      { product_id: number },
      {
        ajaxUrl: string;
        ajaxAction: string;
        data: EliteSignupData;
      }
    >({
      query: (payload) => ({
        url: "/auth/generate-elite-access-product",
        method: "POST",
        body: {
          action: payload.ajaxAction,
          ...payload.data,
        },
      }),
    }),
    fetchCreateEliteProductWithSignup: builder.mutation<
      { product_id: number },
      EliteSignupData
    >({
      query: (payload) => ({
        url: "/auth/generate-elite-access-product-with-signup",
        method: "POST",
        body: payload,
      }),
    }),
    createEliteProduct: builder.mutation<{ product_id: number }, void>({
      query: (payload) => ({
        url: "/auth/generate-elite-access-product-without-signup",
        method: "POST",
      }),
    }),
    recoverPasswordSendOtp: builder.mutation<string, { username: string }>({
      query: ({ username }) => ({
        url: "/auth/password-recovery/send-otp",
        method: "POST",
        body: {
          username,
        },
      }),
    }),
    recoverPasswordVerifyOtp: builder.mutation<
      string,
      { otp: string; username: string }
    >({
      query: ({ otp, username }) => ({
        url: "/auth/password-recovery/verify-otp",
        method: "POST",
        body: {
          otp,
          username,
        },
      }),
    }),
    recoverPasswordResetPassword: builder.mutation<
      string,
      ResetPasswordPayload
    >({
      query: (payload) => ({
        url: "/auth/password-recovery/reset-password",
        method: "POST",
        body: payload,
      }),
    }),
    saveUserDebugInfo: builder.mutation<string, UserDebugInfoType>({
      query: (payload) => ({
        url: "/auth/save-user-debug-info",
        method: "POST",
        body: payload,
      }),
    }),
    getUserDebugInfo: builder.query<UserDebugInfoType, void>({
      query: () => ({
        url: "/auth/get-user-debug-info",
        method: "GET",
      }),
    }),
    saveBuyerPreference: builder.mutation<string, BuyerPreference>({
      query: (payload) => ({
        url: "/auth/save-buyer-preference",
        method: "POST",
        body: payload,
      }),
    }),
    getBuyerPreference: builder.query<BuyerPreference, { user_id: number }>({
      query: (payload) => ({
        url: "/auth/get-buyer-preference",
        method: "GET",
        params: payload,
      }),
    }),
    getAgentDetails: builder.query<AgentDetails, { agent_id: number }>({
      query: (payload) => ({
        url: "/auth/get-agent-details",
        method: "GET",
        params: payload,
      }),
    }),
    signupAgent: builder.mutation<string, AgentSignupData>({
      query: (payload) => ({
        url: "/auth/signup-agent",
        method: "POST",
        body: payload,
      }),
    }),
    // Admin
    fetchSaveAdminSettings: builder.mutation<
      boolean,
      ClientData["admin_settings"]
    >({
      query: (payload) => ({
        url: `/admin/save-settings`,
        method: "POST",
        body: payload,
      }),
    }),
    // Buyer Application.
    fetCreateBuyerApplication: builder.mutation<
      BuyerApplication,
      CreateBuyerApplicationPayload
    >({
      query: (payload) => ({
        url: `/buyer-application/create-application`,
        method: "POST",
        body: payload,
      }),
      invalidatesTags: ["buyer_applications"],
    }),
    fetchCanBuyerApply: builder.mutation<boolean, { property_id: number }>({
      query: (payload) => ({
        url: `/buyer-application/can-buyer-apply`,
        method: "POST",
        body: payload,
      }),
    }),
    fetchHasBuyerApplied: builder.query<
      BuyerApplication,
      { propertyId: number }
    >({
      query: ({ propertyId }) => ({
        url: `/buyer-application/has-buyer-already-applied`,
        method: "GET",
        params: {
          property_id: propertyId,
        },
      }),
      providesTags: ["buyer_applications"],
    }),
    fetchBatchGetBuyerApplications: builder.query<
      BuyerApplication[],
      BatchQueryArgs
    >({
      query: (payload) => ({
        url: `/buyer-application/batch-get`,
        method: "GET",
        params: payload,
      }),
      providesTags: ["buyer_applications"],
    }),
    // Inquiry.
    fetchCreateInquiry: builder.mutation<Inquiry, InquiryData>({
      query: (payload) => ({
        url: `/inquiry/create-inquiry`,
        method: "POST",
        body: { ...payload },
      }),
    }),
    fetchGetInquiry: builder.query<Inquiry, { id: number }>({
      query: (id) => ({
        url: `/inquiry/${id}`,
        method: "GET",
      }),
    }),
  }),
});

export const {
  // Auth.
  useGetUserQuery,
  useLoginMutation,
  useSignupMutation,
  useRecoverPasswordSendOtpMutation,
  useRecoverPasswordVerifyOtpMutation,
  useRecoverPasswordResetPasswordMutation,
  useEliteSignupMutation,
  useSaveBuyerPreferenceMutation,
  useSaveUserDebugInfoMutation,
  useGetUserDebugInfoQuery,
  useCreateEliteProductMutation,
  useFetchCreateEliteProductWithSignupMutation,
  useGetBuyerPreferenceQuery,
  useSignupAgentMutation,
  useGetAgentDetailsQuery,
  useBuyerSignupMutation,
  // Admin.
  useFetchSaveAdminSettingsMutation,
  // Buyer Application.
  useFetCreateBuyerApplicationMutation,
  useFetchHasBuyerAppliedQuery,
  useFetchCanBuyerApplyMutation,
  useFetchBatchGetBuyerApplicationsQuery,
  // Inquiry.
  useFetchCreateInquiryMutation,
  useFetchGetInquiryQuery,
} = myApi;
