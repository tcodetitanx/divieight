// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../../css/admin/pages/admin-page-settings.scss";
import { Provider } from "react-redux";
import { myStore } from "../../rtk/mystore";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import AdminPageSettings from "./AdminPageSettings";

console.log("Admin Settings");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  ReactDOM.render(
    <React.StrictMode>
      <Provider store={myStore}>
        <AdminPageSettings />
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
    document.getElementById("hre-admin-page-settings"),
  );
}
