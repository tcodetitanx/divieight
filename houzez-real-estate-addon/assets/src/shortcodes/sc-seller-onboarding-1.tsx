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
import SellerOnboardingProcess1 from "../features/seller-onboarding/SellerOnboardingProcess1";

// console.log("AdminPageAvailability");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-seller-onboarding").each((index, element) => {
    // console.log({ index, element });
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={muiThemeSettings}>
            <SellerOnboardingProcess1 />
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
