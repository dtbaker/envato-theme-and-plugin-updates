# WordPress Theme and Plugin updates on the Envato Market

Some ideas on theme and plugin updates for the Envato market.

Everything here would work without any new special dev work from Envato. There are better options that would require Envato dev work but that is a discussion for another day. 

Feel free to submit an <a href="https://github.com/dtbaker/envato-theme-and-plugin-updates/issues">issue</a> with any new ideas/feedback.

The goal of this document is to find a nice "standard" which could be applied to all WordPress Themes and Plugins on the Envato Market. This would bring a more consistent installation and updating experience to all Market items. 


### The Envato Market WordPress plugin:

 - This plugin was created by @valendesigns to replace the old Envato Toolkit plugin
 - It is in late Beta and should be ready soon - it looks awesome!
 - It runs separately to themes/plugins
 - Talks to Envato API to find theme/plugin version numbers and to download latest theme/plugin zip files
 - Currently requires every buyer to register a Personal Token on http://build.envato.com (*todo: asking buyer to register an API key sucks hard! Especially when oAuth is available to us. See oAuth improvement idea below*)
 
### How to install the Envato Market plugin:

 - Obvious choice is by using http://tgmpluginactivation.com - the plugin could be installed automatically on Theme activation. Also possible for plugins, however TGMP usage in plugins isn't really widespread and may be a hard requirement to push on CodeCanyon peeps. 
 - Better choice would be a dedicated nag script and install helper (WooCommerce does something similar). A single file is added to every Theme/Plugin zip file. This is instead of TGMP, however it could be added to all themes/plugins automatically and not conflict with TGMP. Example simple nag script is here: <a href="https://github.com/dtbaker/envato-theme-and-plugin-updates/blob/master/envato-market-helper.php">envato-market-helper.php</a> 


### The `envato.json` file:

 - This file should be in the root of every theme/plugin on the Envato market.
 - This file can be easily generated and applied to existing uploads, or generated during the item approval process if one doesn't exist (similar to including License information).
 - Provides a way to give configuration variables to the Envato Market plugin
 - Provides a way to enhance the buyers experience with the Envato Market plugin (e.g. specify an oAuth callback URL so buyer doesn't need to register a Personal Token to receive item updates).
 - Provides a way to identify a theme/plugin as an Envato item, as relying on theme/plugin slugs has issues
 - Provides a way to specify what 3rd party plugins are bundled with this theme (e.g. Visual Composer)
 
Here is a minimal `envato.json` file (this would be the "auto generated" bare bones config file):

```json
{
  "item_id": 1234,
  "name": "My Awesome Theme"
}
```

Here is a custom `envato.json` file:

```json
{
  "item_id": 1234,
  "name": "My Awesome Theme",
  "oauth_callback_url": "https://dtbaker.net/envato/market_oauth.php",
  "plugins": [
    {
      "item_id": "242431",
      "name": "Visual Composer",
      "version": false
    },
    {
      "item_id": "2751380",
      "name": "Slider Revolution",
      "version": "1.2.4"
     }
  ],
  "plugin_update_url": "https://dtbaker.net/envato/bundled_plugin_update.php"
```

 - `oauth_callback_url`: this oAuth URL allows a buyer to "Login" to activate Theme/Plugin updates (instead of registering an API Personal Token) making the experience *much* quicker.
 - `plugins[]`: this lists all Envato plugins that are bundled in this theme. An appropriate Extended License and Author Permission should be obtained as per http://themeforest.net/licenses/faq#small-element-stock-a 
 - `plugins[]  version`: this will lock a bundled plugin to a specified version. This allows theme authors to prevent a plugin from showing "Update Available" messages so they can ensure plugin compatibility and manually release the update.
 - `plugin_update_url`: if the Envato Market plugin detects an available bundled plugin update, this endpoint is used to verify the buyers purchase of the theme and redirect WordPress to the latest plugin zip file hosted on Envato. This allows the individual components of a theme (e.g. Visual Composer) to be updated without the parent theme getting updated first. This reduces quite a few problems (theme updates breaking custom changes) and frees up reviewers time (i.e. no more approving a theme just because it includes a bundled plugin update). 

 
### Envato API Changes

 - Once the Envato Market plugin gains traction, hundreds of thousands of WordPress installations will be hitting the API asking "What is the latest version of X". Please create a dedicated cached "version" store that we can hit without auth. Example: `http://version.envato.com/242431` will simply output `4.7.2` as the latest version for item number 242431. I have seen misconfigured WordPress installations flood custom "latest version" endpoints hundreds of times a minute in my own small batch of item sales. 
 - The `/buyer/purchases` endpoint should return all Purchases and Purchase Codes similar to http://codecanyon.net/downloads when using a Personal Token (I believe they're working on this right now - thanks guys!!!!!)


### Example `market_oauth.php` file:

(coming soon) - this file allows a buyer to "Login" and easily activate a Theme/Plugin without needing an API Personal Token. Not a neccessary step but improves buyer experience.

### Example `bundled_plugin_update.php` file:

(coming soon) - this handles the buyer theme purchase verification and Extended License verification of a bundled plugin update, it tells the Envato Market plugin where the latest plugin update file is hosted.
This is a controversial topic. Should bundled plugins be allowed to update automatically? Short answer: *Yes*. Long answer: *Yes*, it's no different to waiting a week for the author to update the theme and have it approved. But then the buyer has to update their ENTIRE theme just to get a new plugin update, or they have to hunt through the theme ZIP file to find the Plugin ZIP file and manually delete/install the latest plugin version. For those complaining about distributing source, the current method of bundling plugin updates generally involves downloading those plugin updates to a computer, unzipping the theme, and uploading the zipped plugin file. This *distributes* much more copies of the code than a background update script that happens on the server in a single click. Think about it. Updates only go out to verified theme buyers from verified extended license authors. It's all good.
