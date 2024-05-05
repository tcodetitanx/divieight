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
export default function BuyerOnboardingProcess1() {
  // State to manage the modal visibility
  const [openModalEliteAccess, setOpenModalEliteAccess] = React.useState(false);
  const [openModalBuyerAgent, setOpenModalBuyerAgent] = React.useState(false);

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
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            Congratulations! Now take just 3 easy steps and you’ll be on your
            way to affordably acquiring the second home you’ve always wanted!
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            <span className={"font-semibold"}>Step One</span> - get{" "}
            <a
              href={"#"}
              onClick={(e) => {
                e.preventDefault();
                setOpenModalEliteAccess(true);
              }}
              className={"text-hre font-semibold decoration-none"}
            >
              {tr("elite access")}
            </a>{" "}
            to our growing property database. Exclusive access is granted. Then,
            you’ll also have access to our exclusive video library for private
            tutoring. Also, your elite access lets you view our proprietary
            transaction documents and lots more!
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            <span className={"font-semibold"}>Step Two</span> - tell us your
            preferences for second home co-ownership (location, budget,
            amenities, etc.)
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            <span className={"font-semibold"}>Step Three</span> - tell us if you
            need our recommendation for a{" "}
            <a
              href={"#"}
              className={"text-hre font-semibold"}
              onClick={(e) => {
                e.preventDefault();
                setOpenModalBuyerAgent(true);
              }}
            >
              {tr("Buyer Agent")}
            </a>{" "}
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            Then, get ready to receive alerts and notifications for co-ownership
            opportunities.
          </Typography>
          <div>
            <LoadingButton
              variant="contained"
              loading={false}
              className=""
              type="submit"
              color={"primary"}
              onClick={(e) => {
                // setOpenModalEliteAccess(true);
                toast.success(
                  tr("Please wait while we redirect you to the next step."),
                );
                window.location.href =
                  getClientData().client_settings.buyer_elite_signup_page_url;
              }}
            >
              {tr("Continue")}
            </LoadingButton>
          </div>
        </div>
        <CustomizedDialog
          open={openModalEliteAccess}
          onClose={() => setOpenModalEliteAccess(false)}
          title={tr("Elite Access")}
          content={renderEliteAccessModalContent()}
          actions={renderModalActionEliteAccess()}
        />
        <CustomizedDialog
          open={openModalBuyerAgent}
          onClose={() => setOpenModalBuyerAgent(false)}
          title={tr("Buyer Agent")}
          content={renderBuyerAgentModalContent()}
          actions={renderModalActionBuyerAgent()}
        />
      </div>
    );
  }

  function renderEliteAccessModalContent() {
    return (
      <div className={"flex flex-col gap-3"}>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          Get started now with your fully refundable Initial Process Fee of{" "}
          <span className={"text-hre font-semibold"}>
            <span
              dangerouslySetInnerHTML={{
                __html: he.decode(buyerAccessFee.toString()),
              }}
            ></span>
          </span>
          . For six months you’ll then get elite access to not only search and
          view our property database but also our exclusive and proprietary
          transaction documents, including the Priority Reservation Agreement
          and the Co-ownership Operating Agreement. Plus, you’ll have exclusive
          and elite access to our video tutorial library. You’ll also receive
          timely notifications and alerts of events, listings and opportunities
          for co-ownership. And, you’ll have priority access to the divieight
          team for questions on independent co-ownership (indy-co). Yes, you’ll
          get all of this and more with the payment of your IPF.
        </Typography>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          Your IPF payment gets you into our database. It also gets you entered
          into our blockchain which regulates and controls the movement of funds
          and their timing.
        </Typography>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          <span className={"font-semibold"}>Note:</span> Proceeds of the IPF
          funds are donated to homeless shelters throughout the U.S. and to
          other humanitarian charities.
        </Typography>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          All Initial Process Fee funds are fully refundable after 3 months if
          you have not purchased a real estate property (restrictions apply). By
          making payment and creating a log in, you accept and agree to the{" "}
          <a
            href={cs.terms_and_conditions_page_url}
            target={"_blank"}
            className={"text-hre font-semibold decoration-none"}
          >
            {tr("Terms and Conditions")}
          </a>{" "}
          of our process shown on this website.
        </Typography>
      </div>
    );
  }

  function renderBuyerAgentModalContent() {
    return (
      <div className={"flex flex-col gap-3"}>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          The Buyer’s Agent is indispensable to the process of acquiring
          independent co-ownership (indy-co). divieight respects and works with
          Buyers Agents knowledgeable of the indy-co purchase process. At your
          request, we will refer an agent to you familiar with the market where
          you want to purchase.
        </Typography>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
          You can let us know later if you need a recommendation. But for now,
          get started with your{" "}
          <a
            href={"#"}
            onClick={(e) => {
              e.preventDefault();
              setOpenModalEliteAccess(true);
            }}
            className={"text-hre font-semibold decoration-none"}
          >
            {tr("elite access")}
          </a>{" "}
          to our growing property database
        </Typography>
      </div>
    );
  }

  function renderModalActionEliteAccess() {
    return (
      <>
        <Button onClick={() => setOpenModalEliteAccess(false)}>Close</Button>
        <Button
          variant="contained"
          onClick={() => {
            setOpenModalBuyerAgent(false);
            setOpenModalEliteAccess(false);
            toast.success(
              tr("Please wait while we redirect you to the next step."),
            );
            window.location.href =
              getClientData().client_settings.buyer_elite_signup_page_url;
          }}
        >
          {tr("Get Started Now")}
        </Button>
      </>
    );
  }

  function renderModalActionBuyerAgent() {
    return (
      <>
        <Button onClick={() => setOpenModalBuyerAgent(false)}>Close</Button>
        <Button
          variant="contained"
          onClick={() => {
            setOpenModalBuyerAgent(false);
            setOpenModalEliteAccess(true);
          }}
        >
          {tr("Continue")}
        </Button>
      </>
    );
  }
}
