import * as React from "react";
import * as ReactDOM from "react-dom";
import "../css/shortcode/sc-user-dashboard.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import UserDashboard from "../features/UserDashboard/UserDashboard";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import muiThemeSettings from "../theme";
import InquiryForm from "../features/inquiry/InquiryForm";

const theme = muiThemeSettings;

// console.log("AdminPageAvailability");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-inquiry").each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <InquiryForm />
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
      // document.getElementById("hre-shortcode-booking"),
    );
  });
}
