import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { BookingStatus } from "../../my-types";

export interface UserDashboardSliceState {
  bookingListStatus: BookingStatus;
}

const initialState: UserDashboardSliceState = {
  bookingListStatus: "all",
};

export const userDashboardSlice = createSlice({
  name: "userDashboard",
  initialState,
  reducers: {
    setBookingListStatus: (state, action: PayloadAction<BookingStatus>) => {
      state.bookingListStatus = action.payload;
    },
  },
});

export const { setBookingListStatus } = userDashboardSlice.actions;
export default userDashboardSlice.reducer;
