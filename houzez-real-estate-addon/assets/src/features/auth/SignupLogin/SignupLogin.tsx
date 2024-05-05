import React, { useEffect, useState } from "react";
import Login from "../Login/Login";
import Signup from "../Signup/Signup";
import { getClientData } from "../../../libs/client-data";
import { RecoverSendRecoveryCode } from "../password-recorvery/RecoverSendRecoveryCode/RecoverSendRecoveryCode";
import { RecoverVerifyRecoveryCode } from "../password-recorvery/RecoverVerifyRecoveryCode/RecoverVerifyRecoveryCode";
import { RecoverResetPassword } from "../password-recorvery/RecoverResetPassword/RecoverResetPassword";

export default function SignupLogin({ showSignup }: SignupLoginProps) {
  const logoUrl = getClientData().logo_url;

  const [state, setState] = useState<SignupLoginState>({
    currentView: !showSignup ? "login" : "signup",
    recoveryEmail: "",
    recoveryCode: "",
  });

  useEffect(() => {
    // Reset the recovery code  and recovery email when the user is in the login page.
    if ("login" === state.currentView) {
      setState({
        ...state,
        recoveryEmail: "",
        recoveryCode: "",
      });
    }
  }, [state.currentView]);

  const changeView = (view: _ChangeViewTypes) => {
    setState({
      ...state,
      currentView: view,
    });
  };

  const setRecoveryEmail = (email: string) => {
    setState({
      ...state,
      recoveryEmail: email,
    });
  };

  const setRecoveryCode = (code: string) => {
    setState({
      ...state,
      recoveryCode: code,
    });
  };

  return (
    <div className={"max-w-[500px] m-auto"}>
      <div className="logo-wrap mb-4">
        <div
          style={{
            backgroundImage: `url(${logoUrl})`,
          }}
          className={
            "w-[150px] h-[150px] rounded-full bg-cover bg-center m-auto"
          }
        ></div>
      </div>
      {"login" === state.currentView && <Login changeView={changeView} />}
      {"signup" === state.currentView && <Signup changeView={changeView} />}
      {"send_recovery_code" === state.currentView && (
        <RecoverSendRecoveryCode
          setRecoverEmail={setRecoveryEmail}
          changeView={changeView}
        />
      )}
      {"verify_recovery_code" === state.currentView && (
        <RecoverVerifyRecoveryCode
          recoveryUserName={state.recoveryEmail}
          setRecoveryCode={setRecoveryCode}
          changeView={changeView}
        />
      )}
      {"reset_password" === state.currentView && (
        <RecoverResetPassword
          recoveryUserName={state.recoveryEmail}
          recoveryCode={state.recoveryCode}
          setRecoveryCode={setRecoveryCode}
          changeView={changeView}
        />
      )}
    </div>
  );
}

export interface SignupLoginProps {
  showSignup?: boolean;
}

export type _ChangeViewTypes =
  | "login"
  | "signup"
  | "send_recovery_code"
  | "verify_recovery_code"
  | "reset_password";

export interface _ChangeView {
  changeView(view: _ChangeViewTypes): void;
}

interface SignupLoginState {
  currentView: _ChangeViewTypes;
  recoveryEmail: string;
  recoveryCode: string;
}
