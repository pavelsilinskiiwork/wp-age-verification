# Age Verification by Pavel Silinskii

![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-21759B?logo=wordpress&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-GPL%20v2-green)
![Version](https://img.shields.io/badge/Version-1.0.0-blue)

Age verification popup for WordPress. Shows a verification gate when a visitor enters the site (or selected pages/categories). On confirmation the choice is remembered in a cookie; on decline the page is blocked with a message or the visitor is redirected elsewhere.

---

## Table of Contents

- [Features](#features)
- [How It Works](#how-it-works)
- [Installation](#installation)
- [Configuration](#configuration)
- [Verification Modes](#verification-modes)
- [Content Scope](#content-scope)
- [Decline Action](#decline-action)
- [Settings Reference](#settings-reference)
- [AJAX Endpoint](#ajax-endpoint)
- [Security](#security)
- [Project Structure](#project-structure)
- [Data & Privacy](#data--privacy)
- [Requirements](#requirements)
- [Testing Scenarios](#testing-scenarios)
- [Changelog](#changelog)

---

## Features

- **Two verification modes** — Yes/No buttons or date of birth entry
- **Configurable minimum age** — 14, 16, 18, or 21
- **Content scope** — the entire site, or only selected pages and categories
- **Decline action** — block the page with a message, or redirect to a URL
- **Server-side age validation** — the birth date is validated and the age recalculated in PHP, not trusted from the browser
- **Remembered choice** — a single `avps_verified` cookie with configurable duration
- **Light / dark popup styles** and a custom overlay color
- **Fully translatable** — text domain `age-verification-by-pavel-silinskii`, `.pot` included
- **Clean uninstall** — the single option row is removed on delete

---

## How It Works

1. On every front-end request `AVPS_Frontend::should_show_popup()` decides whether the gate is needed (plugin enabled, no valid cookie, current URL in scope).
2. If needed, the popup markup is printed in `wp_footer` and the assets are enqueued.
3. The visitor confirms (or submits a birth date). The browser sends an AJAX request to `admin-ajax.php` (`action=avps_verify`).
4. `AVPS_Frontend::handle_verify()` checks the nonce, validates the input server-side, and on success sets the `avps_verified` cookie.
5. On the next page load the cookie is present and the popup is not shown until it expires.

---

## Installation

### Manual

1. Download or clone this repository.
2. Upload the `age-verification-by-pavel-silinskii` folder to `/wp-content/plugins/`.
3. Activate the plugin in **WordPress Admin → Plugins**.
4. Go to **Settings → Age Verification** to configure it.

### Via Git

```bash
cd wp-content/plugins
git clone https://github.com/pavelsilinskiiwork/age-verification-by-pavel-silinskii.git
```

Then activate in **WordPress Admin → Plugins**.

---

## Configuration

All options live on one settings screen at **Settings → Age Verification**, split into five sections:

| Section | Options |
|---|---|
| **General** | Enable plugin, Minimum age, Verification type, Cookie duration |
| **Content Scope** | Scope (entire site / specific), Specific pages, Specific categories |
| **Popup Text** | Popup title, Popup description, Button YES text, Button NO text |
| **Popup Style** | Style (light / dark), Overlay color |
| **Decline Action** | Action (block / redirect), Redirect URL, Blocked message |

Fields that only apply to a given choice (redirect URL, blocked message, button labels, specific pages/categories) are shown/hidden automatically. Settings are saved over AJAX.

---

## Verification Modes

### Buttons (Yes / No)

The visitor sees the title, description, and two buttons. Clicking **Yes** sets the cookie and hides the popup. Clicking **No** triggers the configured [decline action](#decline-action).

### Date of Birth

The visitor enters a date in a native date picker. On submit the server:

1. Validates the date (`checkdate`, `YYYY-MM-DD`).
2. Computes full years between that date and today (`DateTime::diff`).
3. Grants access (sets the cookie) if the age is at least the configured minimum.

If the visitor is under age, the popup shows the error, disables the date input, and replaces the button with a **Refresh page** action — the gate cannot be retried. A malformed or empty date just shows a validation error and stays editable.

There is no block/redirect in this mode — an underage visitor simply cannot pass the gate.

---

## Content Scope

| Scope | Behavior |
|---|---|
| `entire_site` | The popup is evaluated on every front-end request. |
| `specific` | The popup shows only when the current request matches one of the selected pages (`is_page()`), a selected category archive (`is_category()`), or a single post assigned to a selected category (`in_category()`). |

---

## Decline Action

Applies to the **Buttons** mode when the visitor clicks **No**.

| Action | Behavior |
|---|---|
| `block` | The popup content is replaced with the **Blocked message**. The page stays inaccessible (no cookie set). |
| `redirect` | The browser is redirected to **Redirect URL**. |

---

## Settings Reference

Stored as a single option, key `avps_settings`.

| Key | Type | Default | Description |
|---|---|---|---|
| `enabled` | bool | `true` | Master on/off switch |
| `verification_type` | string | `buttons` | `buttons` or `birthdate` |
| `minimum_age` | int | `18` | 14, 16, 18, or 21 |
| `scope` | string | `entire_site` | `entire_site` or `specific` |
| `specific_pages` | array | `[]` | Page IDs when `scope = specific` |
| `specific_categories` | array | `[]` | Category IDs when `scope = specific` |
| `decline_action` | string | `block` | `block` or `redirect` |
| `redirect_url` | string | `https://google.com` | Target for redirect on decline |
| `blocked_message` | string | *(see below)* | Text shown when blocked |
| `cookie_duration` | int | `30` | Cookie lifetime in days |
| `popup_title` | string | `Age Verification` | Popup heading |
| `popup_description` | string | *(see below)* | Popup body text |
| `button_yes_text` | string | `Yes, I'm 18+` | Confirm button label |
| `button_no_text` | string | `No, Exit` | Decline button label |
| `overlay_color` | string | `rgba(0,0,0,0.85)` | Overlay background |
| `popup_style` | string | `light` | `light` or `dark` |

**Default `blocked_message`:**

```
You must be 18 years or older to access this website.
```

**Default `popup_description`:**

```
This website contains age-restricted content. By entering, you accept our terms and confirm your age is 18 years or older.
```

---

## AJAX Endpoint

| Action | Auth | Handler |
|---|---|---|
| `avps_verify` | Public (`nopriv` + logged-in) | `AVPS_Frontend::handle_verify()` |

**Request (buttons):**

```
action=avps_verify
nonce=<avps_nonce>
confirmed=true|false
```

**Request (birthdate):**

```
action=avps_verify
nonce=<avps_nonce>
birthdate=YYYY-MM-DD
```

**Success response:**

```json
{ "success": true }
```

**Failure responses:**

```json
{ "success": false, "data": { "action": "block",    "message": "..." } }
{ "success": false, "data": { "action": "redirect", "url": "https://..." } }
{ "success": false, "data": { "action": "error",    "message": "..." } }
{ "success": false, "data": { "action": "error",    "message": "...", "lock": true } }
```

`lock: true` is returned only for the underage case in birth-date mode. The
client then disables the date field and swaps the submit button for a
**Refresh page** action, so the visitor cannot keep retrying the same gate.
A plain `error` (empty or malformed date) leaves the field editable.

---

## Security

| Measure | Where |
|---|---|
| `check_ajax_referer( 'avps_nonce' )` | `handle_verify()` |
| `current_user_can( 'manage_options' )` | `save_settings()` |
| `check_ajax_referer( 'avps_admin_nonce' )` | `save_settings()` |
| `sanitize_text_field()` / `sanitize_textarea_field()` | all text settings |
| `absint()` | `minimum_age`, `cookie_duration`, page/category IDs |
| `esc_url_raw()` | `redirect_url` |
| `wp_kses_post()` | `blocked_message` |
| `esc_html()` / `esc_attr()` / `esc_textarea()` | all template output |
| Server-side age recalculation | `handle_verify()` — the browser value is never trusted |

---

## Project Structure

```
age-verification-by-pavel-silinskii/
├── age-verification-by-pavel-silinskii.php        # Main plugin file, constants, hooks
├── readme.txt                     # WordPress.org readme
├── README.md                      # This file
├── uninstall.php                  # Removes the avps_settings option
├── includes/
│   ├── class-avps-settings.php     # Settings get/get_all/save/defaults
│   ├── class-avps-frontend.php     # Popup logic + AJAX verification
│   └── class-avps-installer.php    # Activation / deactivation
├── admin/
│   ├── class-avps-admin.php        # Settings page + AJAX save
│   └── views/
│       └── settings-page.php      # Settings screen markup
├── templates/
│   ├── popup-buttons.php          # Yes / No popup
│   └── popup-birthdate.php        # Date of birth popup
├── assets/
│   ├── css/
│   │   ├── avps-frontend.css       # Popup styles (light/dark, responsive)
│   │   └── avps-admin.css          # Settings page styles
│   └── js/
│       ├── avps-frontend.js        # Popup behavior, fetch, validation
│       └── avps-admin.js           # Conditional fields, AJAX save
└── languages/
    └── age-verification-by-pavel-silinskii.pot    # Translation template
```

---

## Data & Privacy

The plugin stores:

- **One option row** (`avps_settings`) in the database — removed on uninstall.
- **One cookie** (`avps_verified`) in the visitor's browser — `httpOnly`, `Secure` when the site is HTTPS, lifetime configurable.

No personal data (including the submitted birth date) is stored or logged.

---

## Requirements

- WordPress 5.8+
- PHP 7.4+

---

## Testing Scenarios

| # | Scenario | Expected |
|---|---|---|
| 1 | First visit | Popup shown |
| 2 | Click **Yes** | Popup hidden, cookie set |
| 3 | Second visit (cookie present) | Popup **not** shown |
| 4 | Click **No**, action = `block` | Page blocked with message |
| 5 | Click **No**, action = `redirect` | Redirected to the URL |
| 6 | Birth date, age ≥ minimum | Access granted |
| 7 | Birth date, age < minimum | Error shown, date field disabled, button becomes **Refresh page** |
| 8 | Birth date, empty / invalid | Validation error shown |
| 9 | `scope = specific`, page not listed | Popup **not** shown |
| 10 | `scope = specific`, page listed | Popup shown |
| 11 | Plugin disabled | Popup never shown |
| 12 | Mobile viewport | Popup renders correctly |

---

## Changelog

### 1.0.0

- Initial release.
- Buttons and date-of-birth verification modes.
- Site-wide or per-page / per-category scope.
- Block or redirect on decline.
- Light / dark styles, custom overlay color.
- Server-side age validation, configurable cookie.

---

## About the Developer

Built by **Pavel Silinskii** — Full-Stack PHP Developer.

- GitHub: [github.com/pavelsilinskiiwork](https://github.com/pavelsilinskiiwork)
- LinkedIn: [linkedin.com/in/pavel-silinskii](https://linkedin.com/in/pavel-silinskii)

---

## License

Licensed under the [GPL v2 or later](https://www.gnu.org/licenses/gpl-2.0.html).
