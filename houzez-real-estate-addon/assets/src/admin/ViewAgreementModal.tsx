import * as React from "react";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Modal from "@mui/material/Modal";
import { useEffect } from "react";
import {
  convertAgreementToInput,
  ConvertedAgreement,
} from "../Services/AgreementService";

const he = require("he");

declare let jQuery: any;
declare let console: any;

const style = {
  position: "absolute" as "absolute",
  top: "50%",
  left: "50%",
  transform: "translate(-50%, -50%)",
  width: "90%",
  bgcolor: "background.paper",
  border: "2px solid #000",
  boxShadow: 24,
  p: 4,
  my: 10,
  zIndex: 999999,
};
export default function ViewAgreementModal() {
  const [open, setOpen] = React.useState(false);
  const [agreement1, setAgreement1] = React.useState("");
  const [agreement1Inputs, setAgreement1Inputs] = React.useState<
    ConvertedAgreement["inputs"]
  >({});
  const [agreement2, setAgreement2] = React.useState("");
  const [agreement2Inputs, setAgreement2Inputs] = React.useState<
    ConvertedAgreement["inputs"]
  >({});
  const handleOpen = () => setOpen(true);
  const handleClose = () => setOpen(false);

  useEffect(() => {
    console.log("useEffect");

    jQuery("body").on(
      "click",
      "button.hre-btn-view-agreement-form",
      (event: any, data: any) => {
        if (open) return;
        console.log("clicked", { event, data });
        const elemButton = jQuery(event.target);
        const elemAgreement1 = jQuery(elemButton.siblings(".hre-agreement-1"));
        const elemAgreement2 = jQuery(elemButton.siblings(".hre-agreement-2"));

        const theAgreement1 = he.decode(elemAgreement1.html());
        const theAgreement2 = he.decode(elemAgreement2.html());

        const theAgreement1Inputs: ConvertedAgreement["inputs"] = JSON.parse(
          he.decode(elemAgreement1.attr("data-inputs")),
        );
        const theAgreement2Inputs: ConvertedAgreement["inputs"] = JSON.parse(
          he.decode(elemAgreement2.attr("data-inputs")),
        );

        // console.log({ theAgreement1Inputs, theAgreement1 });

        const convertedAgreement1 = convertAgreementToInput(
          theAgreement1,
          theAgreement1Inputs,
        );
        const convertedAgreement2 = convertAgreementToInput(
          theAgreement2,
          theAgreement2Inputs,
        );

        setAgreement1(convertedAgreement1.html);
        setAgreement2(convertedAgreement2.html);
        setAgreement1Inputs(theAgreement1Inputs);
        setAgreement2Inputs(theAgreement2Inputs);

        handleOpen();
        console.log("open haneled");
      },
    );

    return () => {
      console.log("useEffect return");
      setOpen(false);
      jQuery("body").off(".hre-btn-view-agreement-form");
    };
  }, []);

  return render();

  function render() {
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
              Agreement Forms
            </Typography>
            <span
              onClick={handleClose}
              className={
                "dashicons dashicons-no cursor-pointer hover:bg-gray-200 rounded p-2"
              }
            ></span>
          </div>
          <div className="modal-body-here flex flex-col gap-2">
            {renderModalBody()}
          </div>
          <br />
        </Box>
      </Modal>
    );
  }

  function renderModalBody() {
    return (
      <div className="modal-body">
        {renderDefaultAgreement1()}
        {renderDefaultAgreement2()}
      </div>
    );
  }

  function renderDefaultAgreement1() {
    return (
      <div className="w-full flex flex-col gap-1 hre-agreement-section hre-agreement1-wrapper">
        <div className={"pt-4 ql-editor"}>
          <div
            className={"border border-solid border-gray-100 p-3 my-2"}
            dangerouslySetInnerHTML={{ __html: agreement1 }}
          />
        </div>
      </div>
    );
  }

  function renderDefaultAgreement2() {
    return (
      <div className="w-full flex flex-col gap-1 hre-agreement-section hre-agreement2-wrapper">
        <div className={"pt-4 ql-editor"}>
          <div
            className={"border border-solid border-gray-100 p-3 my-2"}
            dangerouslySetInnerHTML={{ __html: agreement2 }}
          />
        </div>
      </div>
    );
  }
}
