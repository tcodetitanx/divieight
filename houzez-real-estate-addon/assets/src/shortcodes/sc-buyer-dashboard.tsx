// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../css/shortcode/sc-user-dashboard.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import ShortcodeBuyerSignup from "../features/buyer-login/ShortcodeBuyerSignup";
import ShortcodePropertyAgreement from "./ShortcodePropertyAgreement";
import ShortcodeBuyerDashboard from "./ShortcodeBuyerDashboard";
import muiThemeSettings from "../theme";

declare let jQuery: any;

const theme = muiThemeSettings;

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-buyer-dashboard").each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <ShortcodeBuyerDashboard />
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
