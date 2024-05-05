import { createSlice, PayloadAction } from "@reduxjs/toolkit";
import { ServiceItemDetails, ServiceItemProduct } from "@/my-types";
import { RootState } from "@/stores/store";

export interface AvailabilitySliceState {
  count: number;
}

const initialState: AvailabilitySliceState = {
  count: 0,
};

export const availabilitySlice = createSlice({
  name: "availability",
  initialState,
  reducers: {},
});

export const {} = availabilitySlice.actions;
export default availabilitySlice.reducer;
