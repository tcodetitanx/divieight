import { Box, IconButton, Modal, Tab, TextField } from "@mui/material";
import React, { useState, useEffect } from "react";
import CloseIcon from "@mui/icons-material/Close";
import { tr } from "../../i18n/tr";
import TabLoyaltyPoints from "../../features/UserDashboard/Bookings/TabLoyaltyPoints";
import { LoadingButton, TabContext, TabList } from "@mui/lab";
import {
  restGetErrorMessage,
  useFetchCreateLoyaltyPointMutation,
  useFetchDeleteLoyaltyPointMutation,
} from "../../rtk/myapi";
import { toast } from "react-toastify";
import { getClientData } from "../../libs/client-data";

export default function UserTablePointService(
  props: UserTablePointServiceProps,
) {
  const [open, setOpen] = React.useState(false);
  const [userId, setUserId] = React.useState<number>(props.userId);
  const [currentTab, setCurrentTab] = React.useState<"points" | "add_point">(
    "points",
  );
  const [newLoyaltyPointAmount, setNewLoyaltyPointAmount] = useState<number>(1);
  const [newLoyaltyPointDescription, setNewLoyaltyPointDescription] =
    useState("");
  const [createLoyaltyPoint, { isLoading: isLoadingCreateloyaltyPoint }] =
    useFetchCreateLoyaltyPointMutation();
  const [deleteLoyaltyPoint, { isLoading: isLoadingDeleteLoyaltyPoint }] =
    useFetchDeleteLoyaltyPointMutation();

  //   React.useEffect(() => {
  //     console.log("userId", userId);
  //     setOpen(true);
  //     setUserId(props.userId);

  //     return () => {
  //       setOpen(false);
  //       setUserId(0);
  //     };
  //   }, [userId]);

  //   React.useEffect(() => {
  //     console.log("userId", userId);
  //     setOpen(true);
  //     setUserId(props.userId);
  //     return () => {
  //       setOpen(false);
  //       setUserId(0);
  //     };
  //   }, []);

  React.useEffect(() => {
    setOpen(true);
    // setUserId(props.userId);
    return () => {
      //   setOpen(false);
      //   setUserId(0);
    };
  }, [props.randomKey]);

  return render();

  function render() {
    return <div className="point-service">{renderModal()}</div>;
  }

  function renderModal() {
    return (
      <Modal
        open={open}
        // onClose={handleCloseModal}
        aria-labelledby="parent-modal-title"
        aria-describedby="parent-modal-description"
      >
        <Box sx={{ ...modalStyle, width: "60%" }}>
          <div className="before-calendar flex flex-col gap-4 max-h-[90vh]  relative">
            <h2 id="select-date-and-tme">{tr("User Loyalty Points")}</h2>
            {/* Icon button*/}
            <IconButton
              color={"secondary"}
              aria-label={"close modal"}
              onClick={handleCloseModal}
              className={
                "absolute top-2 right-2 cursor-pointer hover:color-black"
              }
            >
              <CloseIcon />
            </IconButton>
            <div className="modal-body-here overflow-y-auto">
              {renderTab()}
              {renderTabContents()}
            </div>
          </div>
        </Box>
      </Modal>
    );
  }

  function renderTab() {
    return (
      <>
        <TabContext value={currentTab}>
          <Box sx={{ borderBottom: 1, borderColor: "divider" }}>
            <TabList onChange={handleChange} aria-label="Change tab">
              <Tab label={tr("Loyalty Points")} value={"points"} />
              <Tab label={tr("Add Loyalty Points")} value={"add_point"} />
            </TabList>
          </Box>
        </TabContext>
      </>
    );
  }

  function renderTabContents() {
    return (
      <div className={"tab-context "}>
        {"points" === currentTab && <TabLoyaltyPoints />}
        {"add_point" === currentTab && renderAddLoyaltyPoint()}
      </div>
    );
  }

  function renderAddLoyaltyPoint() {
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

  function handleCloseModal() {
    setOpen(false);
  }

  function handleChange(
    event: React.SyntheticEvent,
    newValue: typeof currentTab,
  ) {
    setCurrentTab(newValue);
  }

  function handleCreateLoyaltyPoint(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    createLoyaltyPoint({
      bookingId: 0,
      amount: newLoyaltyPointAmount,
      description: newLoyaltyPointDescription,
      user_id: getClientData().userId,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Loyalty point saved"));
        setNewLoyaltyPointAmount(0);
        setNewLoyaltyPointDescription("");
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
}

interface UserTablePointServiceProps {
  userId: number;
  randomKey: string;
}

const modalStyle = {
  position: "absolute" as "absolute",
  top: "50%",
  left: "50%",
  transform: "translate(-50%, -50%)",
  width: 400,
  bgcolor: "background.paper",
  border: "2px solid #000",
  boxShadow: 24,
  pt: 2,
  px: 4,
  pb: 3,
};
