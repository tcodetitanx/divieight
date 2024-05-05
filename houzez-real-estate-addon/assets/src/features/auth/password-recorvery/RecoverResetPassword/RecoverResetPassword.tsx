import React, { SyntheticEvent } from "react";
import classNames from "classnames";
import { toast } from "react-toastify";
import {
  restGetErrorMessage,
  useRecoverPasswordResetPasswordMutation,
} from "../../../../rtk/myapi";
import { Typography } from "@mui/material";
import { tr } from "../../../../i18n/tr";
import { LoadingButton } from "@mui/lab";
import { _ChangeView, _ChangeViewTypes } from "../../SignupLogin/SignupLogin";

export const RecoverResetPassword = ({
  changeView,
  setRecoveryCode,
  recoveryUserName,
  recoveryCode,
}: RecoverResetPasswordProps) => {
  const [state, setState] = React.useState({
    password: "",
    passwordConfirm: "",
  });
  // Api.
  const [
    recoverPasswordResetPassword,
    { isLoading: isLoadingRecoverPasswordResetPassword },
  ] = useRecoverPasswordResetPasswordMutation();

  /**
   * Clear the  password and password confirm fields on rerender.
   */
  React.useEffect(() => {
    setState({
      password: "",
      passwordConfirm: "",
    });
  }, [recoveryCode]);

  const handleChange = (name: string, value: string) => {
    setState({
      ...state,
      [name]: value,
    });
  };

  return (
    <>
      <form
        onSubmit={(e) => {
          e.preventDefault();
          handleResetPassword(e);
        }}
        className={"mp-slide-in"}
      >
        <div className={"password"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
              {tr("New Password")}
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
              value={state.password}
            />
          </label>
        </div>
        <div className={"password_confirm"}>
          <label>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
              {tr("Confirm Password")}
            </Typography>
            <input
              required={true}
              type={"password"}
              onInput={(event) => {
                handleChange("passwordConfirm", event.currentTarget.value);
              }}
              className={classNames(
                "w-full py-4 px-2 h-[50px]",
                "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
              )}
              value={state.passwordConfirm}
            />
          </label>
        </div>
        <br />
        <LoadingButton
          title={tr("Reset Password")}
          type={"submit"}
          loading={isLoadingRecoverPasswordResetPassword}
          variant={"contained"}
        >
          {tr("Reset Password")}
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

  function handleResetPassword(event: SyntheticEvent) {
    event.preventDefault();
    if (state.password !== state.passwordConfirm) {
      // eslint-disable-next-line no-alert
      alert(tr("Password and Confirm password must be the same"));
      return;
    }
    recoverPasswordResetPassword({
      username: recoveryUserName,
      otp: recoveryCode,
      password: state.password,
    })
      .unwrap()
      .then((res) => {
        toast.success(res);
        changeView("login");
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }
};

export interface RecoverResetPasswordProps extends _ChangeView {
  changeView(view: _ChangeViewTypes): void;
  setRecoveryCode: (code: string) => void;
  /**
   * The user name of the user that is trying to reset their password.
   */
  recoveryUserName: string;
  /**
   * The recovery code that was sent to the user.
   */
  recoveryCode: string;
}
