import React from "react";
import ServicesView, {
  ServicesViewProps,
} from "../../../features/calendar/ServicesView";

export default function AvailabilityServiceLoader({}: AvailabilityServiceLoaderProps) {
  return <ServicesView />;
}

export interface AvailabilityServiceLoaderProps extends ServicesViewProps {}
