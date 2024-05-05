// @ts-ignore
import React, { useState } from "react";
// @ts-ignore
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import Box from "@mui/material/Box";
import { Tab, Tabs } from "@mui/material";

const he = require("he");

export default function MetaBoxAgreement({
  agreement: agreementData,
  agreement2: agreementData2,
  signMode: signModeData,
}: MetaBoxAgreementProps) {
  const [agreement, setAgreement] = React.useState<string>(
    he.decode(agreementData),
  );
  const [agreement2, setAgreement2] = useState<string>(
    he.decode(agreementData2),
  );
  const [currentTab, setCurrentTab] = React.useState(1);
  const [signMode, setSignMode] = React.useState<"simple" | "complex">(
    signModeData,
  );

  const handleTabChange = (event: React.SyntheticEvent, newValue: string) => {
    setCurrentTab(newValue);
  };

  return render();

  function render() {
    return (
      <>
        {/*{renderDefaultAgreement()} */}
        {renderSignModeForm()}
        {renderAgreementTabs()}
        {renderFormInput()}
      </>
    );
  }

  function renderSignModeForm() {
    return (
      <div className={"sign-mode"}>
        <label
          className={
            "flex gap-3 items-center bg-gray-10 border border-solid border-gray-100 rounded p-2 " +
            "hover-bg-100 hover:border-3 cursor-pointer"
          }
        >
          <div
            className={
              "text base font-medium text-black flex gap-2 items-center"
            }
          >
            <input
              type="radio"
              name="sign_mode"
              value="simple"
              checked={signMode === "simple"}
              onChange={() => setSignMode("simple")}
              className={"scale-105 flex-initial"}
            />
            <span className={"flex-1"}>Simple Sign Mode</span>
          </div>
          <p className={"text-sm text-gray-500"}>
            Displays the 2 (non form) agreements for users to read but with a
            signature pad for them to sign
          </p>
        </label>
        <label
          className={
            "flex gap-3 items-center bg-gray-10 border border-solid border-gray-100 rounded p-2 " +
            "hover-bg-100 hover:border-3 cursor-pointer"
          }
        >
          <div
            className={
              "text base font-medium text-black flex gap-2 items-center"
            }
          >
            <input
              type="radio"
              name="sign_mode"
              value="simple"
              checked={signMode === "complex"}
              onChange={() => setSignMode("complex")}
              className={"scale-105 flex-initial"}
            />
            <span className={"flex-1"}>Complex Sign Mode</span>
          </div>
          <p className={"text-sm text-gray-500"}>
            Display the 2 (fillable form) agreements for user to read without a
            signature pad.
          </p>
        </label>
      </div>
    );
  }

  function renderFormInput() {
    return (
      <>
        <input
          name={"hre_property_agreement"}
          type="hidden"
          value={he.encode(agreement)}
        />
        <input
          name={"hre_property_agreement_2"}
          type="hidden"
          value={he.encode(agreement2)}
        />
        <input name={"hre_sign_mode"} type="hidden" value={signMode} />
      </>
    );
  }

  // function renderDefaultAgreement() {
  //   return (
  //     <ReactQuill theme="snow" value={agreement} onChange={setAgreement} />
  //   );
  // }

  function renderDefaultAgreement1() {
    return (
      <div className="w-full flex flex-col gap-1">
        <div className={"pt-4"}>
          <ReactQuill theme="snow" value={agreement} onChange={setAgreement} />
        </div>
      </div>
    );
  }

  function renderDefaultAgreement2() {
    return (
      <div className="w-full flex flex-col gap-1">
        <div className={"pt-4"}>
          <ReactQuill
            theme="snow"
            value={agreement2}
            onChange={setAgreement2}
          />
        </div>
      </div>
    );
  }

  function renderAgreementTabs() {
    return (
      <div>
        <Box sx={{ borderBottom: 1, borderColor: "divider" }}>
          <Tabs
            value={currentTab}
            onChange={handleTabChange}
            aria-label="basic tabs example"
          >
            <Tab value={1} label="Agreement 1" />
            <Tab value={2} label="Agreement 2" />
          </Tabs>
        </Box>
        {currentTab === 1 && <div>{renderDefaultAgreement1()}</div>}
        {currentTab === 2 && <div>{renderDefaultAgreement2()}</div>}
      </div>
    );
  }
}

export interface MetaBoxAgreementProps {
  agreement: string;
  agreement2: string;
  signMode: "simple" | "complex";
}
