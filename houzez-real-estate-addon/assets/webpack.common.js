/**
  import { getPluginData } from './getPluginDetails';
 * External Dependencies
 */
// const { getPluginData } = require("./getPluginDetails");
const fs = require("fs");
const path = require("path");

const parentFolder = path.basename(path.dirname(__dirname)); // First-level parent folder name
const twoLevelsUp = path.join(__dirname, ".."); // Go two levels up
const mainPluginFile = path.join(twoLevelsUp, `${parentFolder}.php`);
const pluginDetails = getPluginData(mainPluginFile);
const pluginVersion = pluginDetails.Version;
const theAppVersion = "-" + pluginVersion;
// console.log({ parentFolder, twoLevelsUp, mainPluginFile, pluginDetails });

/**
 * WordPress Dependencies
 */
const defaultConfig = require("@wordpress/scripts/config/webpack.config.js");

// const globFiles = glob.sync('./src/blocks/block-test/*.ts*');
// console.log("Glob files", globFiles);
// console.log({JSON: JSON.stringify(defaultConfig)})
module.exports = {
  ...defaultConfig,
  entry: {
    ["admin/pages/admin-page-settings" + theAppVersion]:
      "/src/admin/pages/admin-page-settings",
    ["admin/admin-view-agreement-form" + theAppVersion]:
      "/src/admin/admin-view-agreement-form",
    ["admin/admin-view-inquiry" + theAppVersion]:
      "/src/admin/admin-view-inquiry",
    ["admin/meta-boxes/meta-box-agreement" + theAppVersion]:
      "/src/admin/meta-boxes/meta-box-agreement",
    ["public/sc-buyer-signup" + theAppVersion]:
      "/src/shortcodes/sc-buyer-signup",
    ["public/sc-seller-signup" + theAppVersion]:
      "/src/shortcodes/sc-seller-signup",
    ["public/sc-buyer-login" + theAppVersion]: "/src/shortcodes/sc-buyer-login",
    ["public/sc-agent-signup" + theAppVersion]:
      "/src/shortcodes/sc-agent-signup",
    ["public/sc-agent-login" + theAppVersion]: "/src/shortcodes/sc-agent-login",
    ["public/sc-seller-login" + theAppVersion]:
      "/src/shortcodes/sc-seller-login",
    ["admin/hre-elementor-submissions" + theAppVersion]:
      "/src/admin/hre-elementor-submissions",
    ["public/property-section-apply" + theAppVersion]:
      "/src/property-sections/property-section-apply",
    ["public/sc-property-agreement" + theAppVersion]:
      "/src/shortcodes/sc-property-agreement",
    ["public/sc-buyer-dashboard" + theAppVersion]:
      "/src/shortcodes/sc-buyer-dashboard",
    ["public/sc-buyer-preference" + theAppVersion]:
      "/src/shortcodes/sc-buyer-preference",
    ["public/sc-buyer-onboarding-1" + theAppVersion]:
      "/src/shortcodes/sc-buyer-onboarding-1",
    ["public/sc-buyer-onboarding-2" + theAppVersion]:
      "/src/shortcodes/sc-buyer-onboarding-2",
    ["public/sc-seller-onboarding-1" + theAppVersion]:
      "/src/shortcodes/sc-seller-onboarding-1",
    ["public/sc-inquiry" + theAppVersion]: "/src/shortcodes/sc-inquiry",
    ["public/section-checkout-elite-thankyou" + theAppVersion]:
      "/src/sections/section-checkout-elite-thankyou",
    ["cpt/users" + theAppVersion]: "/src/admin/users",
  },
  output: {
    filename: "[name].js",
    path: path.resolve(__dirname, "build"),
    clean: true,
  },
  resolve: {
    extensions: [".ts", ".tsx", ".js", ".jsx"],
    alias: {
      // "@": path.resolve(__dirname, "src"),
      "@": path.resolve(__dirname, "src"),
    },
  },
};

function getPluginData(pluginFilePath) {
  const defaultHeaders = {
    Name: "Plugin Name",
    PluginURI: "Plugin URI",
    Version: "Version",
    Description: "Description",
    Author: "Author",
    AuthorURI: "Author URI",
    TextDomain: "Text Domain",
    DomainPath: "Domain Path",
    Network: "Network",
    RequiresWP: "Requires at least",
    RequiresPHP: "Requires PHP",
    UpdateURI: "Update URI",
    _sitewide: "Site Wide Only", // Deprecated header
  };

  const pluginContent = fs.readFileSync(pluginFilePath, "utf8");
  const pluginLines = pluginContent.split("\n");

  const pluginData = {};

  pluginLines.forEach((line) => {
    const matches = line.match(/^[\s\*]*([\w]+)[\s\*]*:(.*)/);
    if (matches && matches.length >= 3) {
      const key = matches[1].trim();
      const value = matches[2].trim();
      if (defaultHeaders[key]) {
        pluginData[defaultHeaders[key]] = value;
      }
    }
  });

  // Additional processing or adjustments may be required here

  return pluginData;
}
