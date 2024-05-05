// @ts-ignore
import React, { useState } from "react";
import { Typography } from "@mui/material";
import { tr } from "../i18n/tr";
import SignatureCanvas from "react-signature-canvas";
import LoadingButton from "@mui/lab/LoadingButton";
import ClearIcon from "@mui/icons-material/Clear";
import Button from "@mui/material/Button";
import {
  restGetErrorMessage,
  useFetCreateBuyerApplicationMutation,
} from "../rtk/myapi";
import { toast } from "react-toastify";
// @ts-ignore
import ReactQuill from "react-quill";
import {
  convertAgreementToInput,
  ConvertedAgreement,
} from "../Services/AgreementService";
import { Resource } from "../libs/Resource";

export default function ShortcodePropertyAgreement() {
  const signatureRef = React.useRef<any>(null);
  const url = new URL(window.location.href);
  const [signMode] = useState(
    jQuery(".hre-sc-property-agreement").attr("data-sign-mode"),
  );
  const redirect = url.searchParams.get("redirect");
  const [agreement, setAgreement] = useState<string>("");
  const [agreement2, setAgreement2] = useState<string>("");
  const [createBuyerApplication, { isLoading: isLoadingCreateApplication }] =
    useFetCreateBuyerApplicationMutation();
  const [agreement1Inputs, setAgreement1Inputs] = useState<
    ConvertedAgreement["inputs"]
  >({});
  const [agreement2Inputs, setAgreement2Inputs] = useState<
    ConvertedAgreement["inputs"]
  >({});

  // Initialize agreements.
  if ("complex" === signMode) {
    React.useEffect(() => {
      const elemAgreement1 = jQuery(".hre-shortcode-property-agreement1");
      const elemAgreement2 = jQuery(".hre-shortcode-property-agreement2");
      const elemLoading = jQuery(".hre-loading-agreement");

      const convert1 = convertAgreementToInput(elemAgreement1.html());
      const convert2 = convertAgreementToInput(elemAgreement2.html());

      setAgreement(convert1.html);
      setAgreement2(convert2.html);

      setAgreement1Inputs(convert1.inputs);
      setAgreement2Inputs(convert2.inputs);

      elemLoading.hide();
    }, []);

    // Add events to all inputs.
    React.useEffect(() => {
      const elemBody = jQuery("body");
      elemBody.on("input", ".hre-agreement1-wrapper .hre-form-input", (e) => {
        const name = jQuery(e.target).attr("name");
        const value = jQuery(e.target).val().toString();
        handleUpdateAgreementInputs(1, name, value);
      });

      elemBody.on("input", ".hre-agreement2-wrapper .hre-form-input", (e) => {
        const name = jQuery(e.target).attr("name");
        const value = jQuery(e.target).val();
        handleUpdateAgreementInputs(2, name, value.toString());
      });

      return () => {
        elemBody.off("input", ".hre-agreement1-wrapper .hre-form-input");
        elemBody.off("input", ".hre-agreement2-wrapper .hre-form-input");
      };
    });
  }

  return (
    <form
      onSubmit={handleApply}
      className={"shortcode-property-agreement-element"}
    >
      {"simple" === signMode && (
        <>
          {renderAgreementCheckbox()}
          {renderSignatureNotSigned()}
        </>
      )}
      {"complex" === signMode && (
        <>
          {renderDefaultAgreement1()}
          {renderDefaultAgreement2()}
        </>
      )}
      {renderApplyButton()}
    </form>
  );

  function renderDefaultAgreement1() {
    return (
      <div className="w-full flex flex-col gap-1 hre-agreement-section hre-agreement1-wrapper">
        <div className={"pt-4 ql-editor"}>
          <div
            className={"border border-solid border-gray-100 p-3 my-2"}
            dangerouslySetInnerHTML={{ __html: agreement }}
          />
          {/*<ReactQuill theme="snow" value={agreement} onChange={setAgreement} />*/}
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

  function renderAgreementCheckbox() {
    return (
      <label
        className={"w-full flex justify-start gap-2 items-center text-base"}
      >
        <input
          type="checkbox"
          name={"agree"}
          required={true}
          className={"!m-0"}
        />
        <span className={"text-gray-500 "}>
          {tr("I agree to the terms and conditions")}
        </span>
      </label>
    );
  }

  function renderSignatureNotSigned() {
    return (
      <div className={"signature-wrapper flex flex-col gap-3"}>
        <div className={"text-base font-bold mb-1"}>{tr("Signature")}</div>
        <Typography variant={"body1"} className={"text-gray-400"}>
          {tr("Please sign below")}
        </Typography>
        <div>
          <span
            className={
              "inline-block bg-white border border-solid border-gray-100"
            }
          >
            <SignatureCanvas
              classNames="m-auto block shadow p-4 border border-solid"
              ref={signatureRef}
              penColor="green"
              canvasProps={{
                width: 300,
                height: 200,
                className: "sigCanvas",
              }}
            />
          </span>
        </div>
        <div className="signature-actions flex justify-center gap-4 py-2">
          <Button
            variant={"outlined"}
            onClick={() => {
              signatureRef.current.clear();
            }}
            startIcon={<ClearIcon />}
          >
            {tr("Clear")}
          </Button>
        </div>
      </div>
    );
  }

  function renderApplyButton() {
    return (
      <LoadingButton
        loading={isLoadingCreateApplication}
        className="btn btn-secondary w-[100px]"
        variant="contained"
        color="primary"
        type="submit"
      >
        {tr("Apply")}
      </LoadingButton>
    );
  }

  function handleApply(e) {
    e.preventDefault();
    let signatureUrl = "";

    if ("simple" === signMode) {
      if (signatureRef.current.isEmpty()) {
        // eslint-disable-next-line no-alert
        alert(tr("Please sign the agreement"));
        return;
      }
      signatureUrl = signatureRef.current.toDataURL();
    }

    createBuyerApplication({
      property_id: parseInt(url.searchParams.get("property_id")),
      signature_url: signatureUrl,
      agreement_1_inputs: agreement1Inputs,
      agreement_2_inputs: agreement2Inputs,
    })
      .unwrap()
      .then(() => {
        toast.success(tr("Application submitted successfully."));
        setTimeout(() => {
          window.location.href = redirect;
        }, 2000);
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }

  function handleUpdateAgreementInputs(
    aggreement: 1 | 2,
    name: string,
    value: string,
  ) {
    console.log({ aggreement, name, value });
    if (aggreement === 1) {
      setAgreement1Inputs((prev) => {
        return {
          ...prev,
          [name]: value,
        };
      });
    } else {
      setAgreement2Inputs((prev) => {
        return {
          ...prev,
          [name]: value,
        };
      });
    }
  }
}
// class ShortcodePropertyAgreement extends React.Component<ShortcodePropertyAgreementProps, ShortcodePropertyAgreementState> {
