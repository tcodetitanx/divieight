import React from "react";
import { LinearProgress, Typography } from "@mui/material";
import { tr } from "../../../i18n/tr";
import classNames from "classnames";
import { LoadingButton } from "@mui/lab";
import {
  restGetErrorMessage,
  useFetchGetHasDoneFacialAnalysisTodayQuery,
  useFetchRecordFacialAnalysisMutation,
} from "../../../rtk/myapi";
import { toast } from "react-toastify";
import { getClientData } from "../../../libs/client-data";

export default function TabFacialAnalysis({}: FacialAnalysisProps) {
  const facialAnalysisLink = getClientData().clientSettings.facial_analysis_url;
  const [recordFacialAnalysis, { isLoading: isLoadingRecordFacialAnalysis }] =
    useFetchRecordFacialAnalysisMutation();
  const fetchFaDone = useFetchGetHasDoneFacialAnalysisTodayQuery();
  return render();

  function render() {
    if (fetchFaDone.isLoading || fetchFaDone.isFetching)
      return <LinearProgress />;
    return (
      <div className="hre-tab-facial-analysis-tab">
        {fetchFaDone.isError && renderFacialButton(facialAnalysisLink)}
        {fetchFaDone.data && renderFacialAnalysisDone()}
      </div>
    );
  }

  function renderFacialButton(link: string) {
    return (
      <div
        className={classNames(
          "hre-facial-button my-4 w-full cursor-pointer  gap-6 text-center shadow py-4 px-2",
        )}
      >
        <Typography variant={"body1"}>
          {tr("Click the button below to do your facial analysis for today.")}
        </Typography>
        <LoadingButton
          onClick={handleRecordFacialAnalysis}
          className="hre-button"
          loading={isLoadingRecordFacialAnalysis}
          variant="contained"
        >
          {tr("Proceed")}
        </LoadingButton>
      </div>
    );
  }

  function renderFacialAnalysisDone() {
    return (
      <div
        className={classNames(
          "hre-facial-button my-4 text-base w-full cursor-pointer flex flex-col gap-6 text-center shadow py-4 px-2",
          "border-r-0 border-l-0 border-t-2 border-b-2 border-solid border-gray-400 bg-white",
        )}
      >
        <div>{tr("You have already done your facial analysis for today.")}</div>
        <div>{tr("Come back tomorrow for your next facial analysis.")}</div>
      </div>
    );
  }

  function handleRecordFacialAnalysis() {
    recordFacialAnalysis()
      .unwrap()
      .then((res) => {
        toast.success(tr("Facial analysis recorded successfully."));
        window.open(facialAnalysisLink, "_blank");
        fetchFaDone.refetch();
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }
}

export interface FacialAnalysisProps {}
