import * as React from "react";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";
import Typography from "@mui/material/Typography";
import Modal from "@mui/material/Modal";
import { useEffect } from "react";
import LoadingButton from "@mui/lab/LoadingButton";
import { tr } from "../i18n/tr";
import { HreApi, IsAgentApprovedResponse } from "./hre-elementor-submissions";
import { toast } from "react-toastify";
import { Resource } from "../libs/Resource";

declare let jQuery: any;
declare let console: any;

const style = {
  position: "absolute" as "absolute",
  top: "50%",
  left: "50%",
  transform: "translate(-50%, -50%)",
  width: 400,
  bgcolor: "background.paper",
  border: "2px solid #000",
  boxShadow: 24,
  p: 4,
};
export default function ApproveAgentModal() {
  const [open, setOpen] = React.useState(false);
  const [fetchSaveAdminSettingsIsLoading, setFetchSaveAdminSettingsIsLoading] =
    React.useState(false);
  const [agentDetails, setAgentDetails] = React.useState<
    Array<IsAgentApprovedResponse["more"]["agent_details"][0]>
  >([]);
  const [submissionId, setSubmissionId] = React.useState(0);
  const handleOpen = () => setOpen(true);
  const handleClose = () => setOpen(false);

  useEffect(() => {
    jQuery("body").on("show-agent-approval-modal", (event: any, data: any) => {
      const json: {
        submissionId: number;
        details: Array<IsAgentApprovedResponse["more"]["agent_details"][0]>;
      } = JSON.parse(atob(data));
      console.log("useEffect", { event, data, json });
      setAgentDetails(json.details);
      setSubmissionId(json.submissionId);
      handleOpen();
    });
  }, []);

  return (
    <Modal
      open={open}
      onClose={handleClose}
      aria-labelledby="modal-modal-title"
      aria-describedby="modal-modal-description"
    >
      <Box sx={style}>
        <div className="header flex justify-between">
          <Typography id="modal-modal-title" variant="h6" component="h2">
            Approve Agent
          </Typography>
          <span
            onClick={handleClose}
            className={
              "dashicons dashicons-no cursor-pointer hover:bg-gray-200 rounded p-2"
            }
          ></span>
        </div>
        <div className="modal-body-here flex flex-col gap-2">
          <p className={"text-sm text-gray-500 py-4"}>
            When approved, a new agent with the following details will be
            created as a user with the role "Agent"
          </p>
          {agentDetails.map((data, i) => (
            <div key={i} className="flex justify-start gap-2 text-base">
              <div className="key font-medium capitalize">{data.label}</div>
              <div className="value">{data.value}</div>
            </div>
          ))}
        </div>
        <br />
        <br />
        <LoadingButton
          variant="contained"
          loading={fetchSaveAdminSettingsIsLoading}
          className=""
          type="button"
          onClick={approveAgent}
        >
          {tr("Approve")}
        </LoadingButton>
      </Box>
    </Modal>
  );

  function approveAgent() {
    console.log("Approve Agent");
    setFetchSaveAdminSettingsIsLoading(true);
    HreApi.approveSubmission(submissionId)
      .then(() => {
        toast.success("Agent Approved");
        setTimeout(() => {
          // jQuery("body").trigger("hre-agent-approved", submissionId);
          console.log("reload;");
          window.location.reload();
          window.location.href = window.location.href;
          window.location = window.location;
        }, 2000);
      })
      .catch(() => {
        toast.error("Failed to approve agent");
        handleClose();
      })
      .finally(() => {
        setFetchSaveAdminSettingsIsLoading(false);
      });
  }
}
