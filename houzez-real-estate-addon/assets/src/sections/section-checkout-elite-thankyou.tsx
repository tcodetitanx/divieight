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
import EliteSignup from "../features/buyer-login/EliteSignup";
import EliteCheckoutThankyou from "../features/elite-checkout-thankyou/EliteCheckoutThankyou";
import muiThemeSettings from "../theme";
import { getClientData } from "../libs/client-data";

const theme = muiThemeSettings;

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-section-checkout-elite-thank-you").each((index, element) => {
    const dataFor = jQuery(element).attr("data-for");
    const cs = getClientData().client_settings;
    const url =
      "buyer" === dataFor ? cs.buyer_elite_page_url : cs.seller_elite_page_url;
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <EliteCheckoutThankyou elitePageUrl={url} />
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
