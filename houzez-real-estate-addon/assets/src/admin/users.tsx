// @ts-ignore
import { getClientData } from "../libs/client-data";
import "../css/admin/hre-users.scss";
// @ts-ignore
import ReactDOM from "react-dom";
// @ts-ignore
import React from "react";
import "react-toastify/dist/ReactToastify.css";
import { toast, ToastContainer } from "react-toastify";
import { Provider } from "react-redux";
import { myStore, RootState } from "../rtk/mystore";
import { ThemeProvider } from "@mui/material/styles";
import muiThemeSettings from "../theme";
import { tr } from "../i18n/tr";
import { restGetErrorMessage } from "../rtk/myapi";
import { Resource } from "../libs/Resource";
import BuyerPreferenceModal from "../features/user/BuyerPreferenceModal";
import BuyerAgentDetailsModal from "../features/user/BuyerAgentDetailsModal";

declare let jQuery: any;
declare let console: any;

const theme = muiThemeSettings;

let user: CptUser | null = null;
(() => {
  jQuery(document).ready(() => {
    // Add popup root.
    jQuery("body").append('<div id="hre-popup-root"></div>');

    user = new CptUser();
  });
})();

class CptUser {
  constructor() {
    this.renderToast();
    this.renderViewBuyerPreferenceButtons();
    this.watchClickBuyerPreference();
    this.watchClickAgentDetails();
    this.renderUserPointsHistoryModal();
    this.renderAgentDetailsModal();
    this.watchSelectOfAgentFilter();
  }

  protected watchClickBuyerPreference() {
    jQuery("body").on("click", "button.hre-view-buyer-preference", (e) => {
      const parent = jQuery(e.target).parents("tr");
      const elements = this.getElements(parent);

      console.log("click", { elements, parent });
      const props = [elements.userId];
      jQuery(document).trigger("hre_open_modal_buyer_preference", props);
    });
  }

  protected watchClickAgentDetails() {
    jQuery("body").on("click", "button.hre-view-agent-details", (e) => {
      const parent = jQuery(e.target).parents("tr");
      const elements = this.getElements(parent);

      console.log("click", { elements, parent });
      const props = [elements.userId];
      jQuery(document).trigger("hre_open_modal_agent_details", props);
    });
  }

  protected watchSelectOfAgentFilter() {
    jQuery(".hre-filter-button").on("click", (e) => {
      this.submitFormsToApplyFilter();
    });
  }

  protected submitFormsToApplyFilter() {
    const state = jQuery('select[name="hre_agent_state"]').val();
    const city = jQuery('select[name="hre_agent_city"]').val();
    const zipCode = jQuery('select[name="hre_agent_zip_code"]').val();

    const currentUrl = window.location.href;
    const values = {
      hre_agent_state: state,
      hre_agent_city: city,
      hre_agent_zip_code: zipCode,
    };
    const newUrl = this.updateUrlParams(currentUrl, values);
    // console.log({ state, city, zipCode, newUrl });
    window.location.href = newUrl;
  }

  protected updateUrlParams(
    url: string,
    obj: { [key: string]: string },
  ): string {
    // const urlParams = new URLSearchParams(url);
    // for (const key in obj) {
    //   if (obj.hasOwnProperty(key)) {
    //     urlParams.set(key, obj[key]);
    //   }
    // }
    // return urlParams.toString();

    const urlObject = new URL(url);
    const searchParams = urlObject.searchParams;
    for (const key in obj) {
      if (obj.hasOwnProperty(key)) {
        searchParams.set(key, obj[key]);
      }
    }
    return urlObject.toString();
  }

  protected getElements(parent) {
    const userId = parseInt(parent.attr("id").split("-")[1]);

    return {
      userId,
    };
  }

  protected renderViewBuyerPreferenceButtons() {
    jQuery("td.hre_buyer_preference").each((index, element) => {
      const cls = `class="hre-view-buyer-preference !bg-hre text-white hover:!bg-hre-hover cursor-pointer !border-0 disabled:opacity-30 disabled:cursor-not-allowed"`;
      const userId = parseInt(jQuery(element).parents("tr").attr("data-id"));
      const dataUserId = `data-user-id="${userId}"`;
      jQuery(element).html(
        `<button type="button" ${cls} ${dataUserId} >${tr(
          "Preference",
        )}</button>`,
      );
    });
  }

  protected renderToast() {
    jQuery("body").append('<div class="hre-toast-wrapper"></div>');
    jQuery(".hre-toast-wrapper").each((index, element) => {
      ReactDOM.render(
        <React.StrictMode>
          <Provider store={myStore}>
            <ThemeProvider theme={theme}>
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

  protected renderUserPointsHistoryModal() {
    jQuery("body").append('<div id="hre-buyer-preference-modal"></div>');
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <BuyerPreferenceModal />
          </ThemeProvider>
        </Provider>
      </React.StrictMode>,
      document.getElementById("hre-buyer-preference-modal"),
    );
  }

  protected renderAgentDetailsModal() {
    jQuery("body").append('<div id="hre-buyer-agent-detail-modal"></div>');
    ReactDOM.render(
      <React.StrictMode>
        <Provider store={myStore}>
          <ThemeProvider theme={theme}>
            <BuyerAgentDetailsModal />
          </ThemeProvider>
        </Provider>
      </React.StrictMode>,
      document.getElementById("hre-buyer-agent-detail-modal"),
    );
  }
}
