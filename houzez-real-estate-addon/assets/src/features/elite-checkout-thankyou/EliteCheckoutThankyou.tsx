import * as React from "react";
import { Typography } from "@mui/material";
import { LoadingButton } from "@mui/lab";
import { tr } from "../../i18n/tr";

export default function EliteCheckoutThankyou({
  elitePageUrl,
}: MenuSubscriptionsProps) {
  return render();

  function render() {
    return (
      <div
        className={
          "max-w-[500px] my-4 rounded-md border-hre-10 p-4 border border-solid"
        }
      >
        <div className={"flex flex-col gap-3 "}>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            Thank you for your Initial Process Fee. Now you have elite access
            and use of our process and resources.
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            <span className={"font-semibold text-hre"}>Note:</span> Proceeds of
            the IPF funds are donated to homeless shelters throughout the U.S.
            and to other humanitarian charities.{" "}
          </Typography>
          <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 "}>
            All Initial Process Fee funds are fully refundable after 3 months
            (less credit card transaction fees) if you have not purchased a real
            estate property through use of our system. As the blockchain is time
            sensitive, refunds are made only on request which must be received
            by us in writing within five (5) days after three (3) months from
            your IPF payment. Unless you seek refund, you’ll have elite access
            to our process for six (6) months.
          </Typography>
          <div className={"flex gap-3"}>
            <LoadingButton
              variant="contained"
              loading={false}
              className=""
              type="submit"
              color={"primary"}
              href={"/"}
            >
              {tr("Go to Home")}
            </LoadingButton>
            <LoadingButton
              variant="contained"
              loading={false}
              className=""
              type="submit"
              color={"primary"}
              href={elitePageUrl}
            >
              {tr("View Elite Content")}
            </LoadingButton>
          </div>
        </div>
      </div>
    );
  }

  function renderContent() {}
}

export type MenuSubscriptionsProps = {
  elitePageUrl: string;
};
