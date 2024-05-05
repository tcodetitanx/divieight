import React from "react";
import { ApiHelper } from "../../libs/APIHelper";
import { LinearProgress } from "@mui/material";
import { tr } from "../../i18n/tr";

export default function MainWrapper(props: MainWrapperProps) {
  if (props.isLoading) {
    return <LinearProgress className="my-4" />;
    // return <div className="main-wrapper">Loading...</div>;
  }

  if (props.error) {
    return (
      <div className="main-wrapper">
        {ApiHelper.restGetErrorMessage(props.error)}
      </div>
    );
  }

  if (!props.data) {
    return <div className="main-wrapper">{tr("No data")}</div>;
  }
  return <div className="main-wrapper">{props.children}</div>;
}

export interface MainWrapperProps {
  isLoading: boolean;
  children?: React.ReactNode;
  error: any;
  data: any;
}
