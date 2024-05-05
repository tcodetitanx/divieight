// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../css/shortcode/sc-agreement.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import ShortcodePropertyAgreement from "./ShortcodePropertyAgreement";

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
  jQuery(".hre-sc-property-agreement").each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <ShortcodePropertyAgreement />
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
