// @ts-ignore
import React from "react";
import TimeRangeView, {
  TimeRangeViewProps,
} from "../../../features/calendar/TimerangeView";

export default function AvailabilityTimerangeLoader({
  timeRanges,
}: AvailabilityTimerangeLoaderProps) {
  return (
    <TimeRangeView
      timeRanges={timeRanges}
      onUpdateTimeRanges={(timeRanges) => {}}
    />
  );
}

export interface AvailabilityTimerangeLoaderProps extends TimeRangeViewProps {}
