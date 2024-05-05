import * as React from "react";
import { Typography } from "@mui/material";
import Button from "@mui/material/Button";
import CustomizedDialog from "../../components/CustomizedDialog";
import Box from "@mui/material/Box";
import { tr } from "../../i18n/tr";
import { LoadingButton } from "@mui/lab";
import { getClientData } from "../../libs/client-data";
import * as he from "he";
import { toast } from "react-toastify";

const buyerAccessFee = getClientData().client_settings.buyer_elite_access_fee;
const cs = getClientData().client_settings;
export default function BuyerOnboardingProcess2() {
  // State to manage the modal visibility

  return render();

  function render() {
    return (
      <div className={"hre-buyer-onboarding-process-1"}>{renderContent()}</div>
    );
  }

  function renderContent() {
    return (
      <div
        className={
          "max-w-[500px] m-auto rounded-md border-hre-10 p-4 border border-solid"
        }
      >
        <div className={"flex flex-col gap-3 "}>
          <Typography
            variant={"h3"}
            color={"primary"}
            className={"mt-2 text-center !mb-1 !p-0"}
          >
            Welcome!
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            We’re glad you’re here! This is no ordinary real estate property
            database. It is viewed exclusively by those having elite access.
            Let’s get you started!
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            With your modest, and fully refundable, Initial Process Fee, you’ll
            then get elite access to not only search and view our growing
            property database but also our transaction documents, video
            tutorials and lots more.
          </Typography>
          <div>
            <LoadingButton
              variant="contained"
              loading={false}
              className=""
              type="submit"
              color={"primary"}
              onClick={() => {
                toast.success(
                  tr("Please wait while we redirect you to the next step."),
                );
                window.location.href =
                  getClientData().client_settings.buyer_elite_signup_page_url;
              }}
            >
              {tr("Get Started Now")}
            </LoadingButton>
          </div>
        </div>
      </div>
    );
  }
}
