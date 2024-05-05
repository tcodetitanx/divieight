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
import muiThemeSettings from "../theme";

const theme = muiThemeSettings;

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-signup").each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <EliteSignup for={"buyer"} />
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
