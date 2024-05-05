import * as React from "react";
import { Inquiry, InquiryData } from "../../my-types";
import { Typography } from "@mui/material";
import { tr } from "../../i18n/tr";
import * as cn from "classnames";
import LoadingButton from "@mui/lab/LoadingButton";
import {
  restGetErrorMessage,
  useFetchCreateInquiryMutation,
} from "../../rtk/myapi";
import { toast } from "react-toastify";

const emptyInquiryData: InquiryData = getEmptyInquiryData();
export default function InquiryForm() {
  const [form, setForm] = React.useState<InquiryData>(emptyInquiryData);
  const [createInquiry, { isLoading: isLoadingCreateInquiry }] =
    useFetchCreateInquiryMutation();

  return render();

  function render() {
    return (
      <div>
        <form
          onSubmit={(e) =>
            handleSubmitInquiryForm(e, createInquiry(form).unwrap()).then((d) =>
              setForm(d.newForm),
            )
          }
          className={cn(
            "inquiry-form max-w-[700px] m-auto p-2 lg:p-8 rounded ",
            "flex flex-col gap-8 mt-2 mb-4",
          )}
        >
          <Typography variant={"h2"} className={" !text-hre-text-black"}>
            {tr("Inquiry Form")}
          </Typography>
          <div className="mp-slide-in">{renderSectionInquiryType()}</div>
          <div className="mp-slide-in">{renderSectionInformation()} </div>
          <div className="mp-slide-in">{renderSectionLocation()} </div>
          <div className="mp-slide-in"> {renderSectionInProperty()}</div>
          <div className="mp-slide-in"> {renderSectionInMessage()}</div>
          <div className="mp-slide-in">{renderSectionInAgreement()}</div>
          <LoadingButton
            loading={isLoadingCreateInquiry}
            variant="contained"
            color="primary"
            type="submit"
            className={cn("submit-btn")}
          >
            {tr("SUBMIT")}
          </LoadingButton>
        </form>
      </div>
    );
  }

  function renderSectionInquiryType() {
    return (
      <div className={"section-inquiry-type flex flex-col"}>
        <label className={"flex flex-col gap-1"}>
          <Typography
            variant="h6"
            className={"text-base font-semibold !m-0 !p-0 !text-hre-text-black"}
          >
            {tr("Inquiry Type")}
          </Typography>
          <select
            required
            name="inquiry_type"
            value={form.inquiry_type}
            className={cn(
              "first-name h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
            )}
            onChange={(e) => {
              setForm({ ...form, inquiry_type: e.target.value });
            }}
          >
            <option value="">{tr("Select")}</option>
            <option value="Info on Exclusivity">
              {tr("Info on Exclusivity")}
            </option>
          </select>
        </label>
      </div>
    );
  }

  function renderSectionInformation() {
    return (
      <div className={"section-information flex flex-col gap-3"}>
        <Typography
          variant="h6"
          className={"text-base font-semibold !m-0 !p-0  !text-hre-text-black"}
        >
          {tr("Information")}
        </Typography>
        <label className={"flex flex-col gap-1"}>
          <select
            name="information.i_am"
            required
            value={form.information.i_am}
            className={cn(
              "first-name h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
            )}
            onChange={(e) => {
              setForm({
                ...form,
                information: { ...form.information, i_am: e.target.value },
              });
            }}
          >
            <option value="">{tr("I am")}</option>
            <option value="I am a real estate agent">
              {tr("I am a real estate agent.")}
            </option>
            <option value="I am a propert owner">
              {tr("I am a property owner.")}
            </option>
          </select>
        </label>
        {/*Firstname and Lastname*/}
        <div className="first-name-and-last-name  md:flex  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              required
              type="text"
              placeholder={tr("First Name")}
              name="first_name"
              value={form.information.first_name}
              className={cn(
                "first-name h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  information: {
                    ...form.information,
                    first_name: e.target.value,
                  },
                });
              }}
            />
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              type={"text"}
              required
              name="last_name"
              placeholder={tr("Last Name")}
              value={form.information.last_name}
              className={cn(
                "first-name h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  information: {
                    ...form.information,
                    last_name: e.target.value,
                  },
                });
              }}
            />
          </label>
        </div>
        {/*Email and Phone*/}
        <div className="first-name-and-last-name  md:flex  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              required
              placeholder={tr("Email Address")}
              name="email"
              value={form.information.email}
              type="email"
              className={cn(
                "email h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  information: {
                    ...form.information,
                    email: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              required
              name="phone"
              placeholder={tr("Mobile")}
              value={form.information.phone}
              type={"tel"}
              className={cn(
                "first-name h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  information: {
                    ...form.information,
                    phone: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
        </div>
      </div>
    );
  }

  function renderSectionLocation() {
    return (
      <div className={"section-information flex flex-col gap-3"}>
        <Typography
          variant="h6"
          className={"text-base font-semibold !m-0 !p-0  !text-hre-text-black"}
        >
          {tr("Location")}
        </Typography>
        {/*Location*/}
        <div className="location flex flex-col lg:flex-row gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <select
              name="location.city"
              value={form.location.city}
              className={cn(
                "city h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  location: { ...form.location, city: e.target.value },
                });
              }}
            >
              <option value="">{tr("Select City")}</option>
              {getCities().map((city) => (
                <option value={city}>{city}</option>
              ))}
            </select>
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <select
              name="location.area"
              value={form.location.area}
              className={cn(
                "area h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  location: { ...form.location, area: e.target.value },
                });
              }}
            >
              <option value="">{tr("Select Area")}</option>
              {getAreaOptions().map((item) => (
                <option value={item}>{item}</option>
              ))}
            </select>
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <select
              name="location.state"
              required
              value={form.location.state}
              className={cn(
                "state h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  location: { ...form.location, state: e.target.value },
                });
              }}
            >
              <option value="">{tr("Select State")}</option>
              {getUsStates().map((item) => (
                <option value={item}>{item}</option>
              ))}
            </select>
          </label>
        </div>
        {/*Country and Zip Code*/}
        <div className="location flex flex-col lg:flex-row  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <select
              name="location.country"
              required
              value={form.location.country}
              className={cn(
                "country !h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  location: { ...form.location, country: e.target.value },
                });
              }}
            >
              <option value="">{tr("Select Country")}</option>
              {["United States of America"].map((item) => (
                <option value={item}>{item}</option>
              ))}
            </select>
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              required
              name="zip_code"
              placeholder={tr("Zip Code")}
              value={form.location.zip_code}
              type={"number"}
              className={cn(
                "zip-code h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onChange={(e) => {
                setForm({
                  ...form,
                  location: { ...form.location, zip_code: e.target.value },
                });
              }}
            />
          </label>
        </div>
      </div>
    );
  }

  function renderSectionInProperty() {
    return (
      <div className={"section-property flex flex-col gap-3"}>
        <Typography
          variant="h6"
          className={"text-base font-semibold !m-0 !p-0  !text-hre-text-black"}
        >
          {tr("Property")}
        </Typography>
        <label className={"flex flex-col gap-1"}>
          <select
            name="property.type"
            required
            value={form.property.type}
            className={cn(
              "property-type  h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
            )}
            onChange={(e) => {
              setForm({
                ...form,
                property: { ...form.property, type: e.target.value },
              });
            }}
          >
            <option value="">{tr("Select Type")}</option>
            {getPropertyTypeOptions().map((item) => (
              <option value={item}>{item}</option>
            ))}
          </select>
        </label>
        {/*Max Price & Min Size*/}
        <div className="md:flex  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              type="number"
              placeholder={tr("Max Price")}
              name="max_price"
              value={form.property.max_price}
              className={cn(
                "max-price  h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  property: {
                    ...form.property,
                    max_price: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              type={"number"}
              name="min_size"
              placeholder={tr("Minimum size (Sq Ft)")}
              value={form.property.min_size}
              className={cn(
                "min-size h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  property: {
                    ...form.property,
                    min_size: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
        </div>
        {/*Number of Bathrooms & Number of Beds*/}
        <div className=" md:flex  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              placeholder={tr("Number of beds")}
              name="number_of_bed"
              value={form.property.number_of_bedrooms}
              type="number"
              className={cn(
                "number-of-beds h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  property: {
                    ...form.property,
                    number_of_bedrooms: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
          <label className={"flex flex-col gap-1 flex-auto"}>
            <input
              name="bathroom_number"
              placeholder={tr("Number of baths")}
              value={form.property.number_of_bathrooms}
              type={"number"}
              className={cn(
                "no-of-baths h-[50px] px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  property: {
                    ...form.property,
                    number_of_bathrooms: e.currentTarget.value,
                  },
                });
              }}
            />
          </label>
        </div>
      </div>
    );
  }

  function renderSectionInMessage() {
    return (
      <div className={"section-message flex flex-col gap-3"}>
        <Typography
          variant="h6"
          className={"text-base font-semibold !m-0 !p-0  !text-hre-text-black"}
        >
          {tr("Message")}
        </Typography>
        {/*Message*/}
        <div className="md:flex  gap-2">
          <label className={"flex flex-col gap-1 flex-auto"}>
            <textarea
              rows={4}
              placeholder={tr("Message")}
              name="message"
              value={form.message}
              className={cn(
                "message  px-2 py-2 rounded bg-white border-2 border-solid border-gray-300",
              )}
              onInput={(e) => {
                setForm({
                  ...form,
                  message: e.currentTarget.value,
                });
              }}
            >
              {form.message}
            </textarea>
          </label>
        </div>
      </div>
    );
  }

  function renderSectionInAgreement() {
    return (
      <div className={"section-agreement flex flex-col gap-1"}>
        <Typography
          variant="h6"
          className={"text-base font-semibold !m-0 !p-0  !text-hre-text-black"}
        >
          {tr("GDPR Agreement")}
        </Typography>
        <div className="flex  gap-2">
          <label
            className={
              "flex-auto flex  gap-2 items-center hover:font-medium cursor-pointer"
            }
          >
            <input
              type={"checkbox"}
              required
              name="agree"
              value={form.agreed_to_terms}
              checked={form.agreed_to_terms === "yes"}
              className={cn("agree  scale-[1.2]")}
              onChange={(e) => {
                setForm({
                  ...form,
                  agreed_to_terms: e.target.checked ? "yes" : "no",
                });
              }}
            />
            <Typography
              variant={"body1"}
              className={
                " !m-0 !p-0 cursor-pointer hover:font-medium  !text-hre-text-black"
              }
            >
              {tr(
                " I consent to having this website store my submitted information.",
              )}
            </Typography>
          </label>
        </div>
      </div>
    );
  }
}

function getCities() {
  return [
    "Chicago",
    "Faisalabad",
    "Los Angeles",
    "Miami",
    "New York",
    "Winfield",
  ];
}

function getAreaOptions(): string[] {
  return [
    "Albany Park",
    "Altgeld Gardens",
    "Andersonville",
    "Beverly",
    "Brickel",
    "Brooklyn",
    "Brookside",
    "Central City",
    "Coconut Grove",
    "Hyde Park",
    "Manhattan",
    "Midtown",
    "Northeast Los Angeles",
    "University Roadmap",
    "West Flagger",
    "Wynwood",
  ];
}

export function getUsStates(): string[] {
  return [
    "Alabama",
    "Alaska",
    "Arizona",
    "Arkansas",
    "California",
    "Colorado",
    "Connecticut",
    "Delaware",
    "Florida",
    "Georgia",
    "Hawaii",
    "Idaho",
    "Illinois",
    "Indiana",
    "Iowa",
    "Kansas",
    "Kentucky",
    "Louisiana",
    "Maine",
    "Maryland",
    "Massachusetts",
    "Michigan",
    "Minnesota",
    "Mississippi",
    "Missouri",
    "Montana",
    "Nebraska",
    "Nevada",
    "New Hampshire",
    "New Jersey",
    "New Mexico",
    "New York",
    "North Carolina",
    "North Dakota",
    "Ohio",
    "Oklahoma",
    "Oregon",
    "Pennsylvania",
    "Rhode Island",
    "South Carolina",
    "South Dakota",
    "Tennessee",
    "Texas",
    "Utah",
    "Vermont",
    "Virginia",
    "Washington",
    "West Virginia",
    "Wisconsin",
    "Wyoming",
  ];
}

function getPropertyTypeOptions(): string[] {
  return ["Condo", "Single Family", "Townhouse"];
}

function handleSubmitInquiryForm(
  e: any,
  request: Promise<Inquiry>,
): Promise<{ newForm: InquiryData }> {
  e.preventDefault();
  // console.log({ form });
  return new Promise((resolve, reject) => {
    request
      .then((data) => {
        toast.success("Inquiry submitted successfully");
        resolve({ newForm: getEmptyInquiryData() });
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
        reject(err);
      });
  });
}

function getEmptyInquiryData(): InquiryData {
  return {
    inquiry_type: "",
    information: {
      i_am: "",
      first_name: "",
      last_name: "",
      email: "",
      phone: "",
    },
    location: {
      country: "",
      state: "",
      city: "",
      area: "",
      zip_code: "",
    },
    property: {
      type: "",
      max_price: "",
      min_size: "",
      number_of_bedrooms: "",
      number_of_bathrooms: "",
    },
    message: "",
    agreed_to_terms: "no",
  };
}
