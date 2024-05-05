import React from "react";
import ReactDOM from "react-dom";
import "../../css/admin/pages/admin-page-availability.scss";
import { Provider } from "react-redux";
import { myStore } from "../../rtk/mystore";
import AdminPageAvailability from "./AdminPageAvailability";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import {
  ApolloClient,
  ApolloProvider,
  InMemoryCache,
  NormalizedCacheObject,
} from "@apollo/client";

console.log("AdminPageAvailability");

jQuery(() => {
  const client = new ApolloClient({
    // todo: move to config
    uri: "https://test2.test/wp-json/hre-addon/v1/graphql",
    cache: new InMemoryCache(),
  });

  createReactInstance(client);
});

function createReactInstance(
  apolloClient: ApolloClient<NormalizedCacheObject>,
) {
  ReactDOM.render(
    <React.StrictMode>
      <Provider store={myStore}>
        <ApolloProvider client={apolloClient}>
          <AdminPageAvailability />
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
        </ApolloProvider>
      </Provider>
    </React.StrictMode>,
    document.getElementById("hre-admin-page-availability"),
  );
}
