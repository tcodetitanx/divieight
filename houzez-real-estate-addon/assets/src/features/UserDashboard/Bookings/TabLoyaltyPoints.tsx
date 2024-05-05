import React, { useState, useEffect } from "react";
import { tr } from "../../../i18n/tr";
import {
  Booking,
  BookingStatus,
  LoyaltyPoint,
  LoyaltyPointStats,
  LoyaltyPointStatus,
} from "../../../my-types";
import {
  Badge,
  Button,
  ButtonGroup,
  Chip,
  CircularProgress,
  LinearProgress,
  Skeleton,
  Typography,
} from "@mui/material";
import TvIcon from "@mui/icons-material/Tv";
import AttachMoneyIcon from "@mui/icons-material/AttachMoney";
import LocalMoviesIcon from "@mui/icons-material/LocalMovies";
import {
  useFetchGetLoyaltyPointsQuery,
  useFetchLoyaltyPointStatsQuery,
  useFetchLoyaltyPointTotalsQuery,
} from "../../../rtk/myapi";
import NoItemsFound from "../../../components/NoItemsFound";
import MainWrapper from "../../Wrapper/MainWrapper";
import { getClientData } from "../../../libs/client-data";
import ReactPaginate from "react-paginate";

export default function TabLoyaltyPoints() {
  const [testLoyaltyPoints, setTestLoyaltyPoints] = useState<LoyaltyPoint[]>([
    {
      id: 32,
      status: "available",
      date: "2022-01-02",
      description: "For purchasing a product wort $23.42",
      amount_html: "<span>$23.44</span>",
      amount: 0,
    },
    {
      id: 12,
      status: "used",
      date: "2022-02-01",
      description: "For placing an order",
      amount_html: "<span>$22.24</span>",
      amount: 0,
    },
  ]);
  const [loyaltyPoints, setLoyaltyPoints] = useState<LoyaltyPoint[]>([]);
  const [page, setPage] = useState<number>(1);
  const [perPage, setPerPage] = useState<number>(10);
  const [activeStatus, setActiveStatus] = useState<LoyaltyPointStatus | "all">(
    "all",
  );

  const {
    data: loyaltyPointsData,
    isLoading: isLoyaltyPointsLoading,
    isFetching: isFetchingLoyaltyPoints,
    isError: isErrorPoints,
    refetch: refetchLoyaltyPoints,
  } = useFetchGetLoyaltyPointsQuery({
    page,
    posts_per_page: perPage,
    status: activeStatus,
    user_id: getClientData().userId,
  });
  const {
    data: totalLoyaltyPointData,
    isLoading: isLoadingLoyaltyPointTotals,
    isError: isErrorLoyaltyPointTotals,
    refetch: refetchLoyaltyPointTotals,
    isFetching: isFetchingLoyaltyPointTotals,
  } = useFetchLoyaltyPointTotalsQuery({
    user_id: getClientData().userId,
  });
  const [loyaltyPointStats, setLoyaltyPointStats] = useState<LoyaltyPointStats>(
    {
      total: 340,
      total_html: "<span>$340</span>",
      used: 300,
      used_html: "<span>$300</span>",
      available: 40,
      available_html: "<span>$40.00</span>",
    },
  );
  const fetchStats = useFetchLoyaltyPointStatsQuery({
    user_id: getClientData().userId,
  });

  // Set loyalty points
  useEffect(() => {
    if (loyaltyPointsData) {
      setLoyaltyPoints(loyaltyPointsData.loyalty_points);
    }
  }, [loyaltyPointsData]);

  // Refetch loyalty points when page, perPage or status changes.
  useEffect(() => {
    refetchLoyaltyPoints();
  }, [page, perPage, status]);

  return render();

  function render() {
    return (
      <div className="hre-loyalty-point-tab">
        {renderStats()}
        {renderInnerNavigation()}
        {renderTable(loyaltyPoints)}
        {renderPagination()}
      </div>
    );
  }

  function renderTable(stats: LoyaltyPoint[]) {
    if (isLoyaltyPointsLoading) return <LinearProgress />;
    return (
      <MainWrapper
        isLoading={isLoyaltyPointsLoading}
        data={loyaltyPoints}
        error={isErrorPoints}
      >
        <div className={"loyalty-points"}>
          {loyaltyPoints.length > 0 ? (
            <>
              {isFetchingLoyaltyPoints && <LinearProgress />}
              <table>
                <thead>
                  <tr>
                    <th className="text-left">{tr("Point")}</th>
                    <th className="text-left">{tr("Message")}</th>
                    <th className="text-left">{tr("Status")}</th>
                  </tr>
                </thead>
                <tbody>
                  {stats.map((lp) => (
                    <tr>
                      <td>
                        <Typography
                          variant={"h3"}
                          dangerouslySetInnerHTML={{
                            __html:
                              lp.amount +
                              " " +
                              lp.amount_html +
                              `<span class='block text-sm text-gray-400'>${lp.date}</span>`,
                          }}
                          className="text-xl "
                        ></Typography>
                      </td>
                      <td>
                        <Typography
                          variant={"body2"}
                          dangerouslySetInnerHTML={{ __html: lp.description }}
                        ></Typography>
                      </td>
                      <td>{renderLoyaltyPointStatus(lp)}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </>
          ) : (
            <NoItemsFound text={"No loyalty points found"} />
          )}
        </div>
      </MainWrapper>
    );
  }

  function renderStats() {
    if (fetchStats.isLoading)
      return (
        <>
          <Skeleton width={"300px"} height={20} />
        </>
      );

    if (!fetchStats.data) return null;
    const lp = fetchStats.data;
    return (
      <div className={"stats-wrapper py-3 flex gap-2 justify-between"}>
        {renderSingleStat(
          lp.total_html,
          lp.total,
          <TvIcon />,
          tr("Total Points"),
        )}
        {renderSingleStat(
          lp.available_html,
          lp.available,
          <LocalMoviesIcon />,
          tr("Available Points"),
        )}
        {renderSingleStat(
          lp.used_html,
          lp.used,
          <AttachMoneyIcon />,
          tr("Used Points"),
        )}
      </div>
    );
  }
  function renderPagination() {
    if (!totalLoyaltyPointData) return null;

    const status = activeStatus;
    const total = totalLoyaltyPointData[status];

    // Get total
    const pageCount = Math.ceil(total / perPage);
    return (
      <div className="react-paginate-table-wrapper py-4">
        {isFetchingLoyaltyPoints && <LinearProgress className="" />}
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

  function renderSingleStat(
    html: string,
    amount: number,
    icon: any,
    description: string,
  ) {
    return (
      <div className={"one-stat py-2 px-4 rounded-md shadow flex-auto"}>
        <div className="amount-and-logo flex justify-between">
          <div
            className="amount text-xl font-medium"
            dangerouslySetInnerHTML={{ __html: html }}
          ></div>
          <div className="logo text-gray-400">{icon}</div>
        </div>
        <div className="description text-gray-400">
          {description} <span className="font-semibold">[{amount} points]</span>
        </div>
      </div>
    );
  }

  function renderLoyaltyPointStatus(loyaltyPoint: LoyaltyPoint) {
    const AVAILABLE_COLOR = "bg-[#dcf6db]";
    const USED_COLOR = "bg-[#e7e7e7]";
    let css = AVAILABLE_COLOR;
    if (loyaltyPoint.status === "used") {
      css = USED_COLOR;
    }
    return <Chip label={loyaltyPoint.status} className={css} />;
  }

  function renderInnerNavigation() {
    return (
      <div className="hre-status-buttons py-4">
        <ButtonGroup
          variant="text"
          aria-label="text button group"
          className={"flex gap-2"}
        >
          {renderOneButtonStatus("all", "All", totalLoyaltyPointData?.all ?? 0)}
          {renderOneButtonStatus(
            "available",
            "Available",
            totalLoyaltyPointData?.available ?? 0,
          )}
          {renderOneButtonStatus(
            "used",
            "Used",
            totalLoyaltyPointData?.used ?? 0,
          )}
        </ButtonGroup>
      </div>
    );
  }

  function renderOneButtonStatus(
    theStatus: LoyaltyPointStatus,
    title: string,
    count: number,
  ) {
    let content: any = 0;
    if (isLoadingLoyaltyPointTotals || isFetchingLoyaltyPointTotals) {
      content = <CircularProgress size={10} />;
    } else if (isErrorLoyaltyPointTotals) {
      content = 0;
    } else {
      content = count;
    }

    return (
      <Badge
        badgeContent={content}
        color={theStatus === activeStatus ? "primary" : "secondary"}
        invisible={false}
        showZero={true}
        onClick={() => handleChangeStatus(theStatus)}
      >
        <Button
          className={"hover:text-black"}
          variant={"text"}
          color={theStatus === activeStatus ? "primary" : "secondary"}
        >
          {title}
        </Button>
      </Badge>
    );
  }

  function handleChangeStatus(status: LoyaltyPointStatus) {
    setPage(1);
    setActiveStatus(status);
    refetchLoyaltyPointTotals();
  }
}

export interface TabLoyaltyPointsProps {
  loyaltyPoints: LoyaltyPoint[];
}
