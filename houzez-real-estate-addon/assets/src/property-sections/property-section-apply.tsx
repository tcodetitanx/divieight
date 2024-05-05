// @ts-ignore
import React from "react";
// @ts-ignore
import ReactDOM from "react-dom";
import "../css/shortcode/sc-property-section-apply.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
import ApplyForProperty from "./ApplyForProperty";
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
  const root = jQuery(".hre-property-root");
  const propertyId = parseInt(root.attr("data-property-id"));
  console.log({ propertyId, root });
  root.each((index, element) => {
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <ApplyForProperty propertyId={propertyId} />
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
