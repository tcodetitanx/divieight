// @ts-ignore
import React, { useEffect, useState } from "react";
import { toast } from "react-toastify";
import { ClientData, getClientData } from "../../libs/client-data";
import { tr } from "../../i18n/tr";
import { Tab, Tabs, Typography } from "@mui/material";
import { LoadingButton } from "@mui/lab";
// @ts-ignore
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import {
  restGetErrorMessage,
  useFetchSaveAdminSettingsMutation,
} from "../../rtk/myapi";
import Box from "@mui/material/Box";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import Paper from "@mui/material/Paper";

const he = require("he");

export default function AdminPageSettings() {
  const [
    fetchSaveAdminSettings,
    { isLoading: fetchSaveAdminSettingsIsLoading },
  ] = useFetchSaveAdminSettingsMutation();
  const [adminPageSettings, setAdminPageSettings] = useState<
    ClientData["admin_settings"]
  >(getClientData().admin_settings);
  const pages: ClientData["wp_pages"] = getClientData().wp_pages;
  const [agreement2, setAgreement2] = useState<string>(
    he.decode(adminPageSettings.default_agreement2),
  );
  const [agreement, setAgreement] = useState<string>(
    he.decode(adminPageSettings.default_agreement),
  );
  const [currentTab, setCurrentTab] = React.useState(1);
  const sk: ReturnType<typeof getShortcodes> = getShortcodes();
  const [showGoogleSiteKey, setShowGoogleSiteKey] = useState(false);
  const [showGoogleSecretKey, setShowGoogleSecretKey] = useState(false);

  const handleTabChange = (event: React.SyntheticEvent, newValue: string) => {
    setCurrentTab(newValue);
  };

  useEffect(() => {
    setAdminPageSettings({
      ...adminPageSettings,
      default_agreement: agreement,
      default_agreement2: agreement2,
    });
  }, [agreement, agreement2]);

  return render();

  function render() {
    return (
      <div className="admin-settings-page">
        {renderForm()}
        <br />
        {renderShortcodeLists()}
      </div>
    );
  }

  function renderForm() {
    return (
      <form
        onSubmit={handleSaveSettings}
        className="lg:max-w-[70%] rounded-md p-5 my-4"
      >
        <h1>{tr("Settings")}</h1>
        <div className="ounded-md flex flex-col gap-6 my-4">
          {renderFees()}
          {renderGoogleCaptcha()}
          {renderPages()}
          <div>{renderSubmitButton()}</div>
          <div className="one-section">
            <h2>{tr("Default Agreements")}</h2>
            {renderAgreementTabs()}
          </div>
        </div>
        {renderSubmitButton()}
      </form>
    );
  }

  function renderFees() {
    return (
      <div className="ounded-md flex flex-col gap-6 my-4">
        <div className="one-section">
          <h2>{tr("Fees")}</h2>
          <div className={"lg:flex gap-4 lg:my-4"}>
            {renderBuyerEliteAccessFee()}
            {renderSellerEliteAccessFee()}
          </div>
          <div className={"lg:flex gap-4 lg:my-4"}>{renderPlanDuration()}</div>
        </div>
      </div>
    );
  }

  function renderGoogleCaptcha() {
    return (
      <div className="ounded-md flex flex-col gap-6 my-4">
        <div className="one-section">
          <h2>{tr("Google Captcha ")} </h2>
          <p>{tr("Added to Agent Signup Page")}</p>
          <div className={"lg:flex gap-4 lg:my-4"}>
            {renderGoogleCaptchaSiteKey()}
            {renderGoogleCaptchaSecreteKey()}
          </div>
        </div>
      </div>
    );
  }

  function renderPages() {
    return (
      <div className="one-section">
        <h2>{tr("Pages")}</h2>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderBuyerEliteSignupPage()}
          {renderBuyerEliteLoginPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderSellerEliteSignupPage()}
          {renderSellerEliteLoginPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderAgentSignupPage()}
          {renderAgentLoginPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderBuyerElitePage()}
          {renderSellerElitePage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderPropertyAgreementPage()}
          {renderBuyerDashboardPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderSearchByMapPage()}
          {renderTermsAndConditionsPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderBuyerOnboardingProcess1()}
          {renderSellerOnboardingProcess()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderBuyerOnboardingProcess2()}
          {renderCreateListingPage()}
        </div>
        <div className={"lg:flex gap-4 lg:my-4"}>
          {renderBuyerPreferencePage()}
        </div>
      </div>
    );
  }

  function renderSubmitButton() {
    return (
      <LoadingButton
        variant="contained"
        loading={fetchSaveAdminSettingsIsLoading}
        className=""
        type="submit"
      >
        {tr("Save")}
      </LoadingButton>
    );
  }

  function renderGoogleCaptchaSiteKey() {
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Google Captcha Site Key")}{" "}
          <button
            onClick={() => setShowGoogleSiteKey(!showGoogleSiteKey)}
            type={"button"}
          >
            {" "}
            {showGoogleSiteKey ? "Hide" : "Show"}
          </button>
        </Typography>
        <input
          required={true}
          type={showGoogleSiteKey ? "text" : "password"}
          value={adminPageSettings.google_captcha_site_key}
          onInput={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              google_captcha_site_key: e.currentTarget.value.trim(),
            })
          }
          className="w-full block h-[50px]"
        />
      </label>
    );
  }

  function renderGoogleCaptchaSecreteKey() {
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Google Captcha Secret Key")}{" "}
          <button
            onClick={() => setShowGoogleSecretKey(!showGoogleSecretKey)}
            type={"button"}
          >
            {" "}
            {showGoogleSecretKey ? "Hide" : "Show"}
          </button>
        </Typography>
        <input
          required={true}
          type={showGoogleSecretKey ? "text" : "password"}
          value={adminPageSettings.google_captcha_secret_key}
          onInput={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              google_captcha_secret_key: e.currentTarget.value.trim(),
            })
          }
          className="w-full block h-[50px]"
        />
      </label>
    );
  }

  function renderBuyerEliteAccessFee() {
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Elite Access Fee")}{" "}
        </Typography>
        <input
          required={true}
          type="number"
          value={adminPageSettings.buyer_elite_access_fee}
          onInput={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_elite_access_fee: parseFloat(e.currentTarget.value),
            })
          }
          className="w-full block h-[50px]"
        />
      </label>
    );
  }

  function renderPlanDuration() {
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Elite access Duration")}{" "}
        </Typography>
        <input
          required={true}
          type="number"
          value={adminPageSettings.elite_membership_duration_months}
          onInput={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              elite_membership_duration_months: parseFloat(
                e.currentTarget.value,
              ),
            })
          }
          className="w-full block h-[50px]"
        />
        <p className={"!p-0 !m-0 text-gray-500"}>
          How long will each elite access fee last? (in months)
        </p>
      </label>
    );
  }

  function renderSellerEliteAccessFee() {
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Seller Elite Access Fee")}{" "}
        </Typography>
        <input
          required={true}
          type="number"
          value={adminPageSettings.seller_elite_access_fee}
          onInput={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              seller_elite_access_fee: parseFloat(e.currentTarget.value),
            })
          }
          className="w-full block h-[50px]"
        />
      </label>
    );
  }

  function renderBuyerEliteSignupPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_elite_signup_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Elite Signup Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_elite_signup_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.buyer_elite_signup_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_buyer_elite_signup.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderSellerEliteSignupPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.seller_elite_signup_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Seller Elite Signup Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              seller_elite_signup_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.seller_elite_signup_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_seller_elite_signup.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderAgentSignupPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.seller_elite_signup_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Agent Signup Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              agent_signup_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.agent_signup_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_agent_signup.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderBuyerEliteLoginPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_elite_login_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Elite Login Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_elite_login_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.buyer_elite_login_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_buyer_elite_login.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderSellerEliteLoginPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.seller_elite_login_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Seller Elite Login Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              seller_elite_login_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.seller_elite_login_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_seller_elite_login.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderBuyerPreferencePage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_preference_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Preference Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_preference_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.buyer_preference_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_buyer_preference.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderAgentLoginPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.agent_login_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Agent Login Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              agent_login_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.agent_login_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_agent_login.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderSearchByMapPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.search_by_map_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Search by map page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              search_by_map_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.search_by_map_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
      </label>
    );
  }

  function renderTermsAndConditionsPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.terms_and_conditions_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Terms and conditions page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              terms_and_conditions_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.terms_and_conditions_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
      </label>
    );
  }

  function renderBuyerOnboardingProcess1() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_onboarding_process_1_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer onboarding process 1")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_onboarding_process_1_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.buyer_onboarding_process_1_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.buyer_onboarding_1.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderBuyerOnboardingProcess2() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_onboarding_process_2_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer onboarding process 2")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_onboarding_process_2_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.buyer_onboarding_process_2_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.buyer_onboarding_2.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderCreateListingPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.create_listing_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Create Listing Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              create_listing_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.create_listing_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          This should be the page where agents can create listings. By selecting
          it here, the plugin will be able to protect it from non agents. Anyone
          who is not logged n as an agent will be redirected to the
          <b> {tr("Agent Login Page")} </b>
        </p>
      </label>
    );
  }

  function renderSellerOnboardingProcess() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.seller_onboarding_process_1_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Seller onboarding process ")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              seller_onboarding_process_1_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id ===
                adminPageSettings.seller_onboarding_process_1_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.seller_onboarding_1.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderPropertyAgreementPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.property_agreement_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Property Agreement Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              property_agreement_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={
                page.id === adminPageSettings.property_agreement_page_id
              }
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>[property_agreement_view]</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderSellerElitePage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.seller_elite_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Seller Elite Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              seller_elite_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.seller_elite_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
      </label>
    );
  }

  function renderBuyerElitePage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_elite_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Elite Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_elite_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.buyer_elite_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
      </label>
    );
  }

  function renderBuyerDashboardPage() {
    const url = pages.find(
      (p) => p.id === adminPageSettings.buyer_dashboard_page_id,
    )?.url;
    return (
      <label className="w-full flex flex-col gap-1">
        <Typography variant="body1" className="block w-full">
          {tr("Buyer Dashboard Page")}{" "}
        </Typography>
        <select
          required={true}
          onChange={(e) =>
            setAdminPageSettings({
              ...adminPageSettings,
              buyer_dashboard_page_id: parseInt(e.target.value),
            })
          }
          className="w-full min-w-[100%] block h-[50px]"
        >
          <option value="">{tr("Select Page")}</option>
          {pages.map((page) => (
            <option
              value={page.id}
              selected={page.id === adminPageSettings.buyer_dashboard_page_id}
            >
              {page.title}
            </option>
          ))}
        </select>
        <a href={url}>{url}</a>
        <p className={"!p-0 !m-0 text-gray-500"}>
          Select the page you added the{" "}
          <b>
            <code>{sk.hre_buyer_dashboard.shortcode}</code>
          </b>{" "}
          shortcode{" "}
        </p>
      </label>
    );
  }

  function renderDefaultAgreement1() {
    return (
      <div className="w-full flex flex-col gap-1">
        {/*<Typography variant="body1" className="block w-full">*/}
        {/*  {tr("Default Agreement 1")}{" "}*/}
        {/*  /!*<code className="font-medium">[buyer_dashboard_view]</code>*!/*/}
        {/*</Typography>*/}
        <div className={"pt-4"}>
          <ReactQuill theme="snow" value={agreement} onChange={setAgreement} />
        </div>
      </div>
    );
  }

  function renderDefaultAgreement2() {
    return (
      <div className="w-full flex flex-col gap-1">
        {/*<Typography variant="body1" className="block w-full">*/}
        {/*  {tr("Default Agreement 2")}{" "}*/}
        {/*  /!*<code className="font-medium">[buyer_dashboard_view]</code>*!/*/}
        {/*</Typography>*/}
        <div className={"pt-4"}>
          <ReactQuill
            theme="snow"
            value={agreement2}
            onChange={setAgreement2}
          />
        </div>
      </div>
    );
  }

  function handleSaveSettings(e) {
    e.preventDefault();
    fetchSaveAdminSettings({
      buyer_elite_access_fee: adminPageSettings.buyer_elite_access_fee,
      seller_elite_access_fee: adminPageSettings.seller_elite_access_fee,
      buyer_elite_signup_page_id: adminPageSettings.buyer_elite_signup_page_id,
      buyer_elite_login_page_id: adminPageSettings.buyer_elite_login_page_id,
      seller_elite_signup_page_id:
        adminPageSettings.seller_elite_signup_page_id,
      seller_elite_login_page_id: adminPageSettings.seller_elite_login_page_id,
      agent_login_page_id: adminPageSettings.agent_login_page_id,
      agent_signup_page_id: adminPageSettings.agent_signup_page_id,
      property_agreement_page_id: adminPageSettings.property_agreement_page_id,
      buyer_dashboard_page_id: adminPageSettings.buyer_dashboard_page_id,
      default_agreement: he.encode(adminPageSettings.default_agreement),
      default_agreement2: he.encode(adminPageSettings.default_agreement2),
      search_by_map_page_id: adminPageSettings.search_by_map_page_id,
      create_listing_page_id: adminPageSettings.create_listing_page_id,
      buyer_preference_page_id: adminPageSettings.buyer_preference_page_id,
      terms_and_conditions_page_id:
        adminPageSettings.terms_and_conditions_page_id,
      elite_membership_duration_months:
        adminPageSettings.elite_membership_duration_months,
      buyer_onboarding_process_1_page_id:
        adminPageSettings.buyer_onboarding_process_1_page_id,
      seller_onboarding_process_1_page_id:
        adminPageSettings.seller_onboarding_process_1_page_id,
      buyer_elite_page_id: adminPageSettings.buyer_elite_page_id,
      seller_elite_page_id: adminPageSettings.seller_elite_page_id,
      buyer_onboarding_process_2_page_id:
        adminPageSettings.buyer_onboarding_process_2_page_id,
      google_captcha_site_key: adminPageSettings.google_captcha_site_key,
      google_captcha_secret_key: adminPageSettings.google_captcha_secret_key,
    })
      .unwrap()
      .then((res) => {
        toast.success(tr("Saved"));
      })
      .catch((err) => {
        toast.error(restGetErrorMessage(err));
      });
  }

  function renderAgreementTabs() {
    return (
      <div>
        <Box sx={{ borderBottom: 1, borderColor: "divider" }}>
          <Tabs
            value={currentTab}
            onChange={handleTabChange}
            aria-label="basic tabs example"
          >
            <Tab value={1} label="Agreement 1" />
            <Tab value={2} label="Agreement 2" />
          </Tabs>
        </Box>
        {currentTab === 1 && <div>{renderDefaultAgreement1()}</div>}
        {currentTab === 2 && <div>{renderDefaultAgreement2()}</div>}
      </div>
    );
  }

  function renderShortcodeLists() {
    return (
      <div>
        <TableContainer component={Paper}>
          <Table sx={{ minWidth: 650 }} size="small" aria-label="a dense table">
            <TableHead>
              <TableRow>
                <TableCell className={"font-semibold"}>
                  {tr("Shortcode")}
                </TableCell>
                <TableCell className={"font-semibold"}>
                  {tr("Description")}
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {Object.values(getShortcodes()).map((row) => (
                <TableRow
                  key={row.shortcode}
                  sx={{ "&:last-child td, &:last-child th": { border: 0 } }}
                >
                  <TableCell component="th" scope="row">
                    {row.shortcode}
                  </TableCell>
                  <TableCell>{row.desc}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </TableContainer>
      </div>
    );
  }
}

function getShortcodes() {
  return {
    hre_buyer_elite_signup: {
      shortcode: "[hre_buyer_elite_signup]",
      desc: tr("Buyer Elite Signup Form"),
    },
    hre_buyer_elite_login: {
      shortcode: "[hre_buyer_elite_login]",
      desc: tr("Buyer Elite Login Form"),
    },
    hre_seller_elite_signup: {
      shortcode: "[hre_seller_elite_signup]",
      desc: tr("Seller Elite Signup Form"),
    },
    hre_seller_elite_login: {
      shortcode: "[hre_seller_elite_login]",
      desc: tr("Seller Elite Login Form"),
    },
    hre_agent_signup: {
      shortcode: "[hre_agent_signup]",
      desc: tr("Agent Signup"),
    },
    hre_agent_login: {
      shortcode: "[hre_agent_login]",
      desc: tr("Agent Login"),
    },
    hre_buyer_preference: {
      shortcode: "[hre_buyer_preference]",
      desc: tr("Buyer Preference Form"),
    },
    hre_buyer_dashboard: {
      shortcode: "[hre_buyer_dashboard]",
      desc: tr("Buyer Dashboard"),
    },
    hre_property_agreement: {
      shortcode: "[hre_property_agreement]",
      desc: tr("Property Agreement"),
    },
    buyer_onboarding_1: {
      shortcode: "[buyer_onboarding_1]",
      desc: tr("Buyer Onboarding Process 1 Start page"),
    },
    seller_onboarding_1: {
      shortcode: "[seller_onboarding_1]",
      desc: tr("Seller Onboarding Process Start page"),
    },
    buyer_onboarding_2: {
      shortcode: "[buyer_onboarding_2]",
      desc: tr("Buyer Onboarding Process 2 Start page"),
    },
    inquiry: {
      shortcode: "[hre_inquiry]",
      desc: tr("Inquiry Form"),
    },
  };
}
