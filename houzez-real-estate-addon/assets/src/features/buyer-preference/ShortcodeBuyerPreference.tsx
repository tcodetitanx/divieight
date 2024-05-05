import * as React from "react";
import { Typography } from "@mui/material";
import { tr } from "../../i18n/tr";
import * as classNames from "classnames";
import { BuyerPreference } from "../../my-types";
import { getClientData } from "../../libs/client-data";
import Box from "@mui/material/Box";
import { LoadingButton } from "@mui/lab";
import { useSaveBuyerPreferenceMutation } from "../../rtk/myapi";
import { toast } from "react-toastify";
import { Resource } from "../../libs/Resource";
import * as cn from "classnames";
import { getUsStates } from "../inquiry/InquiryForm";

export default function ShortcodeBuyerPreference() {
  const [buyerPreference, setBuyerPreference] = React.useState<BuyerPreference>(
    getClientData().buyer_preference,
  );
  const [saveBuyerPreference, { isLoading: isLoadingSaveBuyerPreference }] =
    useSaveBuyerPreferenceMutation();
  const handleChange = (name: keyof BuyerPreference, value: string) => {
    setBuyerPreference({
      ...buyerPreference,
      [name]: value.toString(),
    });
  };

  return (
    <form
      className={
        "p-4 border border-solid border-hre-10 rounded-md max-w-[800px] m-auto my-4"
      }
      onSubmit={handleSaveBuyerPreference}
    >
      <div className="flex flex-col gap-4">
        <h2 className={"mt-2 !mb-1 !p-0 !text-hre-10"}>
          {tr("Tell us your preferences!")}
        </h2>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 text-base"}>
          Looking to relax at a beach house? Maybe alpine skiing is your thing.
          Either way, just tell us what you have in mind. We’ll can alert you to
          opportunities. Just think, an incredible vacation home (or investment)
          at 1/8th of the cost
        </Typography>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"first_name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("First Name")}
              </Typography>
              <input
                required={true}
                type={"text"}
                name={"first_name"}
                onInput={(event) => {
                  handleChange("first_name", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-500 bg-[#F7F7F7]",
                )}
                value={buyerPreference.first_name}
              />
            </label>
          </div>
          <div className={"last_name flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Last Name")}
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("last_name", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.last_name}
              />
            </label>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"email flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Email")}
              </Typography>
              <input
                required={true}
                type={"text"}
                name={"email"}
                onInput={(event) => {
                  handleChange("email", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.email}
              />
            </label>
          </div>
          <div className={"phone flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Phone")}{" "}
                <span className={"text-gray-500 text-normal text-sm"}>
                  {tr("(xxx) xxx-xxxx")}
                </span>
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("phone", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.phone}
              />
            </label>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"first_choice flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("First choice")}{" "}
                <span className={"text-gray-500 text-normal text-sm"}>
                  {tr("Preferred city (or community)")}
                </span>
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("first_choice", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.first_choice}
              />
            </label>
          </div>
          <div className={"state flex-auto"}>
            <label className={"w-full text-base flex flex-col"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("State")}{" "}
              </Typography>
              {/*<input*/}
              {/*  required={true}*/}
              {/*  type={"text"}*/}
              {/*  onInput={(event) => {*/}
              {/*    handleChange("state", event.currentTarget.value);*/}
              {/*  }}*/}
              {/*  className={classNames(*/}
              {/*    "w-full py-4 px-2 h-[50px]",*/}
              {/*    "border rounded border-solid border-gray-200 bg-[#F7F7F7]",*/}
              {/*  )}*/}
              {/*  value={buyerPreference.state}*/}
              {/*/>*/}
              <select
                name="state"
                required
                value={buyerPreference.state}
                className={cn(
                  "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
                  "flex-auto",
                )}
                onChange={(e) => {
                  handleChange("state", e.target.value);
                }}
              >
                <option value="">{tr("Select State")}</option>
                {getUsStates().map((item) => (
                  <option value={item}>{item}</option>
                ))}
              </select>
            </label>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"preferred-budget flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Preferred Budget")}{" "}
                <span className={"text-gray-500 text-normal text-sm"}>
                  {tr("(for the purchase of 1/8 interest)")}
                </span>
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange("preferred_budget", event.currentTarget.value);
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.preferred_budget}
              />
            </label>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"no-of-18-interest flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("No of 1/8 interest")}{" "}
                <span className={"text-gray-500 text-normal text-sm"}>
                  {tr("(intended for purchase)")}
                </span>
              </Typography>
              <input
                required={true}
                type={"text"}
                onInput={(event) => {
                  handleChange(
                    "no_of_1_8th_interest",
                    event.currentTarget.value,
                  );
                }}
                className={classNames(
                  "w-full py-4 px-2 h-[50px]",
                  "border rounded border-solid border-gray-200 bg-[#F7F7F7]",
                )}
                value={buyerPreference.no_of_1_8th_interest}
              />
            </label>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"no-of-18-interest flex-auto"}>
            <Typography
              variant={"h6"}
              className={"mt-2 !mb-1 !p-0 font-medium text-base"}
            >
              {tr("Do you need recommendation for Buyer's Agent?")}{" "}
            </Typography>
            <div className="flex gap-4">
              <label className={"cursor-pointer"}>
                <div className="flex gap-3">
                  <input
                    required={true}
                    type={"radio"}
                    name={"do_you_need_recommendation_for_buyer_agent"}
                    onChange={(event) => {
                      handleChange(
                        "do_you_need_recommendation_for_buyer_agent",
                        "yes",
                      );
                    }}
                    className={classNames()}
                    checked={
                      "yes" ===
                      buyerPreference.do_you_need_recommendation_for_buyer_agent
                    }
                    value={"yes"}
                  />
                  <Typography
                    variant={"h6"}
                    className={"mt-2 !mb-1 !p-0 text-base"}
                  >
                    {tr("Yes")}{" "}
                  </Typography>
                </div>
              </label>
              <label className={"cursor-pointer"}>
                <div className="flex gap-3">
                  <input
                    required={true}
                    type={"radio"}
                    name={"do_you_need_recommendation_for_buyer_agent"}
                    onChange={(event) => {
                      handleChange(
                        "do_you_need_recommendation_for_buyer_agent",
                        "no",
                      );
                    }}
                    className={classNames()}
                    checked={
                      "no" ===
                      buyerPreference.do_you_need_recommendation_for_buyer_agent
                    }
                    value={"no"}
                  />
                  <Typography
                    variant={"h6"}
                    className={"mt-2 !mb-1 !p-0 text-base"}
                  >
                    {tr("No")}{" "}
                  </Typography>
                </div>
              </label>
            </div>
          </div>
        </div>
        <div className="lg:flex flex-wrap gap-2 justify-between">
          <div className={"comments flex-auto"}>
            <label className={"w-full text-base"}>
              <Typography
                variant={"h6"}
                className={"mt-2 !mb-1 !p-0 text-base"}
              >
                {tr("Comments")}{" "}
                <span className={"text-gray-500 text-normal text-sm"}>
                  Raised beach house, lake front, ski-ski out, condo, townhouse,
                  single family, ground level, second choice of preferred
                  community, etc.
                </span>
              </Typography>
              <div className="flex gap-3">
                <textarea
                  required={true}
                  name={"comment"}
                  rows={4}
                  onInput={(event) => {
                    handleChange("comment", event.currentTarget.value);
                  }}
                  className={classNames("w-full text-base")}
                  value={buyerPreference.comment}
                ></textarea>
              </div>
            </label>
          </div>
        </div>
        <Typography variant={"body1"} className={"mt-2 !mb-1 !p-0 text-base"}>
          <span className={"font-semibold"}>{tr("Note:")}</span>{" "}
          <span className={"text-gray-500 "}>
            Second homes are typically sold furnished. You should anticipate
            that. Also, the divieight system calls for use of both a listing
            agent, a buyer’s agent and a cash offer to the seller. You should
            anticipate that as well. More details on the divieight system are
            explained on the divieight website and in the video tutorial library
          </span>
        </Typography>
      </div>
      <br />
      <LoadingButton
        variant="contained"
        loading={isLoadingSaveBuyerPreference}
        className=""
        type="submit"
      >
        {tr("Save")}
      </LoadingButton>
    </form>
  );

  function handleSaveBuyerPreference(e) {
    e.preventDefault();

    saveBuyerPreference(buyerPreference)
      .unwrap()
      .then((originalPromiseResult) => {
        toast.success(tr("Saved successfully"));
      })
      .catch((rejectedValueOrSerializedError) => {
        toast.error(tr("Error"));
      });
  }
}
