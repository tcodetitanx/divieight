import React, { useState, useEffect } from "react";
import classNames from "classnames";
import { CalendarNamespace, Service } from "../../my-types";
import Calendar = CalendarNamespace.Calendar;
import MainWrapper from "../Wrapper/MainWrapper";
import CalendarDay = CalendarNamespace.CalendarDay;
import CalendarMonth = CalendarNamespace.CalendarMonth;
import { tr } from "../../i18n/tr";
import { LinearProgress, Tooltip } from "@mui/material";
import { getClientData } from "../../libs/client-data";

export default function CalendarView({
  calendar,
  activeCalendarDay,
  onSelectMonth,
  onSelectYear,
  onSelectedDay,
  activeMonth,
  activeYear,
  acceptableServiceId,
  isLoading,
}: CalendarViewProps) {
  // const [currentMonthIndex, setCurrentMonthIndex] = useState(today.getMonth());
  const inAdmin = getClientData().inAdmin;
  // const [currentYearIndex, setCurrentYearIndex] = useState(0);
  // const [activeCalendarDay, setActiveCalendarDay] = useState<CalendarDay>(
  //   calendar.days[0],
  // );

  // Set active calendar day to today.
  // useEffect(() => {
  //   const todayDay = calendar.days.find(
  //     (day) => day.date === today.toISOString().split("T")[0],
  //   );
  //   if (todayDay) {
  //     setActiveCalendarDay(todayDay);
  //   }
  // }, []);

  return (
    <div className="calendar-view  flex flex-col gap-3">
      {displayMonthYearSelector()}
      {weekDaysViews()}
      {isLoading && <LinearProgress />}
      {displayMonthDays()}
    </div>
  );

  function displayMonthYearSelector() {
    return (
      <div className="month-year-selector flex justify-end gap-4">
        <div className="month-selector">
          <select className="border-0 text-medium" onChange={handleSelectMonth}>
            {getMonths().map((month, index) => (
              <option value={month.monthNumber}>{month.monthName}</option>
            ))}
          </select>
        </div>
        <div className="year-selector">
          <select className="border-0 text-medium" onChange={handleSelectYear}>
            {getYears().map((year, index) => (
              <option value={year}>{year}</option>
            ))}
          </select>
        </div>
      </div>
    );
  }

  function weekDaysViews() {
    // const days = ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"];
    return (
      <div className="week-days-view flex justify-between">
        {calendar.topWeekDays.map((day) => (
          <div className="week-day font-medium flex-auto text-hre-10">
            <div className="week-day-wrapper text-center">
              {day.weekDayShortName}
            </div>
          </div>
        ))}
      </div>
    );
  }

  function displayMonthDays() {
    return (
      <div className="month-days-view flex flex-wrap justify-start text-base">
        {calendar.days.map((day) => {
          const cssNotInThisMonth = !day.inThisMonth && "text-gray-300";
          const isActive = day.date === activeCalendarDay?.date;
          // Disable days that are not available for the selected service.
          let disabled = false;
          if (
            disableBasedOnServiceId(
              day.availableDay?.services || [],
              acceptableServiceId || 0,
            ) === false
          ) {
            disabled = true;
          }
          let bgClasses = disabled
            ? classNames(
                "bg-white hover:bg-white !pointer-none",
                "text-gray-400 hover:text-gray-400",
              )
            : classNames(
                isActive
                  ? "bg-hre-10 hover:bg-hre-10 active:bg-hre-10 focus:bg-hre-10"
                  : "bg-white hover:bg-blue-100",
                isActive
                  ? "text-white hover-text-white"
                  : "text-hre-10 hover:text-black",
              );

          let textClasses = "";
          if (inAdmin) {
            textClasses = day.inThisMonth ? "text-black" : "text-gray-400";
            bgClasses =
              day.date === activeCalendarDay?.date
                ? "bg-hre-10 hover:bg-hre-10-hover"
                : "bg-white hover:bg-blue-100 hover:text-blue-500 ";
            disabled = false;
          }

          return (
            <div className="month-day font-medium w-[14.2%]">
              {renderTooltip(
                disabled,
                <span>
                  <button
                    onClick={() => handleSelectedDay(day)}
                    className={classNames(
                      "border-0 block w-full h-full p-2 rounded",
                      "month-day-inner rounded text-center ",
                      "transition-all duration-300 ease-in-out",
                      bgClasses,
                      cssNotInThisMonth,
                      textClasses,
                    )}
                    disabled={disabled}
                  >
                    {day.dateDay}
                  </button>
                </span>,
              )}
            </div>
          );
        })}
      </div>
    );
  }

  function renderTooltip(disabled: boolean, children: any) {
    if (inAdmin) {
      return children;
    }
    return (
      <>
        {!inAdmin && (
          <Tooltip title={disabled ? tr("Not Available") : tr("Available")}>
            {children}
          </Tooltip>
        )}
      </>
    );
  }

  function disableBasedOnServiceId(services: Service[], serviceId: number) {
    if (services.length === 0) {
      return false;
    }
    const service = services.find((s) => s.id === serviceId);
    if (service) {
      return false;
    }
    return true;
  }
  function getMonths(): CalendarMonth[] {
    const theMonths: CalendarMonth[] = [];
    calendar.yearsAndMonths.find((yearAndMonths, index) => {
      if (yearAndMonths.year === activeYear) {
        yearAndMonths.months.forEach((month) => theMonths.push(month));
      }
      return false;
    });

    return theMonths;
  }

  function getYears(): number[] {
    return calendar.yearsAndMonths.map((yearAndMonths) => yearAndMonths.year);
  }
  // Handlers.

  function handleSelectYear(e: React.ChangeEvent<HTMLSelectElement>) {
    onSelectYear(parseInt(e.target.value));
  }

  function handleSelectMonth(e: React.ChangeEvent<HTMLSelectElement>) {
    onSelectMonth(parseInt(e.target.value));
  }

  function handleSelectedDay(day: CalendarDay) {
    onSelectedDay(day);
  }
}

export interface CalendarViewProps {
  calendar: Calendar;
  activeCalendarDay: CalendarDay | undefined;
  activeMonth: number;
  activeYear: number;
  onSelectedDay: (day: CalendarDay) => void;
  onSelectMonth: (month: number) => void;
  onSelectYear: (year: number) => void;

  acceptableServiceId?: number;
  isLoading?: boolean;
}
