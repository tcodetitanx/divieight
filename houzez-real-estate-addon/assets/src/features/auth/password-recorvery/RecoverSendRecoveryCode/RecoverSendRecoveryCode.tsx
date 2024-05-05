import React, { SyntheticEvent } from "react";
import classNames from "classnames";
import { _ChangeView } from "../../SignupLogin/SignupLogin";
import { Typography } from "@mui/material";
import { tr } from "../../../../i18n/tr";
import { LoadingButton } from "@mui/lab";
import { useRecoverPasswordSendOtpMutation } from "../../../../rtk/myapi";
import { toast } from "react-toastify";
import { restGetErrorMessage } from "./../../../../rtk/myapi";

export const RecoverSendRecoveryCode = ({
  changeView,
  setRecoverEmail,
}: RecoverVerifyEmailProps) => {
  const [state, setState] = React.useState({
    user_name: "",
  });
  const [sendOtp, { isLoading: isLoadingSendOtp }] =
    useRecoverPasswordSendOtpMutation();

  const handleChange = (name: string, value: string) => {
    setState({
      ...state,
      [name]: value,
    });
    setRecoverEmail(value);
  };

  const handleSendRecoveryEmail = (event: SyntheticEvent) => {
    event.preventDefault();
    sendOtp({
      username: state.user_name,
    })
      .unwrap()
      .then((data) => {
        toast.success(
          tr(
            "We've sent you a recovery code to your email address. Please check your inbox.",
          ),
        );
        setRecoverEmail(state.user_name);
        changeView("verify_recovery_code");
      })
      .catch((error) => {
        toast.error(restGetErrorMessage(error));
      });
  };

  return (
    <>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleSendRecoveryEmail(e);
        }}
        className={"mp-slide-in"}
      >
        <div className={"username-or-email"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
              {tr("Email or Username")}
            </Typography>
            <input
              required={true}
              type={"user_name"}
              onInput={(event) => {
                handleChange("user_name", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={state.user_name}
            />
          </label>
        </div>
        <p className={"mt-2 mb-2 text-base"}>
          {tr("We'll send you a recovery code to your email address.")}
        </p>
        <LoadingButton
          title={tr("Proceed")}
          type={"submit"}
          loading={isLoadingSendOtp}
          variant={"contained"}
        >
          {tr("Send recovery code")}
        </LoadingButton>
        <p className="mb-0 mt-4 text-gray-500 text-center">
          {tr("Already have an account?")}
          {` `}
          <button
            className={classNames(
              "!p-0 !m-0 !border-0 !bg-transparent !text-jap-primary hover:text-jap-primary-hover",
              "text-base font-normal",
              "cursor-pointer font-normal hover:font-medium hover:underline",
              "text-hre-primary hover:text-gray-400",
            )}
            onClick={(e) => changeView("login")}
          >
            <span className="ml-2">{tr("click here")}</span>
          </button>
        </p>
      </form>
    </>
  );
};

export interface RecoverVerifyEmailProps extends _ChangeView {
  setRecoverEmail: (email: string) => void;
}
