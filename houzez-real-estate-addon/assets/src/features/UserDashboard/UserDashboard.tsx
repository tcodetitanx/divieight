import React from "react";
import Tab from "@mui/material/Tab";
import Box from "@mui/material/Box";
import { TabContext, TabList } from "@mui/lab";
import TabLoyaltyPoints from "./Bookings/TabLoyaltyPoints";
import TabFacialAnalysis from "./Bookings/TabFacialAnalysis";
import TabBookings from "./Bookings/TabBookings";
import { useFetchBookingTotalsQuery } from "../../rtk/myapi";
import { getClientData } from "../../libs/client-data";
import SignupLogin from "../auth/SignupLogin/SignupLogin";
import { Booking } from "../../my-types";
import { tr } from "./../../i18n/tr";

export default function UserDashboard({}: UserDashboardProps) {
  const TAB_NAMES = {
    BOOKINGS: "bookings",
    LOYALTY_POINTS: "loyalty_points",
    FACIAL_ANALYSIS: "facial_analysis",
  };
  const [currentTab, setCurrentTab] = React.useState(TAB_NAMES.BOOKINGS);
  const {
    data: bookingTotalsData,
    error: bookingTotalsError,
    isLoading: bookingTotalsIsLoading,
  } = useFetchBookingTotalsQuery();

  return (
    <div className="hre-user-dashboard-container pb-4 max-w-[800px] m-auto">
      {render()}
    </div>
  );

  function render() {
    return (
      <>
        {renderTab()}

        {getClientData().isLoggedIn && renderTabContents()}
        {!getClientData().isLoggedIn && <SignupLogin />}
      </>
    );
  }

  function renderTab() {
    return (
      <>
        <TabContext value={currentTab}>
          <Box sx={{ borderBottom: 1, borderColor: "divider" }}>
            <TabList onChange={handleChange} aria-label="lab API tabs example">
              <Tab label={tr("Bookings")} value={TAB_NAMES.BOOKINGS} />
              <Tab
                label={tr("Loyalty Points")}
                value={TAB_NAMES.LOYALTY_POINTS}
              />
              <Tab
                label={tr("Facial Analysis")}
                value={TAB_NAMES.FACIAL_ANALYSIS}
              />
            </TabList>
          </Box>
        </TabContext>
      </>
    );
  }

  function renderTabContents() {
    return (
      <div className={"tab-context "}>
        {TAB_NAMES.BOOKINGS === currentTab && (
          <TabBookings
            updateBooking={function (booking: Booking): void {
              // throw new Error("Function not implemented.");
            }}
          />
        )}
        {TAB_NAMES.LOYALTY_POINTS === currentTab && <TabLoyaltyPoints />}
        {TAB_NAMES.FACIAL_ANALYSIS === currentTab && <TabFacialAnalysis />}
      </div>
    );
  }

  function handleChange(event: React.SyntheticEvent, newValue: string) {
    setCurrentTab(newValue);
  }
}

export interface UserDashboardProps {}
