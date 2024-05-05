import React from "react";
import { Service, WpService } from "../../my-types";

export default function ServicesView({
  services,
  selectedServiceIds,
  onDeselectedService,
  onSelectedService,
}: ServicesViewProps) {
  const [search, setSearch] = React.useState("");
  return (
    <div className="services-view">
      {/* {displaySelectAll()} */}
      {displaySearchBar()}
      {displayServices(services)}
    </div>
  );

  function displaySelectAll() {
    return (
      <div className="select-all flex gap-4 text-base cursor-pointer justify-start my-2 ">
        <span className={"hover:text-blue-500"}>Select All</span>
        <span>|</span>
        <span className={"hover:text-blue-500"}>Deselect All</span>
      </div>
    );
  }

  function displayServices(theServices: Array<WpService>) {
    return (
      <div className="services-list flex flex-col gap-2">
        {theServices.map((service) =>
          displayService(service, selectedServiceIds),
        )}
      </div>
    );
  }

  function displaySearchBar() {
    return (
      <div className="search-bar flex flex-row gap-2 py-2 px-1 my-2">
        <input
          type="search"
          className="search-input rounded-md !border-0 !bg-gray-100 !px-2 !py-2 w-full"
          placeholder="Search services"
          onInput={handleSearch}
        />
      </div>
    );
  }

  function displayService(service: WpService, serviceIds: number[]) {
    if (
      search.length > 0 &&
      !service.name.toLowerCase().includes(search.toLowerCase())
    )
      return null;

    return (
      <label className="service block w-full flex gap-5 justify-left items-center">
        <div className={"flex items-center pt-2"}>
          <input
            type="checkbox"
            value={service.term_id}
            className={"scale-[1.0]"}
            checked={selectedServiceIds.includes(service.term_id)}
            onChange={handleChangeService}
          />
        </div>
        <div className="service-name text-base">{service.name}</div>
      </label>
    );
  }

  function handleSearch(e: React.ChangeEvent<HTMLInputElement>) {
    setSearch(e.target.value);
  }

  function handleChangeService(e: React.ChangeEvent<HTMLInputElement>) {
    const serviceId = parseInt(e.target.value);
    const service = services.find((s) => s.term_id === serviceId);
    if (service) {
      if (e.target.checked) {
        onSelectedService(service);
      } else {
        onDeselectedService(service);
      }
    }
  }
}

export interface ServicesViewProps {
  services: WpService[];
  selectedServiceIds: number[];
  onSelectedService: (service: WpService) => void;
  onDeselectedService: (service: WpService) => void;
}
