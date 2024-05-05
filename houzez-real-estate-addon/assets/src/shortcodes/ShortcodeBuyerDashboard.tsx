// @ts-ignore
import React, { useState } from "react";
import { LinearProgress, Typography } from "@mui/material";
import { tr } from "../i18n/tr";
import { useFetchBatchGetBuyerApplicationsQuery } from "../rtk/myapi";
import UserDebugInfo from "../features/auth/debug-info/UserDebugInfo";
import { getClientData } from "../libs/client-data";
import EliteAccessExpired from "../features/auth/expired/EliteAccessExpired";

const cd = getClientData();
export default function ShortcodeBuyerDashboard() {
  const [showDebug, setShowDebug] = useState<number>(0);
  const {
    data: buyerApplications,
    isLoading: isLoadingApplications,
    isError: isErrorApplications,
  } = useFetchBatchGetBuyerApplicationsQuery({
    page: 1,
    posts_per_page: 1000,
  });
  return render();

  function render() {
    if (cd.client_settings.user_elite_fee_has_expired) {
      return <EliteAccessExpired />;
    }
    if (isLoadingApplications) {
      return <LinearProgress />;
    }
    if (isErrorApplications) {
      return (
        <div
          className={
            "p-4 rounded-md bg-gray-50 border border-solid border-gray-100 my-3"
          }
        >
          {tr("Error loading your property applications")}
        </div>
      );
    }
    return (
      <div className={"p-4 border border-solid border-gray-300 rounded-md"}>
        {renderHeader()}
        {renderTable()}
        <br />
        {showDebug > 4 && <UserDebugInfo />}
      </div>
    );
  }

  function renderHeader() {
    return (
      <div
        className={
          "border !border-r-0 !border-t-0 !border-l-0 border-b-1 pb-3 mb-3 text-xl font-medium text-left"
        }
        onClick={(e) => setShowDebug(showDebug + 1)}
      >
        {tr("My property applications")}
      </div>
    );
  }

  function renderTable() {
    return (
      <table className={"border-collapse table-auto w-full text-sm"}>
        <thead>
          <tr>
            <th>{tr("Property")}</th>
            <th>{tr("Date")}</th>
          </tr>
        </thead>
        <tbody>
          {buyerApplications.map((app) => {
            return (
              <tr>
                <td className={"!py-3"}>
                  <a className={"!font-medium"} href={app.property_url}>
                    {app.property_name}
                  </a>
                </td>
                <td className={"!py-3"}>{app.created}</td>
              </tr>
            );
          })}
        </tbody>
      </table>
    );
  }
}
// class ShortcodePropertyAgreement extends React.Component<ShortcodePropertyAgreementProps, ShortcodePropertyAgreementState> {
