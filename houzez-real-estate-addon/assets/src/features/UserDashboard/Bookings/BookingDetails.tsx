import React, { useState, useEffect } from "react";
import {
  Booking,
  BookingService,
  BookingStatus,
  LoyaltyPoint,
} from "../../../my-types";
import {
  Accordion,
  AccordionDetails,
  AccordionSummary,
  Button,
  CircularProgress,
  IconButton,
  TextField,
  Typography,
} from "@mui/material";
import ExpandMoreIcon from "@mui/icons-material/ExpandMore";
import { tr } from "../../../i18n/tr";
import ClearIcon from "@mui/icons-material/Clear";
// @ts-ignore
import SignatureCanvas from "react-signature-canvas";
import { QRCodeSVG } from "qrcode.react";
import TimeHelper from "../../../libs/TimeHelper";
import { LoadingButton } from "@mui/lab";
import { toast } from "react-toastify";
import {
  restGetErrorMessage,
  useFetchAttachSignatureToBookingMutation,
  useFetchCreateLoyaltyPointMutation,
  useFetchCreateRemarkMutation,
  useFetchDeleteLoyaltyPointMutation,
  useFetchDeleteRemarkMutation,
  useFetchSaveBookingSettingsMutation,
} from "../../../rtk/myapi";
import NoItemsFound from "../../../components/NoItemsFound";
import { renderBookingStatus } from "./TabBookings";
import { getClientData } from "../../../libs/client-data";

