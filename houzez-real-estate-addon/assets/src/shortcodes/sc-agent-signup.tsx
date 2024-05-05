// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../css/shortcode/sc-agent-signup.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import EliteSignup from "../features/buyer-login/EliteSignup";
import muiThemeSettings from "../theme";
import AgentSignup from "../features/buyer-login/AgentSignup";
import { getClientData } from "../libs/client-data";

const theme = muiThemeSettings;

jQuery(() => {
  createReactInstance();

  // Set google captcha url.
  const siteKey = getClientData().client_settings.google_captcha_site_key;
  jQuery("head").append(
    `<script src="https://www.google.com/recaptcha/api.js?render=${siteKey}"></script>`,
  );
});

function createReactInstance() {
  jQuery(".hre-shortcode-agent-signup").each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <AgentSignup />
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
