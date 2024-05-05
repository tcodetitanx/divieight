// @ts-ignore
import { getClientData } from "../libs/client-data";
import "../css/admin/hre-elementor-submissions.scss";
// @ts-ignore
import ReactDOM from "react-dom";
// @ts-ignore
import React from "react";
import { ToastContainer } from "react-toastify";
import ApproveAgentModal from "./ApproveAgentModal";
import { StyledEngineProvider } from "@mui/material";
import "react-toastify/dist/ReactToastify.css";

declare let jQuery: any;
declare let console: any;

(() => {
  jQuery(document).ready(() => {
    // Add popup root.
    jQuery("body").append('<div id="hre-popup-root"></div>');

    startRendering();

    // jQuery("body").on("hre-agent-approved", (event: any, data: any) => {
    //   startRendering();
    // });
  });
})();

function createReactInstance() {
  // console.log("createReactInstance");
  ReactDOM.render(
    <React.StrictMode>
      <StyledEngineProvider injectFirst>
        <>
          <ApproveAgentModal />
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
        </>
      </StyledEngineProvider>
    </React.StrictMode>,
    document.getElementById("hre-popup-root"),
  );
}

function startRendering() {
  // Render buttons.
  tableLoadedWithBody().then(() => {
    const elements = getElements();
    // console.log({ elements });
    addColumnTh();
    HreUi.addColumnTds();
    HreUi.listenForButtonClick();
    createReactInstance();
  });
}
function addColumnTh() {
  const elements = getElements();
  const th = jQuery("<th>Approve</th>");
  th.insertAfter(elements.thHeader.find("th:last"));
  // console.log("th", th);
}

function displayButtons(rows: any[]) {
  const elements = getElements();
}

function tableLoadedWithBody(): Promise<boolean> {
  return new Promise((res, reject) => {
    const internal = setInterval(() => {
      const table = jQuery("table.e-form-submissions-list-table");
      if (table.find("tbody tr").length > 0) {
        clearInterval(internal);
        res(true);
      }
    }, 1000);
  });
}

function getElements() {
  const table = jQuery("table.e-form-submissions-list-table");
  const thHeader = table.find("thead tr");
  const tbodyRows: { a: any; submissionId: number; td: any }[] = [];
  const trs = table.find("tbody tr");
  trs.each((index: number, tr: any) => {
    const a = jQuery(jQuery(tr).find("td:nth-child(2) a")[0]);
    const submissionId = parseInt(a.attr("href").split("#")[1].substring(1));
    tbodyRows.push({
      submissionId,
      a,
      td: jQuery(tr).find("td:last"),
    });
  });

  return {
    table,
    thHeader,
    tbodyRows,
  };
}

class HreUi {
  public static addColumnTds() {
    const elements = getElements();
    elements.tbodyRows.forEach((row) => {
      const td = jQuery("<td></td>");
      td.insertAfter(row.td);
      td.append(HreUi.loadingIcon());
      HreApi.isAgentApproved(row.submissionId).then((response) => {
        // console.log({ isApproved: response, approved: response.approved });
        if (true === response.approved) {
          td.html(HreUi.renderApproved());
        } else {
          HreUi.addApproveButton(td, row.submissionId);
          const stringify = btoa(
            JSON.stringify({
              submissionId: row.submissionId,
              details: response.more.agent_details,
            }),
          );
          // console.log({ stringify });
          td.attr("data-approve-data", stringify);
        }
      });

      // HreUi.addApproveButton(row.submissionId, (submissionId: number) => {
      //   HreApi.approveSubmission(submissionId).then(() => {
      //     td.html("Approved");
      //   });
      // });
    });
  }

  private static approveCallback(submissionId: number) {
    return HreApi.approveSubmission(submissionId);
  }

  private static addApproveButton(td: any, submissionId: number) {
    const button = jQuery(
      `<button data-submission-id='${submissionId}' class='hre-approve-button button button-primary'>Approve</button>`,
    );
    td.html(button);
  }

  public static listenForButtonClick() {
    jQuery("body").on("click", ".button.hre-approve-button", (e: any) => {
      // const button = jQuery(e.target);
      // const submissionId = button.data("submission-id");
      // const td = button.parent();
      // td.html(HreUi.loadingIcon());
      const data = jQuery(e.target).parent("td").attr("data-approve-data");
      // console.log("listenForButtonClick", { data });
      // jQuery("body").trigger("show-agent-approval-modal", { one: 1, two: 2 });
      // jQuery('body').attr('data-href-agent-data')
      jQuery("body").trigger("show-agent-approval-modal", data);
      // HreUi.approveCallback(submissionId)
      //   .then(() => {
      //     td.html(HreUi.renderApproved());
      //   })
      //   .catch(() => {
      //     HreUi.addApproveButton(td, submissionId);
      //   });
    });
  }

  private static loadingIcon() {
    HreUi.addLoadingStyle();
    return jQuery(
      "<span class='dashicons dashicons-update dashicons-spin hre-rotate'></span>",
    );
  }

  private static addLoadingStyle() {
    const styles = ` 
        <style id="hre-loading-style">
         .hre-rotate {
              animation: rotate 2s linear infinite;
         }

            @keyframes rotate {
              from {
                transform: rotate(0deg);
              }
              to {
                transform: rotate(359deg);
              }
            } 
        </style>
      `;
    if (jQuery("#hre-loading-style").length === 0) {
      jQuery("head").append(styles);
    }
  }

  private static renderApproved() {
    // Approved text and check icon.
    return jQuery(
      "<span style='color:#2271b1'><span class='dashicons dashicons-yes' ></span> Approved</span>",
    );
  }
}

export class HreApi {
  public static approveSubmission(submissionId: number) {
    // console.log({ submissionId });
    return new Promise((res, reject) => {
      jQuery.ajax({
        url: "/wp-json/hre-addon/v1/elementor-agent-form/approve-agent",
        // Add rest_nonce
        headers: {
          "X-WP-Nonce": getClientData().rest_nonce,
        },
        method: "POST",
        data: {
          "form-submission-id": submissionId,
        },
        success: (response: any) => {
          res(response);
        },
        error: (err: any) => {
          reject(err);
        },
      });
    });
  }

  public static isAgentApproved(
    submissionId: number,
  ): Promise<IsAgentApprovedResponse> {
    return new Promise((res, reject) => {
      jQuery.ajax({
        url: "/wp-json/hre-addon/v1/elementor-agent-form/is-agent-approved",
        // Add rest_nonce
        headers: {
          "X-WP-Nonce": getClientData().rest_nonce,
        },
        method: "POST",
        data: {
          "form-submission-id": submissionId,
        },
        success: (response: any) => {
          res(response);
        },
        error: (err: any) => {
          reject(err);
        },
      });
    });
  }
}

export interface IsAgentApprovedResponse {
  approved: boolean;
  more: {
    agent_details: {
      key: string;
      label: string;
      value: string;
    }[];
  };
}
