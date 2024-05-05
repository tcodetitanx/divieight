import * as classNames from "classnames";
import * as React from "react";
import { _ChangeView } from "../SignupLogin/SignupLogin";
import { SyntheticEvent } from "react";
import { toast } from "react-toastify";
import { SignupData } from "../../../my-types";
import { useSignupMutation } from "../../../rtk/myapi";
import { Typography } from "@mui/material";
import { tr } from "../../../i18n/tr";
import LoadingButton from "@mui/lab/LoadingButton";

export interface SignupProps extends _ChangeView {}

type SignupInputNamesTypes =
  | "email"
  | "username"
  | "first_name"
  | "last_name"
  | "password"
  | "password_confirm";
export default function Signup({ changeView }: SignupProps) {
  const [signupData, setSignupData] = React.useState<SignupData>({
    email: "",
    username: "",
    first_name: "",
    last_name: "",
    password: "",
    password_confirm: "",
  });

  const [signup, { isLoading: isLoadingSignup, error: signupError }] =
    useSignupMutation();

  const [referrerTypedCount, setReferrerTypedCount] = React.useState(0);
  const [xhrVerifyReferrer, setXhrVerifyReferrer] = React.useState(null);
  const [verifyingReferrer, setVerifyingReferrer] = React.useState(false);
  const [refererVerified, setRefererVerified] = React.useState(false);
  const [referrerInfoText, setReferrerInfoText] = React.useState("");

  const handleChange = (name: SignupInputNamesTypes, value: string) => {
    setSignupData({
      ...signupData,
      [name]: value.toString().trim(),
    });
  };

  const ajaxSignup = (event: SyntheticEvent) => {
    event.preventDefault();
    if (signupData.password !== signupData.password_confirm) {
      toast.error("Passwords do not match.");
      return;
    }
    signup(signupData)
      .unwrap()
      .then((data) => {
        // clear the form.
        setSignupData({
          email: "",
          username: "",
          first_name: "",
          last_name: "",
          password: "",
          password_confirm: "",
        });
        toast.success("Signup successful. Please login.");
        changeView("login");
      })
      .catch((error) => {});
  };

  return (
    <form onSubmit={ajaxSignup} className="mp-slide-in bg-white">
      <div className={"username"}>
        <label>
          <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
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
              "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
            )}
            value={signupData.username}
          />
        </label>
      </div>
      <div className={"email"}>
        <label>
          <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
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
              "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
            )}
            value={signupData.email}
          />
        </label>
      </div>
      <div className="lg:flex flex-wrap gap-2 justify-between">
        <div className={"first_name flex-auto"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
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
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={signupData.first_name}
            />
          </label>
        </div>
        <div className={"last_name flex-auto"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
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
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={signupData.last_name}
            />
          </label>
        </div>
      </div>
      <div className="lg:flex flex-wrap gap-2 justify-between">
        <div className={"password flex-auto"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
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
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={signupData.password}
            />
          </label>
        </div>
        <div className={"password_confirm flex-auto"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
              {tr("Confirm Password")}
            </Typography>
            <input
              required={true}
              type={"password"}
              onInput={(event) => {
                handleChange("password_confirm", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={signupData.password_confirm}
            />
          </label>
        </div>
      </div>
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
        {tr("Already a member?")}
        <button
          className={classNames(
            "text-jap-primary text-center hover:text-jap-primary",
            "cursor-pointer font-normal hover:font-medium hover:underline",
            "!m-0 !p-0 !border-0 !bg-transparent !text-left",
            "text-hre-primary hover:text-gray-400",
          )}
          onClick={(e) => {
            e.preventDefault();
            changeView("login");
          }}
        >
          <span className="ml-2">{tr("login")}</span>
        </button>
      </p>
    </form>
  );
}
