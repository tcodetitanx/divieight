import { configureStore } from "@reduxjs/toolkit";
import { myApi } from "./myapi";
import availabilitySlice from "./store-slices/availability-slice";
import userDashboard from "./store-slices/userDashboardSlice";
export const myStore = configureStore({
  reducer: {
    [myApi.reducerPath]: myApi.reducer,
    availability: availabilitySlice,
    userDashboard,
  },
  middleware: (getDefaultMiddleware) =>
    getDefaultMiddleware().concat(myApi.middleware),
});

export type RootState = ReturnType<typeof myStore.getState>;

export type AppDispatch = typeof myStore.dispatch;
// export type AppThunk<ReturnType = void> = ThunkAction<
//   ReturnType,
//   AppState,
//   unknown,
//   Action<string>
// >;

// export const useMyAppDispatch = useDispatch<AppDispatch>();
// export const useMyAppSelector: TypedUseSelectorHook<AppState> = useSelector;
