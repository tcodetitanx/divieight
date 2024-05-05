import React from "react";
import { useFetchGetSingleBookingQuery } from "../../rtk/myapi";
import { LinearProgress } from "@mui/material";
import BookingDetails from "../../features/UserDashboard/Bookings/BookingDetails";
import NoItemsFound from "../../components/NoItemsFound";
import { tr } from "../../i18n/tr";
import { Booking } from "../../my-types";

export default function MetaBoxBooking({ bookingId }: MetaBoxBookingProps) {
  const [booking, setBooking] = React.useState<Booking>(null);
  const {
    data: bookingData,
    isLoading: isLoadingBooking,
    isError: isErrorBooking,
  } = useFetchGetSingleBookingQuery(bookingId);

  React.useEffect(() => {
    if (bookingData) {
      setBooking(bookingData);
    }
  }, [bookingData]);

  return render();

  function render() {
    if (isLoadingBooking) {
      return <LinearProgress />;
    }
    if (isErrorBooking) {
      return <NoItemsFound text={tr("No booking found")} />;
    }

    if (!bookingData) {
      return null;
    }
    return booking ? (
      <BookingDetails booking={booking} update={handleUpdateBooking} />
    ) : null;
  }

  function handleUpdateBooking(bkk: Booking) {
    setBooking(bkk);
  }
}

export interface MetaBoxBookingProps {
  bookingId: number;
}
