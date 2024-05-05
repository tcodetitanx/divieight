import * as React from "react";
import Button from "@mui/material/Button";
import { styled } from "@mui/material/styles";
import DialogTitle from "@mui/material/DialogTitle";
import DialogContent from "@mui/material/DialogContent";
import DialogActions from "@mui/material/DialogActions";
import IconButton from "@mui/material/IconButton";
import CloseIcon from "@mui/icons-material/Close";
import Typography from "@mui/material/Typography";
import { useEffect } from "react";
import { tr } from "../../i18n/tr";
import Dialog, { DialogProps } from "@mui/material/Dialog";
import {
  restGetErrorMessage,
  useGetAgentDetailsQuery,
  useGetBuyerPreferenceQuery,
} from "../../rtk/myapi";
import TableContainer from "@mui/material/TableContainer";
import Paper from "@mui/material/Paper";
import Table from "@mui/material/Table";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import TableCell from "@mui/material/TableCell";
import TableBody from "@mui/material/TableBody";
import { Alert, LinearProgress } from "@mui/material";
import { AgentDetails, BuyerPreference } from "../../my-types";

const BootstrapDialog = styled(Dialog)(({ theme }) => ({
  "& .MuiDialogContent-root": {
    padding: theme.spacing(2),
  },
  "& .MuiDialogActions-root": {
    padding: theme.spacing(1),
  },
}));

export default function BuyerAgentDetailsModal() {
  const [open, setOpen] = React.useState(false);
  const [agentId, setAgentId] = React.useState(0);
  const [fullWidth, setFullWidth] = React.useState(true);
  const [maxWidth, setMaxWidth] = React.useState<DialogProps["maxWidth"]>("sm");
  const [agentDetails, setAgentDetails] = React.useState<null | AgentDetails>(
    null,
  );

  const {
    data: agentDetailsData,
    error: errorAgentDetails,
    isLoading: isLoadingAgentDetails,
    isError: isErrorAgentDetails,
    refetch: refetchAgentDetails,
    isFetching: isFetchingAgentDetails,
  } = useGetAgentDetailsQuery({
    agent_id: agentId,
  });

  const handleClickOpen = () => {
    setOpen(true);
  };
  const handleClose = () => {
    setOpen(false);
    setAgentId(0);
    setAgentDetails(null);
  };

  useEffect(() => {
    setAgentDetails(agentDetailsData);
  }, [agentDetailsData]);

  useEffect(() => {
    // console.log("refetch");
    refetchAgentDetails();
  }, [agentId]);

  useEffect(() => {
    jQuery(document).on("hre_open_modal_agent_details", function (e, arg1) {
      // console.log({ arg1 });
      setAgentId(() => arg1);
      setOpen(() => true);
    });

    // setUserId(userId);
    // handleClickOpen();
  }, []);

  return (
    <React.Fragment>
      {/*<Button variant="outlined" onClick={handleClickOpen}>*/}
      {/*  Open dialog*/}
      {/*</Button>*/}
      <BootstrapDialog
        onClose={handleClose}
        aria-labelledby="customized-dialog-title"
        open={open}
        fullWidth={fullWidth}
        maxWidth={maxWidth}
      >
        <DialogTitle sx={{ m: 0, p: 2 }} id="customized-dialog-title">
          {tr("Agent Details")}
        </DialogTitle>
        <IconButton
          aria-label="close"
          onClick={handleClose}
          sx={{
            position: "absolute",
            right: 8,
            top: 8,
            color: (theme) => theme.palette.grey[500],
          }}
        >
          <CloseIcon />
        </IconButton>
        <DialogContent dividers>
          {/*<h1>The modal content</h1>*/}
          {renderShortcodeLists()}
        </DialogContent>
        <DialogActions>
          {/*<Button autoFocus onClick={handleClose}>*/}
          {/*  Save changes*/}
          {/*</Button>*/}
        </DialogActions>
      </BootstrapDialog>
    </React.Fragment>
  );

  function renderShortcodeLists() {
    if (isLoadingAgentDetails || isFetchingAgentDetails) {
      return <LinearProgress />;
    }
    if (isErrorAgentDetails) {
      <Alert severity="error">{restGetErrorMessage(errorAgentDetails)}</Alert>;
    }
    if ([null, undefined].includes(agentDetails)) {
      return <Alert severity="error">{tr("No data")}</Alert>;
    }
    return (
      <div>
        <TableContainer component={Paper}>
          <Table
            sx={{ minWidth: 350 }}
            size="medium"
            aria-label="a dense table"
          >
            <TableHead>
              <TableRow>
                <TableCell className={"font-semibold"}>{tr("Title")}</TableCell>
                <TableCell className={"font-semibold"}>{tr("Value")}</TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {Object.keys(agentDetails).map((key) => {
                // @ts-ignore
                const title = key.replaceAll("_", " ");
                return (
                  <TableRow
                    key={key}
                    sx={{ "&:last-child td, &:last-child th": { border: 0 } }}
                  >
                    <TableCell component="th" scope="row">
                      <span className="capitalize">{title}</span>
                    </TableCell>
                    <TableCell>{agentDetails[key]}</TableCell>
                  </TableRow>
                );
              })}
            </TableBody>
          </Table>
        </TableContainer>
      </div>
    );
  }
}
