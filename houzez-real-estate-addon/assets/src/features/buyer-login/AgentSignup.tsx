import * as classNames from "classnames";
import * as React from "react";
import { SyntheticEvent } from "react";
import { toast } from "react-toastify";
import { Typography } from "@mui/material";
import LoadingButton from "@mui/lab/LoadingButton";
import {
  restGetErrorMessage,
  useCreateEliteProductMutation,
  useEliteSignupMutation,
  useFetchCreateEliteProductWithSignupMutation,
  useSignupAgentMutation,
} from "../../rtk/myapi";
import { AgentSignupData, EliteSignupData } from "../../my-types";
import { tr } from "../../i18n/tr";
import { getClientData } from "../../libs/client-data";
import * as he from "he";
import { Resource } from "../../libs/Resource";
import { getUsStates } from "../inquiry/InquiryForm";

declare const grecaptcha: any;

// should be key of AgentSignupData
type SignupInputNamesTypes = keyof AgentSignupData;
export default function AgentSignup() {
  const gt = getClientData();
  const url = new URL(window.location.href);
  const redirect = url.searchParams.get("redirect");
  let loginUrl = getClientData().client_settings.agent_login_page_url;
  if (null !== redirect && redirect.length > 3) {
    loginUrl += "?redirect=" + redirect;
  }
  const [confirmPassword, setConfirmPassword] = React.useState("");
  const [signupData, setSignupData] = React.useState<AgentSignupData>({
    first_name: "",
    last_name: "",
    licensed_agent: "no",
    username: "",
    license_state: "",
    license_number: "",
    name_of_agency: "",
    city: "",
    zip_code: "",
    name_of_principal_broker: "",
    state: "",
    email: "",
    phone: "",
    phone_landline: "",
    password: "",
    confirm_password: "",
    recatpcha_token: "",
  });
  const clientSettings = getClientData().client_settings;
  const [signup, { isLoading: isLoadingSignup, error: signupError }] =
    useSignupAgentMutation();

  // const [createEliteProduct, { isLoading: isLoadingCreateEliteProduct }] =
  //   useFetchCreateEliteProductWithSignupMutation();

  const handleChange = (name: SignupInputNamesTypes, value: string) => {
    setSignupData({
      ...signupData,
      [name]: value.toString(),
    });
  };

  const ajaxSignup = (event: SyntheticEvent) => {
    event.preventDefault();
    if (signupData.password !== confirmPassword) {
      toast.error(tr("Passwords do not match"));
    }
  };

  return render();

  function render() {
    return (
      <form
        onSubmit={handleSignup}
        className="mp-slide-in my-2 bg-white max-w-[600px] m-auto p-4 shadow border border-solid border-gray-100 flex flex-col gap-4"
      >
        {renderFormInputs()}
        <br />
        <LoadingButton
          title={tr("Signup")}
          type={"submit"}
          loading={isLoadingSignup}
          variant={"contained"}
        >
          {tr("Signup")}
        </LoadingButton>
        <p className="mb-0 mt-4 text-gray-500 text-center">
          {tr("Already an agent?")} &nbsp;&nbsp;
          <a className={"text-hre"} href={loginUrl}>
            {tr("Login")}
          </a>
        </p>
      </form>
    );
  }

  function renderFormInputs() {
    return (
      <>
        {/*  First name  & Last name*/}
        <div className="lg:flex flex-wrap gap-4 justify-between">
          <div className={"first-name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("First Name")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("first_name", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.first_name}
              />
            </label>
          </div>
          <div className={"last-name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Last Name")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("last_name", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.last_name}
              />
            </label>
          </div>
        </div>
        {/* Are you a licenced real estate agent*/}
        <div className={"licensed-agent w-full text-base flex flex-col gap-2"}>
          <div className={"w-full text-base flex flex-col gap-1"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Are you a licensed real estate agent?")}
            </Typography>
            <label
              className={" flex flex-row gap-3 items-center cursor-pointer"}
            >
              <input
                type="radio"
                value={"no"}
                name={"no_licensed"}
                checked={"no" === signupData.licensed_agent}
                onChange={(event) => {
                  handleChange("licensed_agent", event.currentTarget.value);
                }}
              />
              <span>{tr("No")}</span>
            </label>
            <label
              className={" flex flex-row gap-3 items-center cursor-pointer"}
            >
              <input
                type="radio"
                value={"yes"}
                name={"yes_licensed"}
                checked={"yes" === signupData.licensed_agent}
                onChange={(event) => {
                  handleChange("licensed_agent", event.currentTarget.value);
                }}
              />
              <span>{tr("Yes")}</span>
            </label>
          </div>
        </div>
        {"yes" === signupData.licensed_agent && (
          <div
            className={
              "license-info mp-slide-in px-4 bg-gray-50 py-4 border border-solid border-gray-200"
            }
          >
            {/* License State */}
            <div className="lg:flex flex-wrap gap-4 justify-between">
              <div className={"state flex-auto"}>
                <label className={"w-full text-base flex flex-col"}>
                  <Typography
                    variant={"h6"}
                    className={"mt-2 !mb-1 !p-0 text-base"}
                  >
                    {tr("What state issued your license?")}
                  </Typography>
                  <select
                    name="license.state"
                    required
                    value={signupData.license_state}
                    className={classNames(
                      "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
                    )}
                    onChange={(e) => {
                      handleChange("license_state", e.target.value);
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
            {/*License number or code*/}
            <div className={"license-no"}>
              <label className={"w-full text-base"}>
                <Typography
                  variant={"h6"}
                  className={"mt-2 !mb-1 !p-0 text-base"}
                >
                  {tr("License number or code")}
                </Typography>
                <input
                  required={true}
                  type={"text"}
                  onInput={(event) => {
                    handleChange("license_number", event.currentTarget.value);
                  }}
                  className={classNames(
                    "w-full py-4 px-2 h-[50px]",
                    "border border-solid border-gray-400 bg-[#F7F7F7]",
                  )}
                  value={signupData.license_number}
                />
              </label>
            </div>
            {/*  Name of your agency or brokerage */}
            <div className={"name-of-agent"}>
              <label className={"w-full text-base"}>
                <Typography
                  variant={"h6"}
                  className={"mt-2 !mb-1 !p-0 text-base"}
                >
                  {tr("Name of your agency or brokerage")}
                </Typography>
                <input
                  required={true}
                  type={"text"}
                  onInput={(event) => {
                    handleChange("name_of_agency", event.currentTarget.value);
                  }}
                  className={classNames(
                    "w-full py-4 px-2 h-[50px]",
                    "border border-solid border-gray-400 bg-[#F7F7F7]",
                  )}
                  value={signupData.name_of_agency}
                />
              </label>
            </div>
          </div>
        )}
        {/* City and State  */}
        <div className="lg:flex flex-wrap gap-4 ">
          {/*  City */}
          <div className={"city flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("City")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("city", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border border-solid border-gray-400 bg-[#F7F7F7]",
                )}
                value={signupData.city}
              />
            </label>
          </div>
          {/*  State */}
          <div className={"phone flex-auto"}>
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
        {/*  Zip Code*/}
        <div className={"zip-codes flex-auto"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Zip Code")}
            </Typography>
            <input
              required={true}
              type={"text"}
              onInput={(event) => {
                handleChange("zip_code", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border border-solid border-gray-400 bg-[#F7F7F7]",
              )}
              value={signupData.zip_code}
            />
          </label>
        </div>
        {/*  Name of Principal Broker */}
        <div className={"principal-broker flex-auto"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Name of Principal Broker")}
            </Typography>
            <input
              required={true}
              type={"text"}
              onInput={(event) => {
                handleChange(
                  "name_of_principal_broker",
                  event.currentTarget.value,
                );
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border border-solid border-gray-400 bg-[#F7F7F7]",
              )}
              value={signupData.name_of_principal_broker}
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
        {/*  Phone Landline */}
        <div className={"phone-land-line"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
              {tr("Landline Phone (Optional)")}
            </Typography>
            <input
              type={"text"}
              onInput={(event) => {
                handleChange("phone_landline", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border border-solid border-gray-400 bg-[#F7F7F7]",
              )}
              value={signupData.phone_landline}
            />
          </label>
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
      </>
    );
  }

  function handleSignup(e) {
    e.preventDefault();
    if (signupData.password !== confirmPassword) {
      toast.error(tr("Passwords do not match"));
      return;
    }

    grecaptcha.ready(function () {
      grecaptcha
        .execute(gt.client_settings.google_captcha_site_key, {
          action: "submit",
        })
        .then(function (token) {
          signup({
            ...signupData,
            recatpcha_token: token,
          })
            .unwrap()
            .then((response) => {
              toast.success(
                tr(
                  "Signup successful. Please wait while we redirect you to login.",
                ),
              );
              window.location.href = loginUrl;
            })
            .catch((error) => {
              toast.error(restGetErrorMessage(error));
            });
          // Add your logic to submit to your backend server here.
        })
        .catch(function (error) {
          toast.error(tr("Please make sure you are not a robot"));
        });
    });
  }
}

export interface SignupProps {
  for: "buyer" | "seller";
}
