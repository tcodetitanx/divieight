import * as React from "react";
import * as ReactDOM from "react-dom";
import "../css/shortcode/sc-user-dashboard.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import BuyerOnboardingProcess1 from "../features/buyer-onboarding/BuyerOnboardingProcess1";
import muiThemeSettings from "../theme";
import BuyerOnboardingProcess2 from "../features/buyer-onboarding/BuyerOnboardingProcess2";

// console.log("AdminPageAvailability");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-buyer-onboarding-2").each((index, element) => {
    // console.log({ index, element });
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={muiThemeSettings}>
            <BuyerOnboardingProcess2 />
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
