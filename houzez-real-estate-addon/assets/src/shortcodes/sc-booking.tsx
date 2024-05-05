import React from "react";
import ReactDOM from "react-dom";
import "../css/shortcode/sc-booking.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import BookAService from "../features/Booking/BookAService";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { ThemeOptions, ThemeProvider, createTheme } from "@mui/material/styles";

// console.log("AdminPageAvailability");

jQuery(() => {
  createReactInstance();
});

const themeOptions: ThemeOptions = {
  palette: {
    primary: {
      main: "#30c7b5",
    },
    secondary: {
      main: "#30c7b5",
    },
  },
  components: {
    MuiButton: {
      styleOverrides: {
        root: {
          borderRadius: 0, // No roundness
          padding: "10px 20px", // Adjust padding
          color: "white", // Text color
          "&:hover": {
            color: "white", // Text color on hover
          },
        },
      },
    },
  },
};

const theme = createTheme(themeOptions);
function createReactInstance() {
  jQuery(".hre-shortcode-booking").each((index, element) => {
    const id = jQuery(element).attr("id");
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <BookAService />
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
