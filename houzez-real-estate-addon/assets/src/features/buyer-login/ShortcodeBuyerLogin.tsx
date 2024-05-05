// @ts-ignore
import React, { SyntheticEvent, useState } from "react";
import * as classNames from "classnames";
import { toast } from "react-toastify";
import { Typography } from "@mui/material";
import LoadingButton from "@mui/lab/LoadingButton";
import { restGetErrorMessage, useLoginMutation } from "../../rtk/myapi";
import { tr } from "../../i18n/tr";
import { getClientData } from "../../libs/client-data";
import * as he from "he";
import { SignupProps } from "./EliteSignup";

export default function ShortcodeBuyerLogin({
  for: _for,
}: ShortcodeBuyerLoginProps) {
  const gt = getClientData();
  const [authLogin, { data: japUser, isLoading: loginLoading }] =
    useLoginMutation();
  const url = new URL(window.location.href);
  const redirect = url.searchParams.get("redirect");
  let becomeABuyerUrl = "";
  // "buyer" === _for
  //   ? getClientData().client_settings.buyer_elite_signup_page_url
  //   : getClientData().client_settings.buyer_login_page_url;

  if ("buyer" === _for) {
    becomeABuyerUrl =
      getClientData().client_settings.buyer_elite_signup_page_url;
  } else if ("seller" === _for) {
    becomeABuyerUrl =
      getClientData().client_settings.buyer_elite_signup_page_url;
  } else if ("agent" === _for) {
    becomeABuyerUrl = getClientData().client_settings.agent_signup_page_url;
  }

  // create_listing_page_url

  if (null !== redirect && redirect.length > 3) {
    becomeABuyerUrl += "?redirect=" + redirect;
  }

  const [state, setState] = React.useState({
    email: "",
    password: "",
    for: _for,
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
        // Take buyer to their elite page or the redirect page.
        if (null !== redirect && redirect.length > 3) {
          window.location.href = redirect;
        } else {
          if ("buyer" === _for) {
            window.location.href =
              gt.client_settings.buyer_elite_login_page_url;
          } else if ("seller" === _for) {
            window.location.href =
              gt.client_settings.seller_elite_login_page_url;
          } else if ("agent" === _for) {
            window.location.href = gt.client_settings.agent_login_page_url;
          }
          // window.location.href =
          //   "buyer" === _for
          //     ? getClientData().client_settings.buyer_elite_page_url
          //     : getClientData().client_settings.seller_elite_page_url;
        }
      })
      .catch((error) => {
        toast.error(restGetErrorMessage(error));
      });
  }

  return (
    <form
      onSubmit={handleLogin}
      className="m-auto max-w-[400px] shadow border border-solid border-gray-50 p-4 mp-slide-in bg-white "
    >
      <div className={"flex flex-col gap-3"}>
        {/* Email */}
        <div className={"email-username"}>
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
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
          <label className={"w-full text-base"}>
            <Typography variant={"h6"} className={"mt-2 !mb-1 !p-0 text-base"}>
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
      </div>
      <br />
      <LoadingButton
        title={tr("Login")}
        type={"submit"}
        loading={loginLoading}
        variant={"contained"}
        className={"flex-initial "}
        color={"primary"}
      >
        {tr("Login")}
      </LoadingButton>
      <p className="mb-0 mt-4 text-gray-500 text-center">
        {"agent" === _for ? tr("Not yet an agent?") : tr("Not yet a member?")}{" "}
        <a href={becomeABuyerUrl} className={"text-hre"}>
          {"agent" === _for
            ? tr("Become an agent?")
            : tr("Become an elite member")}
        </a>
      </p>
    </form>
  );
}

export interface ShortcodeBuyerLoginProps {
  for: "buyer" | "seller" | "agent";
}
