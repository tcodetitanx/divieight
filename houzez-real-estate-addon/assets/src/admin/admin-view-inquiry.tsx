// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../css/admin/admin-view-agreement-form.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import ViewAgreementModal from "./ViewAgreementModal";
import { Resource } from "../libs/Resource";
import AdminViewInquiry from "../features/inquiry/AdminViewInquiry";

declare let jQuery: any;

const theme = createTheme({
  palette: {
    primary: {
      main: "#47aeb6",
    },
    secondary: {
      main: "#9E9E9E",
    },
  },
});

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  // if (Resource.jQuery()) {
  //   return;
  // }
  console.log("createReactInstance");
  jQuery(".hre-admin-inquiry-root").each((index, element) => {
    console.log("jfiej");
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <AdminViewInquiry />
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
          </ThemeProvider>
        </Provider>
      </React.StrictMode>,
      element,
    );
  });
}
