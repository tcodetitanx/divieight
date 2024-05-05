import React, { SyntheticEvent } from "react";
import classNames from "classnames";
import { toast } from "react-toastify";
import { _ChangeView, _ChangeViewTypes } from "../../SignupLogin/SignupLogin";
import {
  restGetErrorMessage,
  useRecoverPasswordVerifyOtpMutation,
} from "../../../../rtk/myapi";
import { Typography } from "@mui/material";
import { tr } from "../../../../i18n/tr";
import { LoadingButton } from "@mui/lab";

export const RecoverVerifyRecoveryCode = ({
  changeView,
  setRecoveryCode,
  recoveryUserName,
}: RecoverVerifyRecoveryCodeProps) => {
  const [state, setState] = React.useState({
    recoverCode: "",
  });
  // Api.
  const [recoverVerifyOtp, { isLoading: isLoadingRecoverPasswordVerifyOtp }] =
    useRecoverPasswordVerifyOtpMutation();

  const handleChange = (name: string, value: string) => {
    setState({
      ...state,
      [name]: value,
    });
    setRecoveryCode(value);
  };

  return (
    <>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleVerifyOtp(state.recoverCode, recoveryUserName);
        }}
        className={"mp-slide-in"}
      >
        <div className={"recorvery-code"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
              {tr("Recovery code")}
            </Typography>
            <input
              required={true}
              type={"password"}
              onInput={(event) => {
                handleChange("recoverCode", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={state.recoverCode}
            />
          </label>
        </div>
        <p className={"mt-2 mb-2 text-base"}>
          {tr("Please provide the code sent to your email")}
        </p>
        <LoadingButton
          title={tr("Verify")}
          type={"submit"}
          loading={isLoadingRecoverPasswordVerifyOtp}
          variant={"contained"}
        >
          {tr("Verify")}
        </LoadingButton>
        <p className="mb-0 mt-4 text-gray-500 text-center">
          {tr("Already a member?")}{" "}
          <button
            className={classNames(
              "text-jap-primary text-center",
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
    </>
  );

  function handleVerifyOtp(otp: string, username: string) {
    recoverVerifyOtp({
      username,
      otp,
    })
      .unwrap()
      .then((res) => {
        toast.success(res);
        setRecoveryCode(state.recoverCode);
        changeView("reset_password");
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }
};

export interface RecoverVerifyRecoveryCodeProps extends _ChangeView {
  changeView(view: _ChangeViewTypes): void;
  setRecoveryCode: (code: string) => void;
  recoveryUserName: string;
}
