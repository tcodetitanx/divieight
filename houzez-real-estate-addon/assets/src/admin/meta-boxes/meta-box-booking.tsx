import React from "react";
import ReactDOM from "react-dom";
import "../../css/admin/pages/admin-page-settings.scss";
import { Provider } from "react-redux";
import { myStore } from "../../rtk/mystore";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import MetaBoxBooking from "./MetaBoxBooking";
import { getClientData } from "../../libs/client-data";

console.log("Admin Settings");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  const element = document.getElementById("hre-meta-box-booking");
  const id = element?.attributes["data-booking-id"]?.value;
  const userId = element?.attributes["data-user-id"]?.value;

  getClientData().userId = parseInt(userId);

  ReactDOM.render(
    <React.StrictMode>
      <Provider store={myStore}>
        <MetaBoxBooking bookingId={parseInt(id)} />
        <ToastContainer
          position="bottom-right"
          autoClose={5000}
          hideProgressBar={false}
          newestOnTop={false}
          closeOnClick
          rtl={false}
          pauseOnFocusLoss
          draggable
          pauseOnHover
          theme="light"
        />
      </Provider>
    </React.StrictMode>,
    element,
  );
}
