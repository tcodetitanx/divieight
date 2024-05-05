import React, { SyntheticEvent, useState } from "react";
import classNames from "classnames";
import { _ChangeView } from "../SignupLogin/SignupLogin";
import { toast } from "react-toastify";
import { restGetErrorMessage, useLoginMutation } from "../../../rtk/myapi";
import { Typography } from "@mui/material";
import { tr } from "../../../i18n/tr";
import LoadingButton from "@mui/lab/LoadingButton";

export interface LoginProps extends _ChangeView {}

export default function Login({ changeView }: LoginProps) {
  const [authLogin, { data: japUser, isLoading: loginLoading }] =
    useLoginMutation();

  const [state, setState] = React.useState({
    email: "",
    password: "",
  });

  function handleChange(name: string, value: string) {
    setState({
      ...state,
      [name]: value.trim(),
    });
  }

  function handleLogin(event: SyntheticEvent) {
    event.preventDefault();
    authLogin({
      username: state.email,
      password: state.password,
    })
      .unwrap()
      .then((data) => {
        toast.success("Login successful.");
        // Reload page.
        window.location.reload();
      })
      .catch((error) => {
        toast.error(restGetErrorMessage(error));
      });
  }

  return (
    <form
      onSubmit={handleLogin}
      className="m-auto max-w-[600px] mp-slide-in bg-white flex flex-col gap-3"
    >
      {/* Email */}
      <div className={"email-username"}>
        <label>
          <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0"}>
            {tr("Email or Username")}
          </Typography>
          <input
            required={true}
            type={"text"}
            onInput={(event) => {
              handleChange("email", event.currentTarget.value);
            }}
            className={classNames(
              "w-full py-4 px-2 h-[50px]",
              "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
            )}
            value={state.email}
          />
        </label>
      </div>
      <div className={"email-username"}>
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
            value={state.password}
          />
        </label>
      </div>
      <LoadingButton
        title={"Login"}
        type={"submit"}
        loading={loginLoading}
        variant={"contained"}
      >
        {tr("Login")}
      </LoadingButton>
      <p className="mb-0 mt-6 text-gray-500 text-center my-3">
        {tr("Forgot your password?")}
        <button
          className={classNames(
            "!p-0 !m-0 !border-0 !bg-transparent !text-jap-primary hover:text-jap-primary-hover",
            "text-base font-normal",
            "cursor-pointer font-normal hover:font-medium hover:underline",
            "text-hre-primary hover:text-gray-400",
          )}
          onClick={(e) => changeView("send_recovery_code")}
        >
          <span className="ml-2">{tr("click here")}</span>
        </button>
      </p>
      <p className="mb-0 mt-4 text-gray-500 text-center">
        {tr("Not yet a member?")}
        <button
          className={classNames(
            "!p-0 !m-0 !border-0 !bg-transparent !text-jap-primary hover:text-jap-primary-hover",
            "text-base font-normal",
            "cursor-pointer font-normal hover:font-medium hover:underline",
            "text-hre-primary hover:text-gray-400",
          )}
          onClick={(e) => {
            e.preventDefault();
            changeView("signup");
          }}
        >
          <span className="ml-2">{tr("Signup")}</span>
        </button>
      </p>
    </form>
  );
}
