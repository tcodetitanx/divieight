// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../../css/admin/pages/admin-page-settings.scss";
import { Provider } from "react-redux";
import { myStore } from "../../rtk/mystore";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import MetaBoxAgreement from "./MetaBoxAgreement";

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  const element = document.getElementById("hre-meta-box-agreement");
  const agreement = element?.attributes["data-agreement-1"]?.value;
  const agreement2 = element?.attributes["data-agreement-2"]?.value;
  const signMode = element?.attributes["data-sign-mode"]?.value;

  ReactDOM.render(
    <React.StrictMode>
      <Provider store={myStore}>
        <MetaBoxAgreement
          agreement={agreement}
          agreement2={agreement2}
          signMode={signMode}
        />
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
