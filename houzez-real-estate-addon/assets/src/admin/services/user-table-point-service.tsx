import React from "react";
import ReactDOM from "react-dom";
import "../../css/admin/pages/admin-page-settings.scss";
import { Provider } from "react-redux";
import { myStore } from "../../rtk/mystore";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import UserTablePointService from "./UserTablePointService";
import { getClientData } from "../../libs/client-data";

jQuery(() => {
  const parent = document.getElementById("hre-admin-user-table-point-service");
  jQuery("body").on("click", "button.hre-view-edit-points", function (elem) {
    const userId = jQuery(this).attr("data-user-id");
    getClientData().userId = parseInt(userId);

    console.log("userId", userId);

    // Remove all other instances
    jQuery(".hre-admin-user-table-point-service").remove();
    const randomHash = Math.random().toString(36).substring(7);
    // create a new div and append it to the body
    const newDiv = jQuery("body").append(
      "<div class='hre-admin-user-table-point-service'></div>",
    );

    createReactInstance(parseInt(userId), parent, randomHash);
  });

  // createReactInstance();
});

function createReactInstance(userId: number, elem, randomHash: string) {
  ReactDOM.render(
    <React.StrictMode>
      <Provider store={myStore}>
        {/* <AdminPageSettings /> */}
        <UserTablePointService userId={userId} randomKey={randomHash} />
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
    elem,
  );
}
