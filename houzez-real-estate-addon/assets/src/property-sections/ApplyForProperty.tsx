import { tr } from "../i18n/tr";
import {
  restGetErrorMessage,
  useFetchCanBuyerApplyMutation,
  useFetchHasBuyerAppliedQuery,
  useFetCreateBuyerApplicationMutation,
} from "../rtk/myapi";
import { toast } from "react-toastify";
// @ts-ignore
import React from "react";
import { LinearProgress } from "@mui/material";
import LoadingButton from "@mui/lab/LoadingButton";
import { getClientData } from "../libs/client-data";
import { Resource } from "../libs/Resource";

export default function ApplyForProperty({
  propertyId,
}: ApplyForPropertyProps) {
  const [checkIfBuyerCanApply, { isLoading: isCheckingIfBuyerCanApply }] =
    useFetchCanBuyerApplyMutation();
  const {
    data: buyerApplication,
    isFetching: isFetchHasBuyerAppliedLoading,
    isError: isFetchHasBuyerAppliedError,
  } = useFetchHasBuyerAppliedQuery({
    propertyId,
  });

  return render();

  function render() {
    return (
      <div className={"hre-apply-element"}>
        {renderAlreadyApplied()}
        {renderApplyButton()}
      </div>
    );
  }

  function renderApplyButton() {
    return (
      <LoadingButton
        loading={isCheckingIfBuyerCanApply || isFetchHasBuyerAppliedLoading}
        onClick={handleCheckIfBuyerCanApply}
        className="btn btn-secondary w-[100px]"
        variant="contained"
        color="primary"
      >
        {tr("Apply")}
      </LoadingButton>
    );
  }

  function renderAlreadyApplied() {
    if (isFetchHasBuyerAppliedLoading) return <LinearProgress />;
    if (isFetchHasBuyerAppliedError) return null;
    return (
      <div
        className={
          "already-applied p-4 rounded-md my-3 bg-gray-50 border border-solid border-gray-100"
        }
      >
        <p>{tr("You have applied for this property.")}</p>
        <div className={"text-base flex justify-start gap-2 items-center"}>
          <span className={"text-gray-500"}>Date:</span>
          <span className={"text-gray-600 font-medium"}>
            {buyerApplication.created}
          </span>
        </div>
      </div>
    );
  }

  function handleCheckIfBuyerCanApply() {
    checkIfBuyerCanApply({ property_id: propertyId })
      .unwrap()
      .then(() => {
        // console.log("can apply");
        toast.success(
          tr("Please wait while we redirect you to the agreement page."),
        );
        setTimeout(() => {
          window.location.href =
            getClientData().client_settings.agreement_page_url +
            "?property_id=" +
            propertyId +
            "&redirect=" +
            window.location.href;
        }, 2000);
      })
      .catch((err) => {
        // console.log("can not apply", { err });
        toast.error(tr(restGetErrorMessage(err)));
      });
  }
}

export interface ApplyForPropertyProps {
  propertyId: number;
}
