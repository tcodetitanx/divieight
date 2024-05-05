import * as React from "react";
import * as ReactDOM from "react-dom";
import "../css/shortcode/sc-buyer-preference.scss";
import { Provider } from "react-redux";
import { myStore } from "../rtk/mystore";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { ThemeOptions, ThemeProvider, createTheme } from "@mui/material/styles";
import ShortcodeBuyerPreference from "../features/buyer-preference/ShortcodeBuyerPreference";

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
  jQuery(".hre-shortcode-buyer-preference").each((index, element) => {
    const id = jQuery(element).attr("id");
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <ShortcodeBuyerPreference />
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
