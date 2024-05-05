import * as classNames from "classnames";
import * as React from "react";
import { SyntheticEvent } from "react";
import { toast } from "react-toastify";
import { Typography } from "@mui/material";
import LoadingButton from "@mui/lab/LoadingButton";
import {
  restGetErrorMessage,
  useBuyerSignupMutation,
  useCreateEliteProductMutation,
  useEliteSignupMutation,
  useFetchCreateEliteProductWithSignupMutation,
} from "../../rtk/myapi";
import { BuyerRefererDetails, EliteSignupData } from "../../my-types";
import { tr } from "../../i18n/tr";
import { getClientData } from "../../libs/client-data";
import * as he from "he";
import { Resource } from "../../libs/Resource";
import { getUsStates } from "../inquiry/InquiryForm";
import CustomizedDialog from "../../components/CustomizedDialog";
import Button from "@mui/material/Button";

type SignupInputNamesTypes = keyof EliteSignupData;
// | "email"
// | "username"
// | "full_name"
// | "phone"
// | "password"
// | "state";
export default function EliteSignup({ for: _for }: SignupProps) {
  const gt = getClientData();
  const url = new URL(window.location.href);
  const redirect = url.searchParams.get("redirect");
  let loginUrl =
    "buyer" === _for
      ? getClientData().client_settings.buyer_elite_login_page_url
      : getClientData().client_settings.seller_elite_login_page_url;
  if (null !== redirect && redirect.length > 3) {
    loginUrl += "?redirect=" + redirect;
  }
  const [confirmPassword, setConfirmPassword] = React.useState("");
  const [signupData, setSignupData] = React.useState<EliteSignupData>({
    email: "",
    username: "",
    full_name: "",
    phone: "",
    password: "",
    state: "",
    for: _for,
    seller_agent_id: 0,
    seller_agent_doesnt_exists: "no",
    seller_agent_first_name: "",
    seller_agent_last_name: "",
    seller_agent_phone: "",
    seller_agent_state: "",
  });
  const clientSettings = getClientData().client_settings;
  const [openModalReferer, setOpenModalReferer] = React.useState(false);
  const [openModalRefererConfirmaton, setOpenModalRefererConfirmation] =
    React.useState(false);

  const [createEliteProduct, { isLoading: isLoadingCreateEliteProduct }] =
    useFetchCreateEliteProductWithSignupMutation();
  const [buyerReferrerDetails, setBuyerReferrerDetails] =
    React.useState<BuyerRefererDetails>({
      was_referred: "no",
      referrer_full_name: "",
      referrer_email: "",
      referrer_phone: "",
      referrer_is_agent_or_broker: "no",
      referrer_is_agent_or_broker_confirmation: "no",
    });

  const fee =
    "buyer" === _for
      ? clientSettings.buyer_elite_access_fee.toString()
      : clientSettings.seller_elite_access_fee.toString();

  const handleChange = (name: SignupInputNamesTypes, value: string) => {
    setSignupData({
      ...signupData,
      [name]: value.toString(),
    });
  };
  const [buyerSignup, { isLoading: isLoadingBuyerSignup }] =
    useBuyerSignupMutation();

  const ajaxSignup = () => {
    if (signupData.password !== confirmPassword) {
      toast.error(tr("Passwords do not match"));
      return;
    }

    createEliteProduct(signupData)
      .unwrap()
      .then((data) => {
        // clear the form.
        setSignupData({
          email: "",
          username: "",
          full_name: "",
          phone: "",
          password: "",
          state: "",
          for: _for,
          seller_agent_id: 0,
          seller_agent_doesnt_exists: "yes",
          seller_agent_first_name: "",
          seller_agent_last_name: "",
          seller_agent_phone: "",
          seller_agent_state: "",
        });
        toast.success(tr("Please wait while we redirect you to checkout."));
        setTimeout(() => {
          window.location.href =
            getClientData().site_url +
            "?buyer_action=add_to_cart&product_id=" +
            data.product_id;
        }, 2000);
        // changeView("login");
      })
      .catch((error) => {
        toast.error(restGetErrorMessage(error));
      });
  };

  const ajaxSignupBuyer = () => {
    if (signupData.password !== confirmPassword) {
      toast.error(tr("Passwords do not match"));
      return;
    }

    buyerSignup({
      ...signupData,
      ...buyerReferrerDetails,
    })
      .unwrap()
      .then((data) => {
        // clear the form.
        setSignupData({
          email: "",
          username: "",
          full_name: "",
          phone: "",
          password: "",
          state: "",
          for: _for,
          seller_agent_id: 0,
          seller_agent_doesnt_exists: "yes",
          seller_agent_first_name: "",
          seller_agent_last_name: "",
          seller_agent_phone: "",
          seller_agent_state: "",
        });
        setBuyerReferrerDetails({
          was_referred: "no",
          referrer_full_name: "",
          referrer_email: "",
          referrer_phone: "",
          referrer_is_agent_or_broker: "no",
          referrer_is_agent_or_broker_confirmation: "no",
        });
        toast.success(
          tr(
            "Buyer account created successfully. Wait while we redirect you to set your preferences.",
          ),
        );
        setTimeout(() => {
          window.location.href = gt.client_settings.buyer_preference_page_url;
        }, 4000);
        // changeView("login");
      })
      .catch((error) => {
        toast.error(restGetErrorMessage(error));
      });
  };

  return render();

  function render() {
    return (
      <div className={""}>
        <form
          onSubmit={(e: SyntheticEvent) => {
            e.preventDefault();
            if (_for === "buyer") {
              setOpenModalReferer(true);
            } else {
              ajaxSignup();
            }
          }}
          className="mp-slide-in my-2 bg-white max-w-[600px] m-auto p-4 shadow border border-solid border-gray-100 flex flex-col gap-4"
        >
          {renderFormInputs()}
          {renderSellerAgent()}
          <br />
          <LoadingButton
            title={tr("Signup")}
            type={"submit"}
            loading={isLoadingCreateEliteProduct || isLoadingBuyerSignup}
            variant={"contained"}
          >
            {tr("Continue")}
            {/*{["buyer", "seller"].includes(_for) && <span>{tr("Continue")}</span>}*/}
            {/*{"seller" !== _for && (*/}
            {/*  <span className={"flex justify-center gap-2 items-center"}>*/}
            {/*    <span>{tr("Pay ")}</span>*/}
            {/*    <span*/}
            {/*      className={"font-bold text-xl"}*/}
            {/*      dangerouslySetInnerHTML={{*/}
            {/*        __html: he.decode(fee),*/}
            {/*      }}*/}
            {/*    ></span>*/}
            {/*    <span>{tr("for elite access")}</span>*/}
            {/*  </span>*/}
            {/*)}*/}
          </LoadingButton>
          <p className="mb-0 mt-4 text-gray-500 text-center">
            {tr("Already a member?")} &nbsp;&nbsp;
            <a className={"text-hre"} href={loginUrl}>
              {tr("Login")}
            </a>
          </p>
        </form>
        <CustomizedDialog
          open={openModalReferer}
          onClose={() => setOpenModalReferer(false)}
          title={tr("Your referer Details")}
          content={renderRefererModalContent()}
          actions={null}
        />
        <CustomizedDialog
          open={openModalRefererConfirmaton}
          onClose={() => setOpenModalRefererConfirmation(false)}
          title={tr("Referer Confirmation")}
          content={renderRefererConfirmationModalContent()}
          actions={renderModalActionRefererConfirmation()}
        />
      </div>
    );
  }

  function renderRefererModalContent() {
    return (
      <form
        className={"flex flex-col gap-3"}
        onSubmit={(e) => {
          e.preventDefault();
          if ("no" === buyerReferrerDetails.referrer_is_agent_or_broker) {
            setOpenModalReferer(false);
            setOpenModalRefererConfirmation(true);
          } else {
            setOpenModalReferer(false);
            ajaxSignupBuyer();
          }
        }}
      >
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          Were you referred to divieight.com by someone? If so, that person may
          earn a referral fee! That’s right, even if they’re not a real estate
          agent! So, were you referred by someone?
        </Typography>
        {renderBuyerWasReferredRadioButtons()}
        {"yes" === buyerReferrerDetails.was_referred && [
          renderBuyerReferrerFullName(),
          renderBuyerEmailAndPhoneNumber(),
          renderBuyerRefererIsBrokerOrAgentRadioButtons(),
          // ...[
          //   "yes" === buyerReferrerDetails.referrer_is_agent_or_broker &&
          //     renderBuyerRefererConfirmBuyerReferrerIsBrokerOrAgent(),
          // ],
        ]}
        <div className="actions">{renderModalActionReferer()}</div>
      </form>
    );
  }

  function renderBuyerWasReferredRadioButtons() {
    return (
      <div
        className={"buyer-was-refereed w-full text-base flex flex-col gap-1"}
      >
        <div className="flex flex-row gap-6 flex-wrap">
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"yes"}
              checked={"yes" === buyerReferrerDetails.was_referred}
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  was_referred: event.currentTarget.value as any,
                });
              }}
            />
            <span>{tr("Yes")}</span>
          </label>
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"no"}
              checked={"no" === buyerReferrerDetails.was_referred}
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  was_referred: event.currentTarget.value as any,
                });
              }}
            />
            <span>{tr("No")}</span>
          </label>
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"other_means"}
              checked={"other_means" === buyerReferrerDetails.was_referred}
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  was_referred: event.currentTarget.value as any,
                  referrer_is_agent_or_broker: "no",
                  referrer_is_agent_or_broker_confirmation: "no",
                });
              }}
            />
            <span>{tr("No, I discovered divieight by other means.")}</span>
          </label>
        </div>
      </div>
    );
  }

  function renderBuyerReferrerFullName() {
    return (
      <div className={"buyer-agent-full-name"}>
        <div className="lg:flex flex-row flex-wrap gap-3 mp-slide-in">
          {/*  Buyer Referrer First name */}
          <div className={"agent-first-name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Full name of person referring you.")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  setBuyerReferrerDetails({
                    ...buyerReferrerDetails,
                    referrer_full_name: event.currentTarget.value,
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={buyerReferrerDetails.referrer_full_name}
                name={"referrer_full_name"}
              />
            </label>
          </div>
        </div>
      </div>
    );
  }

  function renderBuyerEmailAndPhoneNumber() {
    return (
      <div className={"buyer-full-name"}>
        <div className="lg:flex flex-row flex-wrap gap-3 mp-slide-in">
          {/*  Buyer Referrer First name */}
          <div
            className={
              "buyer-referrer-email-and-phone flex-auto flex flex-col lg:flex-row gap-4 "
            }
          >
            <label className={"flex-1 text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("That person's email.")}
              </Typography>
              <input
                required={true}
                type={"email"}
                onInput={(event) => {
                  setBuyerReferrerDetails({
                    ...buyerReferrerDetails,
                    referrer_email: event.currentTarget.value,
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={buyerReferrerDetails.referrer_email}
                name={"referrer_email"}
              />
            </label>
            <label className={"flex-1 text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("That person's cell phone number")}
              </Typography>
              <input
                required={true}
                type={"tel"}
                onInput={(event) => {
                  setBuyerReferrerDetails({
                    ...buyerReferrerDetails,
                    referrer_phone: event.currentTarget.value,
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={buyerReferrerDetails.referrer_phone}
                name={"referrer_phone"}
              />
            </label>
          </div>
        </div>
      </div>
    );
  }

  function renderBuyerRefererIsBrokerOrAgentRadioButtons() {
    return (
      <div
        className={"buyer-was-refereed w-full text-base flex flex-col gap-1"}
      >
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          Okay, if you were referred (and this is important), is the person who
          referred you a real estate agent or broker?
        </Typography>
        <div className="flex flex-row gap-6 flex-wrap">
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"yes"}
              checked={
                "yes" === buyerReferrerDetails.referrer_is_agent_or_broker
              }
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  referrer_is_agent_or_broker: event.currentTarget.value as any,
                });
              }}
            />
            <span>{tr("Yes")}</span>
          </label>
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"no"}
              checked={
                "no" === buyerReferrerDetails.referrer_is_agent_or_broker
              }
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  referrer_is_agent_or_broker: event.currentTarget.value as any,
                  referrer_is_agent_or_broker_confirmation: "no",
                });
              }}
            />
            <span>{tr("No")}</span>
          </label>
        </div>
      </div>
    );
  }

  function renderBuyerRefererConfirmBuyerReferrerIsBrokerOrAgent() {
    return (
      <div
        className={"buyer-was-refereed w-full text-base flex flex-col gap-1"}
      >
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          You have indicated that the person referring you is not a real estate
          agent or broker. Is this correct?
        </Typography>
        <div className="flex flex-row gap-6 flex-wrap">
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"yes"}
              checked={
                "yes" ===
                buyerReferrerDetails.referrer_is_agent_or_broker_confirmation
              }
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  referrer_is_agent_or_broker_confirmation: event.currentTarget
                    .value as any,
                });
              }}
            />
            <span>{tr("Yes")}</span>
          </label>
          <label className={" flex flex-row gap-3 items-center cursor-pointer"}>
            <input
              type="radio"
              value={"no"}
              checked={
                "no" ===
                buyerReferrerDetails.referrer_is_agent_or_broker_confirmation
              }
              onChange={(event) => {
                setBuyerReferrerDetails({
                  ...buyerReferrerDetails,
                  referrer_is_agent_or_broker_confirmation: event.currentTarget
                    .value as any,
                });
              }}
            />
            <span>{tr("No")}</span>
          </label>
        </div>
        {/*<div className="w-full">*/}
        {/*  <LoadingButton*/}
        {/*    title={tr("Signup Buyer")}*/}
        {/*    type={"button"}*/}
        {/*    loading={false}*/}
        {/*    variant={"contained"}*/}
        {/*    onClick={(e) => {*/}
        {/*      ajaxSignupBuyer();*/}
        {/*    }}*/}
        {/*  >*/}
        {/*    {tr("Submit")}*/}
        {/*  </LoadingButton>*/}
        {/*</div>*/}
      </div>
    );
  }

  function renderModalActionReferer() {
    return (
      <div className={"referer-action-buttons"}>
        <Button variant={"contained"} type={"submit"}>
          Continue
        </Button>
      </div>
    );
  }

  function renderRefererConfirmationModalContent() {
    return (
      <div className={"flex flex-col gap-3"}>
        {renderBuyerRefererConfirmBuyerReferrerIsBrokerOrAgent()}
      </div>
    );
  }

  function renderModalActionRefererConfirmation() {
    return (
      <div className={"referer-action-buttons"}>
        <Button
          variant="contained"
          onClick={() => {
            setOpenModalRefererConfirmation(false);
            ajaxSignupBuyer();
          }}
        >
          Submit
        </Button>
      </div>
    );
  }

  function renderFormInputs() {
    return (
      <>
        {/*  Username */}
        <div className={"username"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Username")}
            </Typography>
            <input
              required={true}
              type={"text"}
              onInput={(event) => {
                handleChange("username", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border border-solid border-gray-400 bg-[#F7F7F7]",
              )}
              value={signupData.username}
            />
          </label>
        </div>
        {/*  Full name */}
        <div className={"username"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Full Name")}
            </Typography>
            <input
              required={true}
              type={"text"}
              onInput={(event) => {
                handleChange("full_name", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border border-solid border-gray-400 bg-[#F7F7F7]",
              )}
              value={signupData.full_name}
            />
          </label>
        </div>
        {/*  Email & Phone*/}
        <div className="lg:flex flex-wrap gap-4 justify-between">
          {/*  Email */}
          <div className={"email flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Email")}
              </Typography>
              <input
                required={true}
                type={"email"}
                onInput={(event) => {
                  handleChange("email", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.email}
              />
            </label>
          </div>
          {/*  Phone */}
          <div className={"phone flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Phone")}
              </Typography>
              <input
                required={true}
                type={"tel"}
                onInput={(event) => {
                  handleChange("phone", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.phone}
                name={"phone"}
              />
            </label>
          </div>
        </div>
        {/*  Password and Confirm Password  */}
        <div className="lg:flex flex-wrap gap-4 justify-between">
          <div className={"password flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Password")}
              </Typography>
              <input
                required={true}
                type={"password"}
                onInput={(event) => {
                  handleChange("password", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.password}
              />
            </label>
          </div>
          <div className={"confirm-password flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Confirm Password")}
              </Typography>
              <input
                required={true}
                type={"password"}
                onInput={(event) => {
                  setConfirmPassword(event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={confirmPassword}
              />
            </label>
          </div>
        </div>
        {/* State */}
        {"buyer" === _for && (
          <div className="lg:flex flex-wrap gap-4 justify-between">
            <div className={"state flex-auto"}>
              <label className={"w-full text-base flex flex-col"}>
                <Typography
                  variant={"h6"}
                  className={"mt-2 !mb-1 !p-0 text-base"}
                >
                  {tr("State")}
                </Typography>
                <select
                  name="location.state"
                  required
                  value={signupData.state}
                  className={classNames(
                    "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
                  )}
                  onChange={(e) => {
                    handleChange("state", e.target.value);
                  }}
                >
                  <option value="">{tr("Select State")}</option>
                  {getUsStates().map((item) => (
                    <option value={item}>{item}</option>
                  ))}
                </select>
              </label>
            </div>
          </div>
        )}
      </>
    );
  }

  function renderSellerAgent() {
    if ("seller" !== _for) {
      return null;
    }
    return (
      <div
        className={
          "seller-agent-info flex flex-col gap-2 p-4 border border-solid border-gray-200 my-4"
        }
      >
        <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
          {tr("Your Agent's Information")}
        </Typography>
        {/* Agent Dropdown */}
        {renderSellerAgentDropdown()}
        {/* I already have an agent not listed above */}
        {renderSellerAgentNotListedSelection()}
        {"yes" === signupData.seller_agent_doesnt_exists &&
          renderSellerAgentFirstLastPhone()}
      </div>
    );
  }

  function renderSellerAgentFirstLastPhone() {
    return (
      <>
        {/*  Seller Agent First Name & Last Name */}
        <div className="lg:flex flex-row flex-wrap gap-3 mp-slide-in">
          {/*  Seller Agent First name */}
          <div className={"agent-first-name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Agent's First  Name")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  setSignupData({
                    ...signupData,
                    seller_agent_id: 0,
                    seller_agent_first_name: event.currentTarget.value,
                    seller_agent_doesnt_exists: "yes",
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.seller_agent_first_name}
                name={"seller_agent_first_name"}
              />
            </label>
          </div>
          {/*  Agent's Last name */}
          <div className={"seller-agent-last-name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Agent's Last  Name")}
              </Typography>
              <input
                required={true}
                type={"tel"}
                onInput={(event) => {
                  setSignupData({
                    ...signupData,
                    seller_agent_id: 0,
                    seller_agent_last_name: event.currentTarget.value,
                    seller_agent_doesnt_exists: "yes",
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.seller_agent_last_name}
                name={"seller_agent_last_name"}
              />
            </label>
          </div>
          {/*  Agent's Phone */}
          <div className={"seller-agent-phone flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Agent's Phone Number")}
              </Typography>
              <input
                required={true}
                type={"tel"}
                onInput={(event) => {
                  setSignupData({
                    ...signupData,
                    seller_agent_id: 0,
                    seller_agent_phone: event.currentTarget.value,
                    seller_agent_doesnt_exists: "yes",
                  });
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.seller_agent_phone}
                name={"seller_agent_phone"}
              />
            </label>
          </div>
          {/*  State where agent is based seller_agent_state*/}
          {"seller" === _for && (
            <div className="lg:flex flex-wrap gap-4 justify-between w-full">
              <div className={"state flex-auto"}>
                <label className={"w-full text-base flex flex-col"}>
                  <Typography
                    variant={"h6"}
                    className={"mt-2 !mb-1 !p-0 text-base"}
                  >
                    {tr("State where agent is based")}
                  </Typography>
                  <select
                    name="location.seller_agent_state"
                    required
                    value={signupData.seller_agent_state}
                    className={classNames(
                      "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
                    )}
                    onChange={(e) => {
                      handleChange("seller_agent_state", e.target.value);
                    }}
                  >
                    <option value="">{tr("Select State")}</option>
                    {getUsStates().map((item) => (
                      <option value={item}>{item}</option>
                    ))}
                  </select>
                </label>
              </div>
            </div>
          )}
        </div>
      </>
    );
  }

  function renderSellerAgentNotListedSelection() {
    return (
      <div className={"agent-not-listed w-full text-base flex flex-col gap-2"}>
        <div className={"w-full text-base flex flex-col gap-1"}>
          <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
            {/* eslint-disable-next-line  */}
            {tr("I already have an agent who will represent me as potential transaction participant")}
          </Typography>
          <div className="flex flex-row gap-6">
            <label
              className={" flex flex-row gap-3 items-center cursor-pointer"}
            >
              <input
                type="radio"
                value={"yes"}
                checked={"yes" === signupData.seller_agent_doesnt_exists}
                onChange={(event) => {
                  // handleChange(
                  //   "seller_agent_doesnt_exists",
                  //   event.currentTarget.value,
                  // );
                  setSignupData({
                    ...signupData,
                    seller_agent_id: 0,
                    seller_agent_doesnt_exists: event.currentTarget
                      .value as any,
                  });
                }}
              />
              <span>{tr("Yes")}</span>
            </label>
            <label
              className={" flex flex-row gap-3 items-center cursor-pointer"}
            >
              <input
                type="radio"
                value={"no"}
                name={"no_licensed"}
                checked={"no" === signupData.seller_agent_doesnt_exists}
                onChange={(event) => {
                  setSignupData({
                    ...signupData,
                    seller_agent_id: 0,
                    seller_agent_doesnt_exists: event.currentTarget
                      .value as any,
                  });
                }}
              />
              <span>{tr("No")}</span>
            </label>
          </div>
        </div>
      </div>
    );
  }

  function renderSellerAgentDropdown() {
    return (
      <div className="lg:flex flex-wrap gap-4 justify-between">
        <div className={"state flex-auto"}>
          <label
            className={classNames(
              "w-full text-base flex flex-col",
              "yes" === signupData.seller_agent_doesnt_exists
                ? "opacity-30 cursor-not-allowed"
                : "",
            )}
          >
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Select Agent")}
            </Typography>
            <select
              name="location.state"
              required={"no" === signupData.seller_agent_doesnt_exists}
              className={classNames(
                "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                // handleChange("seller_agent_id", parseInt(e.target.value));
                setSignupData({
                  ...signupData,
                  seller_agent_id: parseInt(e.target.value),
                  seller_agent_first_name: "",
                  seller_agent_last_name: "",
                  seller_agent_phone: "",
                  seller_agent_doesnt_exists: "no",
                });
              }}
            >
              <option value="">{tr("Select Agent")}</option>
              {gt.all_agents.map((item) => (
                <option
                  value={item.id}
                  key={item.id}
                  selected={item.id === signupData.seller_agent_id}
                >
                  {item.name}
                </option>
              ))}
            </select>
          </label>
        </div>
      </div>
    );
  }
}

export interface SignupProps {
  for: "buyer" | "seller" | "agent";
}
