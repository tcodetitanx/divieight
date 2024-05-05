import * as React from "react";
import { LoadingButton } from "@mui/lab";
import { DateTimePicker } from "@mui/x-date-pickers";
import { LinearProgress, Typography } from "@mui/material";
import { tr } from "../../../i18n/tr";
import {
  useGetUserDebugInfoQuery,
  useSaveUserDebugInfoMutation,
} from "../../../rtk/myapi";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { LocalizationProvider } from "@mui/x-date-pickers/LocalizationProvider";
import * as dayjs from "dayjs";
import { toast } from "react-toastify";
import { UserDebugInfoType } from "../../../my-types";

export default function UserDebugInfo() {
  const [debugInfo, setDebugInfo] = React.useState<UserDebugInfoType>({
    custom_current_datetime: "",
  });
  const [saveUserDebugInfo, { isLoading: isLoadingSaveUserDebugInfo }] =
    useSaveUserDebugInfoMutation();
  const {
    data: debugInfoData,
    isLoading: isLoadingGetUserDebugInfo,
    error: errorGetUserDebugInfo,
  } = useGetUserDebugInfoQuery();

  React.useEffect(() => {
    if (debugInfoData) {
      setDebugInfo(debugInfoData);
    }
  }, [debugInfoData]);

  return render();

  function render() {
    return renderDebug();
  }

  function renderDebug() {
    if (isLoadingGetUserDebugInfo) {
      return <LinearProgress />;
    }
    return (
      <form
        className={
          "debug-form shadow p-4 border border-solid border-1 border-gray-400 my-4"
        }
      >
        <LocalizationProvider dateAdapter={AdapterDayjs}>
          <Typography variant={"body2"}>
            {tr("Custom Current Date")}{" "}
            <b>{debugInfo.custom_current_datetime}</b>
          </Typography>
          {/*referenceDate={dayjs("2022-04-17T15:30")}*/}
          <DateTimePicker
            defaultValue={dayjs(debugInfo.custom_current_datetime).format(
              "ddd, DD MMM YYYY HH:mm:ss [GMT]",
            )}
            onChange={(e) => {
              setDebugInfo({
                ...debugInfo,
                custom_current_datetime: dayjs(e.toString()).format(
                  "ddd, DD MMM YYYY HH:mm:ss [GMT]",
                ),
              });
            }}
          />
        </LocalizationProvider>
        <br />
        <LoadingButton
          variant="contained"
          color="primary"
          type={"button"}
          loading={isLoadingSaveUserDebugInfo}
          loadingPosition="start"
          onClick={() => handleSaveUserDebugInfo()}
          className={"mt-4"}
        >
          {tr("Save")}
        </LoadingButton>
      </form>
    );
  }

  function handleSaveUserDebugInfo() {
    saveUserDebugInfo({
      ...debugInfo,
      custom_current_datetime: dayjs(debugInfo.custom_current_datetime).format(
        "YYYY-MM-DD HH:mm:ss",
      ),
    })
      .unwrap()
      .then((data) => {
        toast.success(tr("Saved"));
      })
      .catch((error) => {
        toast.error(tr("Error"));
      });
  }
}
