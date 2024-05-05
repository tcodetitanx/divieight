// @ts-ignore
import React from "react";
import { TimeRange } from "../../my-types";
import { tr } from "../../i18n/tr";

export default function TimeRangeView({
  timeRanges,
  onUpdateTimeRanges,
}: TimeRangeViewProps) {
  return (
    <div className="time-range-view w-auto ">
      {renderTimeRanges(timeRanges)}
      {renderAddTimeRange()}
    </div>
  );

  function renderAddTimeRange() {
    return (
      <div className="add-time-range pt-4 pb-2 ">
        <button
          className="bg-gray-100 text-black rounded px-4 py-2 shadow cursor-pointer hover:bg-gray-200"
          onClick={handleAddTimeRange}
        >
          {tr("Add Time Range")}
        </button>
      </div>
    );
  }
  function renderTimeRanges(range: TimeRange[]) {
    return range.map((timeRange, index) => {
      return (
        <div className="time-range py-3">
          <div className="time-range-inner flex gap-4 items-center">
            <div className="time-range-inner-start flex-auto">
              <label className="w-full flex flex-col gap-1">
                <span className="text-base block">Start Time</span>
                <input
                  value={convertNumberToTime(timeRange.start)}
                  type="time"
                  className="w-full"
                  onInput={(e) => {
                    handleChangeStartTime(e.currentTarget.value, index);
                  }}
                />
                <span className="text-sm block">
                  {tr("Start Time for bookings")}
                </span>
              </label>
            </div>
            <div className="time-range-inner-end flex-auto">
              <label className="w-full flex flex-col gap-1">
                <span className="text-base block">{tr("End Time")}</span>
                <input
                  type="time"
                  className="w-full"
                  value={convertNumberToTime(timeRange.stop)}
                  onInput={(e) => {
                    handleChangeStopTime(e.currentTarget.value, index);
                  }}
                />
                <span className="text-sm block">
                  {tr("End Time for bookings")}
                </span>
              </label>
            </div>
            <div className="remove-time-range">
              <button
                className="bg-gray-100 text-black rounded px-4 py-2 shadow cursor-pointer hover:bg-gray-200 button"
                onClick={() => {
                  onUpdateTimeRanges([
                    ...timeRanges.filter((_, i) => i !== index),
                  ]);
                }}
              >
                x
              </button>
            </div>
          </div>
        </div>
      );
    });
  }
  function handleAddTimeRange() {
    onUpdateTimeRanges([...timeRanges, { id: 0, start: 0, stop: 0 }]);
  }
  function handleChangeStartTime(value: string, index: number) {
    onUpdateTimeRanges([
      ...timeRanges.map((timeRange, i) => {
        if (i === index) {
          return { ...timeRange, start: convertTimeToNumber(value) };
        }
        return timeRange;
      }),
    ]);
  }
  function handleChangeStopTime(value: string, index: number) {
    onUpdateTimeRanges([
      ...timeRanges.map((timeRange, i) => {
        if (i === index) {
          return { ...timeRange, stop: convertTimeToNumber(value) };
        }
        return timeRange;
      }),
    ]);
  }
}

export interface TimeRangeViewProps {
  timeRanges: TimeRange[];
  onUpdateTimeRanges: (timeRanges: TimeRange[]) => void;
}

export function convertNumberToTime(number: number): string {
  const hours = Math.floor(number / 100);
  const minutes = number % 100;
  const formattedHours = hours.toString().padStart(2, "0");
  const formattedMinutes = minutes.toString().padStart(2, "0");
  const result = `${formattedHours}:${formattedMinutes}`;
  return result;
}

export function convertTimeToNumber(time: string): number {
  const [hours, minutes] = time.split(":").map(Number);
  const result = hours * 100 + minutes;
  return result;
}
