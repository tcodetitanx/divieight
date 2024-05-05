import * as React from "react";
import Box from "@mui/material/Box";
import Typography from "@mui/material/Typography";
import Modal from "@mui/material/Modal";
import { useEffect } from "react";
import { Inquiry, InquiryData } from "../../my-types";
import { LinearProgress } from "@mui/material";
import { tr } from "../../i18n/tr";

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
export default function AdminViewInquiry() {
  const [open, setOpen] = React.useState(false);
  const [inquiry, setInquiry] = React.useState<null | Inquiry>(null);
  const [inquiryData, setInquiryData] = React.useState<null | InquiryData>(
    null,
  );

  const handleOpen = () => setOpen(true);
  const handleClose = () => setOpen(false);

  useEffect(() => {
    console.log("useEffect");

    jQuery("body").on(
      "click",
      "button.hre-btn-view-inquiry",
      (event: any, data: any) => {
        if (open) return;
        console.log("clicked", { event, data });
        const elemButton = jQuery(event.target);
        const withHmtlEntity = elemButton.attr("data-inquiry-data");
        const withoutHtmlEntity = he.decode(withHmtlEntity);
        const json: Inquiry = JSON.parse(withoutHtmlEntity);

        console.log("open haneled", {
          elemButton,
          withHmtlEntity,
          withoutHtmlEntity,
          json,
        });

        setInquiry(json);
        setInquiryData(json.inquiry_data);
        handleOpen();
      },
    );

    return () => {
      console.log("useEffect return");
      setOpen(false);
      jQuery("body").off(".hre-btn-view-inquiry");
    };
  }, []);

  return render();

  function render() {
    if ([null, undefined].includes(inquiryData)) return <LinearProgress />;
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
              {tr("Inquiry Details")}
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
    return <div className="modal-body">{renderInquiry()}</div>;
  }

  function renderInquiry() {
    return (
      <div className="mx-auto p-8 bg-gray-100 rounded-lg shadow-lg max-h-[71vh] overflow-y-auto">
        {/* Inquiry Type Section */}
        <section className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Inquiry Type</h2>
          <p>{inquiryData.inquiry_type}</p>
        </section>

        {/* User Information Section */}
        <section className="mb-6">
          <h2 className="text-xl font-semibold mb-2">User Information</h2>
          <p>
            <strong>I am:</strong> {inquiryData.information.i_am}
          </p>
          <p>
            <strong>First Name:</strong> {inquiryData.information.first_name}
          </p>
          <p>
            <strong>Last Name:</strong> {inquiryData.information.last_name}
          </p>
          <p>
            <strong>Email:</strong> {inquiryData.information.email}
          </p>
        </section>

        {/* Location Section */}
        <section className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Location</h2>
          <p>
            <strong>Country:</strong> {inquiryData.location.country}
          </p>
          <p>
            <strong>State:</strong> {inquiryData.location.state}
          </p>
          <p>
            <strong>City:</strong> {inquiryData.location.city}
          </p>
          <p>
            <strong>Area:</strong> {inquiryData.location.area}
          </p>
          <p>
            <strong>Zip Code:</strong> {inquiryData.location.zip_code}
          </p>
        </section>

        {/* Property Details Section */}
        <section className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Property Details</h2>
          <p>
            <strong>Type:</strong> {inquiryData.property.type}
          </p>
          <p>
            <strong>Max Price:</strong> ${inquiryData.property.max_price}
          </p>
          <p>
            <strong>Min Size:</strong> {inquiryData.property.min_size} sq ft
          </p>
          <p>
            <strong>Number of Bedrooms:</strong>{" "}
            {inquiryData.property.number_of_bedrooms}
          </p>
          <p>
            <strong>Number of Bathrooms:</strong>{" "}
            {inquiryData.property.number_of_bathrooms}
          </p>
        </section>

        {/* Additional Information Section */}
        <section className="mb-6">
          <h2 className="text-xl font-semibold mb-2">Additional Information</h2>
          <p>
            <strong>Agreed to Terms:</strong> {inquiryData.agreed_to_terms}
          </p>
        </section>
      </div>
    );
  }
}