export default function BookingDetails(props: BookingDetailsProps) {
  const inAdmin = getClientData().inAdmin;
  const [isAdmin, setIsAdmin] = useState(false);
  const [expanded, setExpanded] = React.useState<string | false>(false);
  const signatureRef = React.useRef<any>(null);
  const [savingSignature, setSavingSignature] = useState(false);
  const [newRemark, setNewRemark] = useState("");
  const [
    attachSignatureToBooking,
    { isLoading: isLoadingAttachSignatureToBooking },
  ] = useFetchAttachSignatureToBookingMutation();
  const [createRemark, { isLoading: isLoadingCreateRemark }] =
    useFetchCreateRemarkMutation();
  const [remarks, setRemarks] = useState(props.booking.remarks);
  const [loyaltyPoints, setLoyaltyPoints] = useState<LoyaltyPoint[]>(
    props.booking.loyalty_points,
  );
  const [newLoyaltyPointAmount, setNewLoyaltyPointAmount] = useState<number>(1);
  const [newLoyaltyPointDescription, setNewLoyaltyPointDescription] =
    useState("");
  const [createLoyaltyPoint, { isLoading: isLoadingCreateloyaltyPoint }] =
    useFetchCreateLoyaltyPointMutation();
  const [deleteLoyaltyPoint, { isLoading: isLoadingDeleteLoyaltyPoint }] =
    useFetchDeleteLoyaltyPointMutation();
  const [deletingLoyaltyPointIds, setDeletingLoyaltyPointIds] = useState<
    number[]
  >([]);
  const [deleteRemark, { isLoading: isLoadingDeleteRemark }] =
    useFetchDeleteRemarkMutation();
  // const [bookingStatus, setBookingStatus] = useState<BookingStatus>(
  //   props.booking.status,
  // );
  const [saveBookingSettings, { isLoading: isLoadingSaveBookingSettings }] =
    useFetchSaveBookingSettingsMutation();

  const handleChange =
    (panel: string) => (event: React.SyntheticEvent, isExpanded: boolean) => {
      setExpanded(isExpanded ? panel : false);
    };

  /** Set is admin */
  useEffect(() => {
    if (getClientData().isAdmin) {
      setIsAdmin(true);
    }
  }, []);

  return render();

  function render() {
    return (
      <div className="hre-booking-details">
        {renderHeading(props.booking)}
        {renderDetailsAccordion(props.booking)}
      </div>
    );
  }

  function renderServices(booking: Booking) {
    const style = {
      width: "100%",
      maxWidth: 360,
      bgcolor: "background.paper",
    };

    return (
      <>
        <table>
          <thead>
            <th>{tr("Service")}</th>
            <th>{tr("Date")}</th>
            <th>{tr("Time")}</th>
            <th>{tr("Duration")}</th>
          </thead>
          {booking.booking_services.map((bs) => {
            return (
              <tr>
                <td>{bs.service_name}</td>
                <td>{bs.date}</td>
                <td>{TimeHelper.convertNumberToTime(bs.start_time)}</td>
                <td>
                  {bs.service_timing_in_minutes} {tr("minutes")}
                </td>
              </tr>
            );
          })}
        </table>
      </>
    );
  }

  function renderSingleServiceItem(title: string, value: string) {
    return (
      <li className={"single-service flex gap-2"}>
        <Typography className={"item-title text-gray-400"}>
          {title}:{" "}
        </Typography>
        <Typography className={"item-value text-black font-medium"}>
          {value}
        </Typography>
      </li>
    );
  }

  function renderSignature(booking: Booking) {
    return (
      <>
        {renderSignatureNotSigned(booking)}
        {renderSignatureAlreadySigned(booking)}
      </>
    );
  }

  function renderSignatureAlreadySigned(booking: Booking) {
    if (
      booking.signature_url?.length === undefined ||
      booking.signature_url?.length < 1
    )
      return null;

    return (
      <div className={"signature "}>
        <Typography variant={"body1"} className={"text-gray-400"}>
          {tr("Thanks for signing.")}
        </Typography>
        {booking.signature_url && (
          <img
            src={booking.signature_url}
            className="m-auto block shadow p-4 border border-solid"
          />
        )}
      </div>
    );
  }

  function renderSignatureNotSigned(booking: Booking) {
    if (
      !(
        booking.signature_url?.length === undefined ||
        booking.signature_url?.length < 1
      )
    )
      return null;
    return (
      <div className={"signature-wrapper flex flex-col gap-3"}>
        <Typography variant={"body1"} className={"text-gray-400"}>
          {tr("Signature not signed. Sign below.")}
        </Typography>
        <span className={"m-auto shadow p-4 border border-solid"}>
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
          {/* Save button */}
          <LoadingButton
            variant={"contained"}
            loading={savingSignature}
            onClick={() => {
              handleUpdateSignature(booking);
            }}
          >
            {tr("Save")}
          </LoadingButton>
        </div>
      </div>
    );
  }

  function renderQrCode(booking: Booking) {
    return (
      <div className={"signature"}>
        <QRCodeSVG value={booking.qrcode_url} />
        <Typography variant={"body1"} className={"text-gray-400"}>
          {tr(
            "Show this QR code to the admin to check in. Only admins can use this QR code.",
          )}
        </Typography>
      </div>
    );
  }

  function renderRemark(booking: Booking) {
    return (
      <div className={"remarks max-h-[300px] overflow-y-auto"}>
        {remarks.map((remark) => {
          return (
            <ul>
              <li className={"flex flex-between justify-center"}>
                <div className={"flex-auto w-full"}>
                  <Typography
                    variant={"body1"}
                    gutterBottom
                    className={"block"}
                  >
                    {remark.content}
                  </Typography>
                  <Typography
                    variant={"caption"}
                    className={"block text-gray-400"}
                    gutterBottom
                  >
                    {remark.date}
                  </Typography>
                </div>
                <button
                  className="button-primary h-5 button"
                  onClick={(e) => {
                    e.preventDefault();
                    // @ts-ignore
                    if (!confirm(tr("Are you sure?"))) return;
                    // @ts-ignore
                    deleteRemark(remark.id)
                      .unwrap()
                      .then((res) => {
                        toast.success(tr("Remark deleted"));
                        setRemarks(remarks.filter((r) => r.id !== remark.id));
                      })
                      .catch((err) => {
                        toast.error(restGetErrorMessage(err));
                      });
                  }}
                  disabled={isLoadingDeleteRemark}
                >
                  {!isLoadingDeleteRemark && <>x</>}
                  {isLoadingDeleteRemark && <>...</>}
                </button>
              </li>
            </ul>
          );
        })}
      </div>
    );
  }

  function renderAddRemark(booking: Booking) {
    return (
      <form
        className={"add-remark my-3 block w-full pb-2 flex flex-col gap-3"}
        onSubmit={handleCreateRemark}
      >
        <TextField
          id="remark"
          label={tr("Remark")}
          multiline
          rows={2}
          defaultValue=""
          value={newRemark}
          onChange={handleRemarkInput}
        />
        <LoadingButton
          variant={"contained"}
          loading={isLoadingCreateRemark}
          type="submit"
        >
          {tr("Save")}
        </LoadingButton>
      </form>
    );
  }

  function renderLoyaltyPoint(booking: Booking) {
    // if (loyaltyPoints.length < 1)
    //   return <NoItemsFound text={"No loyalty points found"} />;
    return (
      <div className={"loyalty-points"}>
        {inAdmin && renderAddLoyaltyPoint(booking)}
        {loyaltyPoints.length > 0 ? (
          <>
            <table>
              <thead>
                <tr>
                  <th>{tr("Amount")}</th>
                  <th>{tr("Description")}</th>
                  <th>{tr("Date")}</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                {loyaltyPoints.map((point) => (
                  <tr key={point.id}>
                    <td>
                      <div className="text-base">
                        <span>{tr("Point")}:</span>{" "}
                        <span className="font-semibold">{point.amount}</span>{" "}
                      </div>
                      <div>
                        <span>{tr("Amounts To")}:</span> :{" "}
                        <Typography
                          variant={"h4"}
                          className={"inline-block font-semibold"}
                          dangerouslySetInnerHTML={{
                            __html: point.amount_html,
                          }}
                        ></Typography>
                      </div>
                    </td>
                    <td>
                      <Typography
                        variant={"caption"}
                        className={"text-gray-400"}
                      >
                        {point.description}
                      </Typography>
                    </td>
                    <td>
                      <Typography
                        variant={"caption"}
                        className={"text-gray-400"}
                      >
                        {point.date}
                      </Typography>
                    </td>
                    <td>
                      <IconButton
                        className="hover:text-black"
                        onClick={() => handleDeleteLoyaltyPoint(point.id)}
                      >
                        {renderDeleteLoyaltyPointIcon(point.id)}
                      </IconButton>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </>
        ) : (
          <NoItemsFound text={"No loyalty points found"} />
        )}
      </div>
    );
  }

  function renderDeleteLoyaltyPointIcon(loyaltyPointId: number) {
    const thisPointDeleting =
      deletingLoyaltyPointIds.find((id) => id === loyaltyPointId) !== undefined;
    const showDeleting = isLoadingDeleteLoyaltyPoint && thisPointDeleting;
    if (showDeleting) {
      return <CircularProgress size={20} />;
    }
    return <ClearIcon />;
  }

  function renderAddLoyaltyPoint(booking: Booking) {
    return (
      <div className={"signature pb-3"}>
        <form
          className="shadow p-2 rounded flex flex-col gap-3"
          onSubmit={handleCreateLoyaltyPoint}
        >
          <TextField
            id={"loyalty-point-amount"}
            label={tr("Loyalty point amount")}
            defaultValue=""
            type="number"
            value={newLoyaltyPointAmount}
            onChange={handleNewLoyaltyPointAmountChange}
            required={true}
          />
          <TextField
            id="loyalty-point-description"
            label={tr("Description")}
            value={newLoyaltyPointDescription}
            multiline
            rows={2}
            defaultValue=""
            onChange={handleNewLoyaltyPointDescriptionChange}
            required={true}
          />
          <LoadingButton
            variant={"contained"}
            loading={isLoadingCreateloyaltyPoint}
            type="submit"
          >
            {tr("Add Point")}
          </LoadingButton>
        </form>
      </div>
    );
  }

  function renderDetailsAccordion(booking: Booking) {
    return (
      <div>
        {inAdmin &&
          renderSingleAccordionItem(
            tr("Settings"),
            renderAccordionItemSettings(booking),
            booking,
          )}
        {renderSingleAccordionItem(
          tr("Services"),
          renderServices(booking),
          booking,
        )}
        {renderSingleAccordionItem(
          tr("Signature"),
          renderSignature(booking),
          booking,
        )}
        {renderSingleAccordionItem(
          tr("QR Code"),
          renderQrCode(booking),
          booking,
        )}
        {renderSingleAccordionItem(
          tr("Remarks"),
          <>
            {inAdmin && renderAddRemark(booking)}, {renderRemark(booking)}{" "}
          </>,
          booking,
        )}
        {renderSingleAccordionItem(
          tr("Loyalty Point"),
          renderLoyaltyPoint(booking),
          booking,
        )}
      </div>
    );
  }

  function renderSingleAccordionItem(
    title: string,
    content: JSX.Element,
    booking: Booking,
    secondary = "",
  ) {
    return (
      <Accordion>
        <AccordionSummary
          expandIcon={<ExpandMoreIcon />}
          aria-controls={title}
          id={"accordion-" + title}
        >
          <Typography>
            {title} {secondary && <span>{secondary}</span>}
          </Typography>
        </AccordionSummary>
        <AccordionDetails>{content}</AccordionDetails>
      </Accordion>
    );
  }

  function renderHeading(booking: Booking) {
    return (
      <div className="heading-details">
        <h1 className="flex gap-2 items-center">
          <span>{tr("Booking")}:</span>
          <span className={"text-gray-400"}>#{booking.id}</span>
          <span>{renderBookingStatus(booking.status)}</span>
        </h1>
      </div>
    );
  }

  function renderAccordionItemSettings(booking: Booking) {
    const values: BookingStatus[] = ["complete", "handled", "pending"];
    return (
      <form
        className="booking-settings flex flex-col gap-2"
        onSubmit={handleSaveBookingSettings}
      >
        <label className="block w-full">
          <Typography variant="h6" className="w-full block">
            {tr("Status")}
          </Typography>
          <select
            className="w-full block"
            onChange={(e) =>
              handleUpdateStatus(e.target.value as BookingStatus, booking)
            }
            required={true}
          >
            <option value={""}>{tr("Status")}</option>
            {values.map((value) => {
              return (
                <option
                  key={value}
                  value={value}
                  selected={booking.status === value}
                >
                  {tr(value)}
                </option>
              );
            })}
          </select>
        </label>
        <LoadingButton
          type="submit"
          variant="contained"
          loading={isLoadingSaveBookingSettings}
        >
          {tr("Save")}
        </LoadingButton>
      </form>
    );
  }

  function handleUpdateSignature(booking: Booking) {
    const signature = signatureRef.current.toDataURL();
    setSavingSignature(true);
    attachSignatureToBooking({
      bookingId: booking.id,
      signatureUrl: signature,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Signature saved"));
        setSavingSignature(false);
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
        setSavingSignature(false);
      });
  }

  function handleRemarkInput(e) {
    setNewRemark(e.currentTarget.value);
  }

  function handleCreateRemark(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();

    createRemark({
      bookingId: props.booking.id,
      content: newRemark,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Remark saved"));
        setRemarks([res, ...remarks]);
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }

  function handleNewLoyaltyPointAmountChange(e) {
    setNewLoyaltyPointAmount(e.currentTarget.value);
  }

  function handleNewLoyaltyPointDescriptionChange(e) {
    setNewLoyaltyPointDescription(e.currentTarget.value);
  }

  function handleCreateLoyaltyPoint(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    createLoyaltyPoint({
      bookingId: props.booking.id,
      amount: newLoyaltyPointAmount,
      description: newLoyaltyPointDescription,
      user_id: getClientData().userId,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Loyalty point saved"));
        setLoyaltyPoints([res, ...loyaltyPoints]);
        setNewLoyaltyPointAmount(0);
        setNewLoyaltyPointDescription("");
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }

  function handleDeleteLoyaltyPoint(loyaltyPointId: number) {
    // @ts-ignore
    if (!confirm(tr("Are you sure?"))) return;

    // Return if already deleting this id.
    if (deletingLoyaltyPointIds.includes(loyaltyPointId)) return;

    setDeletingLoyaltyPointIds(deletingLoyaltyPointIds.concat(loyaltyPointId));

    deleteLoyaltyPoint(loyaltyPointId)
      .unwrap()
      .then((res) => {
        toast.success(tr("Loyalty point deleted"));
        setLoyaltyPoints(
          loyaltyPoints.filter((lp) => lp.id !== loyaltyPointId),
        );
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      })
      .finally(() => {
        setDeletingLoyaltyPointIds(
          deletingLoyaltyPointIds.filter((id) => id !== loyaltyPointId),
        );
      });
  }

  function handleUpdateStatus(status: BookingStatus, booking: Booking) {
    props.update({
      ...booking,
      status,
    });
  }

  function handleSaveBookingSettings(e) {
    e.preventDefault();
    saveBookingSettings({
      id: props.booking.id,
      status: props.booking.status,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Booking settings saved"));
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }
}

export interface BookingDetailsProps {
  booking: Booking;
  update: (booking: Booking) => void;
}
