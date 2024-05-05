// @ts-ignore
import React from "react";
import { Badge, Button, ButtonGroup, CircularProgress } from "@mui/material";
import { Booking, BookingStatus } from "../../../my-types";
import BookingDetails from "./BookingDetails";
import ArrowBackOutlinedIcon from "@mui/icons-material/ArrowBackOutlined";
import classNames from "classnames";
import {
  useFetchBookingTotalsQuery,
  useFetchGetBookingsQuery,
} from "../../../rtk/myapi";
import MainWrapper from "../../Wrapper/MainWrapper";
import {
  useMyAppDispatch,
  useMyAppSelector,
} from "./../../../rtk/mystore-hook";
import { setBookingListStatus } from "../../../rtk/store-slices/userDashboardSlice";
import NoItemsFound from "../../../components/NoItemsFound";
import { tr } from "../../../i18n/tr";
import { getClientData } from "../../../libs/client-data";
import ReactPaginate from "react-paginate";

export default function TabBookings({}: BookingsProps) {
  const clientData = getClientData();
  // const [activeStatus, setStatus] = React.useState<BookingStatus | "all">(
  //   "all",
  // );
  const dispatch = useMyAppDispatch();
  const activeStatus = useMyAppSelector(
    (state) => state.userDashboard.bookingListStatus,
  );
  const [currentBooking, setCurrentBooking] = React.useState<Booking | null>(
    null,
  );
  const [bookings, setBookings] = React.useState<Booking[]>([]);
  const [totalBookings, setTotalBookings] = React.useState<number>(0);
  const [page, setPage] = React.useState(1);
  const [postsPerPage, setPostsPerPage] = React.useState(10);
  const {
    data: bookingsData,
    error: bookingsError,
    isLoading: bookingsIsLoading,
    isFetching: bookingsIsFetching,
    refetch: refetchBookings,
  } = useFetchGetBookingsQuery({
    status: activeStatus,
    posts_per_page: postsPerPage,
    page,
  });
  const {
    data: totalBookingsData,
    error: errorTotalBookings,
    isLoading: isLoadingBookingTotals,
    isFetching: isFetchingBookingTotals,
  } = useFetchBookingTotalsQuery();
  const [testBookings, setTestBookings] = React.useState<Booking[]>([
    {
      id: 753,
      created: "",
      status: "handled",
      qrcode_url: "https://example.com",
      booking_services: [
        {
          id: 1,
          service_name: "Pedicure",
          date: "2022-02,01",
          service_id: 1,
          stop_time: 0,
          start_time: 0,
          service_timing_in_minutes: 0,
        },
        {
          id: 5,
          service_name: "Nail Publishing",
          date: "2022-02,01",
          service_id: 1,
          stop_time: 0,
          start_time: 0,
          service_timing_in_minutes: 0,
        },
      ],
      remarks: [
        {
          id: 34,
          content: "Hey, we are happy you enjoyed our services",
          date: "2022-01-03",
          booking_id: 753,
        },
        {
          id: 34,
          content: "Hey, we are happy you enjoyed our services",
          date: "2022-01-03",
          booking_id: 753,
        },
      ],
      loyalty_points: [
        {
          id: 48,
          description: "Points for purchasing a product wort $34.80",
          date: "2022-02-01",
          amount_html: "<span>$34.80</span>",
          status: "available",
          amount: 34.8,
        },
        {
          id: 45,
          description: "Points for dong Facial Analysis on 2022-02-03",
          date: "2022-02-03",
          amount_html: "<span>$143.80</span>",
          status: "used",
          amount: 143.8,
        },
      ],
    },
  ]);

  // Set bookings when data is fetched
  React.useEffect(() => {
    if (bookingsData) {
      setBookings(bookingsData.bookings);
      setTotalBookings(bookingsData.found_posts);
    }
  }, [bookingsData]);

  // Refetch bookings when status changes
  React.useEffect(() => {
    refetchBookings();
  }, [activeStatus]);

  // Reset current booking when bookings change
  React.useEffect(() => {
    if (currentBooking) {
      const find = bookings.find((b) => b.id === currentBooking?.id);
      if (undefined !== find) {
        setCurrentBooking(find);
      }
    }
  }, [bookings]);

  return <>{render()}</>;

  function render() {
    return (
      <>
        {" "}
        {bookings && (
          <div className="hre-bookings pt-6">
            {renderInnerNavigation()}
            <MainWrapper
              isLoading={bookingsIsLoading || bookingsIsFetching}
              error={bookingsError}
              data={bookings}
            >
              {!currentBooking && renderBookingList(bookings)}
              {currentBooking && renderBookingDetails(currentBooking)}
            </MainWrapper>
          </div>
        )}
      </>
    );
  }

  function renderBookingDetails(bks: Booking) {
    return (
      <div className={"booking-details pt-4"}>
        <div
          className="back-button hover:bg-gray-100 p-2"
          onClick={handleBackButton}
        >
          <ArrowBackOutlinedIcon />
        </div>
        {currentBooking && (
          <BookingDetails
            booking={currentBooking}
            update={handleUpdateBooking}
          />
        )}
      </div>
    );
  }

  function renderBookingList(bks: Booking[]) {
    if (!bks.length) {
      return (
        <div>
          <NoItemsFound text={"No bookings found"} />
          <a
            href={clientData.clientSettings.makeBookingUrl}
            className="text-base my-2 w-full text-center block py-2"
          >
            {tr("Book a service")}
          </a>
        </div>
      );
      // return <NoItemsFound text={"No bookings found"} />;
    }
    return (
      <div className="hre-booking-list flex flex-col gap-4">
        {bks.map((booking) => renderBookingItem(booking))}
        {renderPagination()}
      </div>
    );
  }

  function renderPagination() {
    if (!totalBookingsData) return null;

    const status = activeStatus;
    const perPage = postsPerPage;
    const total = totalBookingsData[status];

    // Get total
    const pageCount = Math.ceil(total / perPage);
    return (
      <div className="react-paginate-table-wrapper">
        <ReactPaginate
          breakLabel="..."
          nextLabel={">"}
          onPageChange={(data) => {
            setPage(data.selected + 1);
          }}
          pageRangeDisplayed={5}
          pageCount={pageCount}
          previousLabel={"<"}
          renderOnZeroPageCount={null}
        />
      </div>
    );
  }

  function renderBookingItem(bks: Booking) {
    return (
      <div
        className={classNames(
          "hre-booking-item border border-r-0 border-l-0 border-t-0 border-b-1 border-solid border-gray-400 py-4 bg-white ",
        )}
      >
        <div className="booking-id-and-status flex justify-between items-center">
          <h3 className="booking-id text-xl font-bold text-gray-400 !p-0 !m-0">
            #{bks.id}
          </h3>
          <span className={"font-medium"}>
            {renderBookingStatus(bks.status)}
          </span>
        </div>
        <div className="services-count">
          <span className="text-gray-500">Services: </span>
          {bks.booking_services.length} <span>services</span>
        </div>
        <div className="date-and-time">
          <span className="text-gray-500">Created: </span>
          {bks.created}
        </div>
        <div className="action">
          <a
            href={"#"}
            onClick={(e) => {
              e.preventDefault();
              setCurrentBooking(bks);
            }}
            className={"!px-0 hover:text-black"}
          >
            {tr("view")}
          </a>
        </div>
      </div>
    );
  }

  function renderInnerNavigation() {
    return (
      <div className="hre-status-buttons">
        <ButtonGroup
          variant="text"
          aria-label="text button group"
          className={"flex gap-2"}
        >
          {renderOneButtonStatus(
            "all",
            activeStatus,
            tr("All"),
            totalBookingsData?.all ?? 0,
            handleChangeStatus,
            isLoadingBookingTotals || isFetchingBookingTotals,
            errorTotalBookings,
          )}
          {renderOneButtonStatus(
            "pending",
            activeStatus,
            tr("Pending"),
            totalBookingsData?.pending ?? 0,
            handleChangeStatus,
            isLoadingBookingTotals || isFetchingBookingTotals,
            errorTotalBookings,
          )}
          {renderOneButtonStatus(
            "handled",
            activeStatus,
            tr("Handled"),
            totalBookingsData?.handled ?? 0,
            handleChangeStatus,
            isLoadingBookingTotals || isFetchingBookingTotals,
            errorTotalBookings,
          )}
          {renderOneButtonStatus(
            "complete",
            activeStatus,
            tr("Complete"),
            totalBookingsData?.complete ?? 0,
            handleChangeStatus,
            isLoadingBookingTotals || isFetchingBookingTotals,
            errorTotalBookings,
          )}
        </ButtonGroup>
      </div>
    );
  }

  function handleChangeStatus(status: BookingStatus) {
    // setStatus(status);
    setPage(1);
    dispatch(setBookingListStatus(status));
    setCurrentBooking(null);
  }

  function handleUpdateBooking(booking: Booking) {
    const bookingIndex = bookings.findIndex((b) => b.id === booking.id);
    if (undefined !== bookingIndex) {
      setBookings(
        bookings.map((b) => {
          if (b.id === booking.id) {
            return booking;
          }
          return b;
        }),
      );
    }
  }

  function handleBackButton() {
    setCurrentBooking(null);
    refetchBookings();
  }
}

