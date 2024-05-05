import React, { useEffect, useState } from "react";
import { toast } from "react-toastify";
import {
  useFetchCalendarQuery,
  useFetchCreateAvailableDayMutation,
  useFetchGetAvailableDayQuery,
  useFetchGetServicesQuery,
} from "../../rtk/myapi";
import MainWrapper from "../../features/Wrapper/MainWrapper";
import CalendarView from "../../features/calendar/CalendarView";
import {
  AvailableDay,
  CalendarNamespace,
  TimeRange,
  WpService,
} from "../../my-types";
import CalendarDay = CalendarNamespace.CalendarDay;
import ServicesView from "../../features/calendar/ServicesView";
import TimeRangeView from "../../features/calendar/TimerangeView";
import { LoadingButton } from "@mui/lab";
import { SaveOutlined as SaveOutlinedIcon } from "@mui/icons-material";
import { tr } from "../../i18n/tr";
export default function AdminPageAvailability() {
  const today = new Date();

  // States.
  const [selectedCalendarDay, setSelectedCalendarDay] =
    React.useState<CalendarDay>();
  const [initialDayDetails, setInitialDayDetails] = React.useState({
    day: today.getDate(),
    month: today.getMonth() + 1,
    year: today.getFullYear(),
    noOfYears: 3,
  });
  const [availableDay, setAvailableDay] = React.useState<AvailableDay>();
  const [allServicesAvailable, setAllServicesAvailable] = useState(false);
  // API.
  const {
    data: calendar,
    error: errorCalendar,
    isLoading: isLoadingCalendar,
    isFetching: isFetchingCalendar,
    refetch: refetchCalendar,
  } = useFetchCalendarQuery({
    year: initialDayDetails.year,
    month: initialDayDetails.month,
    noOfYears: initialDayDetails.noOfYears,
  });
  const {
    data: services,
    isLoading: isLoadingServices,
    error: errorServices,
  } = useFetchGetServicesQuery();
  const {
    data: dataAvailableDay,
    isLoading: isLoadingAvailableDay,
    error: errorAvailableDay,
    isFetching: isFetchingAvailableDay,
    refetch: refetchAvailableDay,
  } = useFetchGetAvailableDayQuery(selectedCalendarDay?.date || "", {
    skip: !selectedCalendarDay,
  });
  const [createAvailableDay, { isLoading: isLoadingCreateAvailableDay }] =
    useFetchCreateAvailableDayMutation();

  // Effects.

  // Set selected calendar day to first day of month.
  useEffect(() => {
    if (calendar) {
      const firstDayOfMonth = getFirstDayOfMonth(calendar.days);
      if (firstDayOfMonth) {
        setSelectedCalendarDay(firstDayOfMonth);
      }
    }
  }, [calendar]);

  // Refetch calendar when selected month and year changes.
  useEffect(() => {
    if (calendar) {
      refetchCalendar();
    }
  }, [initialDayDetails.year, initialDayDetails.month]);

  // REfetch available day when selected day changes.
  useEffect(() => {
    if (selectedCalendarDay) {
      refetchAvailableDay();
    }
  }, [selectedCalendarDay]);

  // Set available day.
  useEffect(() => {
    if (dataAvailableDay) {
      setAvailableDay(dataAvailableDay);
    }
  }, [dataAvailableDay]);

  return (
    <div className="admin-page-availability ">
      <div className="header-availability pb-4">
        <h1>{tr("Availability")}</h1>
        <p>
          {tr(
            "Here, you can set the services for days and time people can make bookings.",
          )}
        </p>
        {renderSaveButton()}
      </div>
      <div className="availability-content flex flex-wrap gap-4 pr-4">
        {renderCalendar()}
        {selectedCalendarDay && (
          <>
            {renderTimeRanges()}
            {renderServices()}
          </>
        )}
      </div>
      {renderSaveButton()}
    </div>
  );

  function renderSaveButton() {
    if (!selectedCalendarDay) return null;
    return (
      <div className="save-button-wrapper py-2">
        <LoadingButton
          loading={isLoadingCreateAvailableDay}
          loadingPosition="start"
          startIcon={<SaveOutlinedIcon />}
          variant="outlined"
          onClick={handleSave}
        >
          {tr("Save")}
        </LoadingButton>
      </div>
    );
  }

  function renderTimeRanges() {
    return (
      <div className="services-wrapper w-full bg-white shadow my-4 p-2 rounded max-w-[400px]">
        <div className="services-wrapper flex-initial min-w-[250px] ">
          <MainWrapper
            isLoading={isLoadingAvailableDay || isFetchingAvailableDay}
            error={errorCalendar}
            data={calendar}
          >
            {availableDay && (
              <>
                <span className={"my-3 text-base"}>
                  <em>{tr("Time ranges")} </em>
                  {tr("that can be booked on")}{" "}
                </span>
                <span className="text-xl font-semibold">
                  {availableDay.date}
                </span>
                <TimeRangeView
                  timeRanges={availableDay.timeRanges}
                  onUpdateTimeRanges={handleUpdateTimeRanges}
                />
              </>
            )}
          </MainWrapper>
        </div>
      </div>
    );
  }

  function renderServices() {
    return (
      <div className="services-wrapper w-full bg-white shadow my-4 p-2 rounded max-w-[400px]">
        <div className="services-wrapper flex-initial min-w-[250px] max-h-[300px] overflow-y-auto">
          <MainWrapper
            isLoading={isLoadingAvailableDay || isFetchingAvailableDay}
            error={errorCalendar}
            data={calendar}
          >
            {availableDay && (
              <>
                <span className={"my-3 text-base"}>
                  {tr("Services that can be purchased on")}{" "}
                </span>
                <span className="text-xl font-semibold">
                  {availableDay.date}
                </span>
                <ServicesView
                  services={services || []}
                  selectedServiceIds={getServiceIdsFromAvailableDay(
                    availableDay,
                  )}
                  onDeselectedService={handleServiceDeselected}
                  onSelectedService={handleServiceSelected}
                />
              </>
            )}
          </MainWrapper>
        </div>
      </div>
    );
  }

  function renderCalendar() {
    return (
      <div className="calendar-wrapper flex-initial w-[250px] shadow rounded p-3 bg-white">
        <MainWrapper
          isLoading={isLoadingCalendar || isFetchingCalendar}
          error={errorCalendar}
          data={calendar}
        >
          {calendar && (
            <CalendarView
              calendar={calendar}
              activeMonth={initialDayDetails.month}
              activeYear={initialDayDetails.year}
              activeCalendarDay={selectedCalendarDay}
              onSelectedDay={handleOnSelectDay}
              onSelectMonth={handleOnSelectMonth}
              onSelectYear={handleOnSelectYear}
            />
          )}
        </MainWrapper>
      </div>
    );
  }

  function handleOnSelectDay(day: CalendarNamespace.CalendarDay) {
    setSelectedCalendarDay(day);
  }

  function handleOnSelectMonth(month: number) {
    setInitialDayDetails((prev) => ({ ...prev, month }));
  }

  function handleOnSelectYear(year: number) {
    setInitialDayDetails((prev) => ({ ...prev, year, month: 1 }));
  }

  function handleServiceSelected(service: WpService) {
    if (availableDay) {
      const newAvailableDay: AvailableDay = { ...availableDay };
      const existinService = newAvailableDay?.services?.find(
        (s) => s.id === service.term_id,
      );
      if (!existinService) {
        if (newAvailableDay) {
          setAvailableDay({
            ...newAvailableDay,
            services: [
              ...newAvailableDay.services,
              {
                id: service.term_id,
                timingInMinutes: service.timingInMinutes,
              },
            ],
          });
        }
      }
    }
  }

  function handleServiceDeselected(service: WpService) {
    if (availableDay) {
      const newAvailableDay: AvailableDay = { ...availableDay };
      const existinService = newAvailableDay?.services?.find(
        (s) => s.id === service.term_id,
      );
      if (existinService) {
        if (newAvailableDay) {
          setAvailableDay({
            ...newAvailableDay,
            services: newAvailableDay.services.filter(
              (s) => s.id !== service.term_id,
            ),
          });
        }
      }
    }
  }

  function handleUpdateTimeRanges(timeRanges: TimeRange[]) {
    if (availableDay) {
      setAvailableDay({ ...availableDay, timeRanges });
    }
  }

  function handleSave() {
    if (availableDay) {
      createAvailableDay({
        date: availableDay.date,
        service_ids: availableDay.services.map((service) => service.id),
        time_ranges: availableDay.timeRanges,
        all_services_available: allServicesAvailable,
      })
        .unwrap()
        .then((data) => {
          toast.success("Saved");
        })
        .catch((error) => {
          toast.error("Unable to save");
        });
    }
  }
}
export function getFirstDayOfMonth(
  calendarDays: CalendarDay[],
): CalendarDay | undefined {
  return calendarDays.find((day) => day.inThisMonth);
}

function getServiceIdsFromAvailableDay(availableDay: AvailableDay): number[] {
  return availableDay.services.map((service) => service.id);
}
