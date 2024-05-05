import * as React from "react";
import { Typography } from "@mui/material";
import { tr } from "../../../i18n/tr";
import { LoadingButton } from "@mui/lab";
import { getClientData } from "../../../libs/client-data";
import * as he from "he";
import {
  restGetErrorMessage,
  useCreateEliteProductMutation,
} from "../../../rtk/myapi";
import { toast } from "react-toastify";

const cd = getClientData();
export default function EliteAccessExpired() {
  const [createEliteProduct, { isLoading: isLoadingCreateEliteProduct }] =
    useCreateEliteProductMutation();
  return render();

  function render() {
    const amount =
      "buyer" === cd.client_settings.elite_role
        ? cd.client_settings.buyer_elite_access_fee_number
        : cd.client_settings.seller_elite_access_fee_number;

    return (
      <div
        className={
          "elite-access-expired max-w-[500px] rounded-md border border-hre border-solid p-4 mx-auto my-4"
        }
      >
        <Typography variant="h2" className="block w-full text-center !text-hre">
          {tr("Elite access expired!!!")}
        </Typography>
        <Typography variant="body1" className="block w-full text-center">
          Your elite access subscription has expired. Please click the button
          below to re-subscribed.
        </Typography>
        <div className="text-center">
          <LoadingButton
            variant="contained"
            loading={isLoadingCreateEliteProduct}
            className=""
            type="button"
            onClick={() => {
              handlePayEliteAccess();
            }}
          >
            Pay{" "}
            <span className={"font-bold text-xl px-2"}>
              {he.decode(cd.client_settings.currency_symbol)}
              {amount}
            </span>{" "}
            for elite access
          </LoadingButton>
        </div>
      </div>
    );
  }

  function handlePayEliteAccess() {
    createEliteProduct()
      .unwrap()
      .then((res) => {
        toast.success(tr("Wait while we redirect you to the checkout page"));
        setTimeout(() => {
          window.location.href =
            getClientData().site_url +
            "?buyer_action=add_to_cart&product_id=" +
            res.product_id;
        }, 2000);
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }
}