export interface BookingsProps {
  updateBooking: (booking: Booking) => void;
}

function renderOneButtonStatus(
  status: BookingStatus,
  activeStatus: BookingStatus,
  title: string,
  count: number,
  onClickFunc: (st: BookingStatus) => void,
  loading: boolean,
  error: any,
) {
  let content: any = 0;
  if (loading) {
    content = <CircularProgress size={10} />;
  } else if (error) {
    content = 0;
  } else {
    content = count;
  }

  return (
    <Badge
      badgeContent={content}
      color={status === activeStatus ? "primary" : "secondary"}
      invisible={false}
      showZero={true}
      onClick={() => onClickFunc(status)}
    >
      <Button
        className={"hover:text-black"}
        variant={"text"}
        color={status === activeStatus ? "primary" : "secondary"}
      >
        {title}
      </Button>
    </Badge>
  );
}

export function renderBookingStatus(status: BookingStatus) {
  const PENDING_STATUS_BG = "#f1efc7";
  const HANDLED_STATUS_BG = "#c7e9f1";
  const FINISHED_STATUS_BG = "#c8f1c7";
  const bg = {
    backgroundColor: PENDING_STATUS_BG,
  };
  if ("handled" === status) {
    bg.backgroundColor = HANDLED_STATUS_BG;
  } else if ("complete" === status) {
    bg.backgroundColor = FINISHED_STATUS_BG;
  }
  return (
    <div className="booking-status py-3">
      <div
        className="booking-status-icon capitalize text-base text-black rounded p-2"
        style={bg}
      >
        {status}
      </div>
    </div>
  );
}
