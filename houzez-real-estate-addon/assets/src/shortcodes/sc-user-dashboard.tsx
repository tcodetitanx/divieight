import React from "react";
import ReactDOM from "react-dom";
import "../css/shortcode/sc-user-dashboard.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import UserDashboard from "../features/UserDashboard/UserDashboard";
import { ThemeProvider, createTheme } from "@mui/material/styles";
import "react-toastify/dist/ReactToastify.css";
const theme = createTheme({
  palette: {
    primary: {
      main: "#1976D2",
    },
    secondary: {
      main: "#9E9E9E",
    },
  },
});

// console.log("AdminPageAvailability");

jQuery(() => {
  createReactInstance();
});

function createReactInstance() {
  jQuery(".hre-shortcode-user-dashboard").each((index, element) => {
    const id = jQuery(element).attr("id");
    // console.log({ index, element });
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <UserDashboard />
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
