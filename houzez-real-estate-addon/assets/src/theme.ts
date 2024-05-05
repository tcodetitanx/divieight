import { createTheme } from "@mui/material/styles";
import { CSSProperties } from "react";

const muiThemeSettings = createTheme({
  palette: {
    primary: {
      main: "#47aeb6",
    },
    secondary: {
      main: "#9E9E9E",
    },
  },
  components: {
    // Name of the component
    MuiButton: {
      styleOverrides: {
        root: ({ ownerState }) => ({
          ...(ownerState.variant === "contained" &&
            ownerState.color === "primary" && {
              backgroundColor: "#47aeb6",
              borderRadius: "5px", // Set your desired border radius
              color: "#fff",
            }),
        }),
      },
    },
  },
});
export default muiThemeSettings;
