=== Instant Countdown Banner ===
Contributors: wpezo
Donate link: https://www.wpezo.com
Tags: countdown, banner, sticky bar, top bar, promotion, urgency, sale, marketing, ecommerce
Requires at least: 5.8
Tested up to: 6.6
Stable tag: 1.3.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight countdown banner for promotions and deadlines — with live preview, color picker, URL targeting, and a copy-shortcode button.

== Description ==
Create urgency with a clean **countdown banner** that fits any theme. Place it at the top or bottom, make it sticky, customize colors and the CTA, and preview everything live before saving.

**Features**
* Top / Bottom position, optional **sticky**
* Custom messages **before/after** the deadline (`{time}` placeholder)
* Colors via **WP Color Picker**
* Optional **CTA** button (label + URL)
* **Dismiss** with cookie (N days)
* Hide for logged-in users
* **Live Preview** in admin (update in real time before saving)
* **URL targeting** (include/exclude with wildcards `*`; full URLs or paths)
* **Body offset toggle** for themes that already handle padding
* One-click **Copy shortcode**
* Shortcode: `[instant_countdown_banner]`
* Fully internationalized (i18n-ready)

**Usage**
- Set the deadline using your **site timezone** (Settings → General).
- Use `{time}` in the “Message before deadline”, e.g. `Ends in {time}`.
- Target URLs: one pattern per line. Examples:


== Installation ==
1. Upload and activate the plugin.
2. Go to **Settings → Instant Countdown Banner**.
3. Configure deadline, messages, colors, CTA, and targeting. Use the **live preview**.
4. Save.

== Frequently Asked Questions ==
= The banner does not appear =
Check that it’s **enabled**, the **deadline** is in the future (or enable “Show after deadline”), and the dismiss cookie isn’t active (try an incognito window). Also verify **URL targeting**.

= The time looks off =
The datetime uses the **site timezone** (Settings → General).

= Can I show the banner only on specific pages? =
Yes. Choose **Include** mode under **Targeting** and add one pattern per line. Wildcards `*` are supported. Full URLs or paths are both valid.

= My theme already adjusts spacing when sticky =
Disable **“Add body offset when sticky”**.

= Does it work with caching? =
Yes. The countdown runs client-side. If you change settings, purge caches/CDN.

== Screenshots ==
1. Settings page with color picker and live preview.
2. Sticky top banner example.
3. Bottom banner with CTA.

== Changelog ==
= 1.3.1 =
* Dev: add ESLint/Stylelint configs aligned with WordPress standards; pin Stylelint 14 for compatibility. No runtime changes.

= 1.3.0 =
* Dev: introduce JS/CSS lint scripts and configs. No runtime changes.

= 1.2.4 =
* PHPCS/WPCS: final cleanups for coding standards compliance.

= 1.2.3 =
* PHPCS/WPCS: remove “commented-out code” warning and ensure trailing newline.

= 1.2.2 =
* PHPCS/WPCS fixes; add Composer scripts (`lint`, `fix`).

= 1.2.1 =
* Add `.pot` translations file and `ruleset.xml` (WordPress Coding Standards).

= 1.2.0 =
* **URL targeting** (include/exclude with wildcards), **body offset** toggle, **Copy shortcode** button, uninstall cleanup, full English/i18n strings.

= 1.1.0 =
* Rebrand to **Instant Countdown Banner** (by **WPezo**), add **WP Color Picker** and **Live Preview**, minified assets.

= 1.0.0 =
* Initial release (Countdown Banner).

== Upgrade Notice ==
= 1.3.1 =
Tooling update (linting configs). Safe to update; no runtime changes.
