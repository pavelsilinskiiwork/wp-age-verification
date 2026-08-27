=== Age Verification by Pavel Silinskii ===
Contributors: pavelsilinskii
Tags: age verification, age gate, popup, restrict content, date of birth
Requires at least: 5.8
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Age verification popup for WordPress. Supports Yes/No and date of birth verification modes, with block or redirect on decline.

== Description ==

Age Verification by Pavel Silinskii shows a verification popup when a visitor enters your site or specific pages. When the visitor confirms their age, the choice is remembered in a cookie. When they decline, the page is blocked with a message or the visitor is redirected elsewhere.

= Features =

* Two verification modes: Yes/No buttons or date of birth entry
* Configurable minimum age (14, 16, 18, 21)
* Show on the entire site or only on selected pages and categories
* Decline action: block the page with a message, or redirect to a URL
* Configurable cookie duration
* Light and dark popup styles, custom overlay color
* Fully translatable, server-side age validation
* No data collected: only a single `avps_verified` cookie is stored

== Installation ==

1. Upload the `age-verification-by-pavel-silinskii` folder to `/wp-content/plugins/`, or install through the Plugins screen.
2. Activate the plugin through the **Plugins** menu.
3. Go to **Settings → Age Verification** to configure the popup.

== Frequently Asked Questions ==

= Does the popup block content for search engines? =

The popup is rendered client-side in the footer and content stays in the page source, so it does not hide content from crawlers.

= How is the age calculated in date of birth mode? =

The submitted date is validated on the server and the full number of years between that date and today is compared to the configured minimum age.

= What data does the plugin store? =

Only plugin settings (one option row) and a single `avps_verified` cookie in the visitor's browser. Everything is removed on uninstall.

== Screenshots ==

1. Buttons verification popup (light style).
2. Date of birth verification popup.
3. Admin settings page.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
