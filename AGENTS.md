# AGENTS.md — Instant Countdown Banner (WPezo)

## 30s Pitch
“Boost conversions with a clean, lightweight countdown banner. Color picker, live preview, URL targeting, and one-click shortcode — configure it in under a minute.”

## USP
- **Live preview** before saving
- **WP Color Picker** styling
- **URL targeting** (include/exclude, wildcards)
- Zero dependencies at runtime (minified assets)
- i18n-ready; safe, standards-compliant

## Audience
E‑commerce managers, marketers, affiliates, publishers, agencies.

## Pricing (marketplaces)
- $5–12 to maximize volume; bundle with other WPezo micro-plugins
- Upsell PRO: multiple banners, per-role targeting, templates, recurrence, WooCommerce hooks, JS events

## Support Macros
- **Not showing?** Enable plugin, check deadline in future or enable “Show after deadline”, clear cookie, review URL targeting.
- **Time off?** Uses **site timezone** (Settings → General).
- **Only some pages?** Use **Include** targeting, one pattern per line (wildcards `*`, full URLs/paths).
- **Theme handles spacing?** Disable “Add body offset when sticky”.

## File Structure (runtime)
```
instant-countdown-banner/
  instant-countdown-banner.php
  uninstall.php
  includes/admin-page.php
  assets/
    js/instant-countdown-banner.min.js
    css/instant-countdown-banner.min.css
    admin/js/admin.js
    admin/css/admin.css
  languages/instant-countdown-banner.pot
  readme.txt
```

## Dev Tooling
- **PHP**: PHPCS/WPCS via `ruleset.xml`
  ```powershell
  composer install
  composer exec phpcs -- --standard=ruleset.xml instant-countdown-banner
  composer exec phpcbf -- --standard=ruleset.xml instant-countdown-banner
  ```
- **JS/CSS**: ESLint + Stylelint (WordPress presets)
  ```powershell
  npm install
  npm run lint
  npm run fix
  ```

## Release Checklist
1. Bump versions in:
   - `instant-countdown-banner.php` → `Version:`
   - `readme.txt` → `Stable tag:`
2. Lint:
   - `composer exec phpcs …` (0 errors)
   - `npm run lint` (clean or fixed)
3. Build **release ZIP** with only runtime files (exclude dev tooling).
4. Test on a fresh WP + default theme, no other plugins.
5. Marketplace:
   - Main file = **clean ZIP**
   - Separate **docs/marketing** (screenshots, GIF, README for vendors)
6. WordPress.org:
   - Place screenshots/banners under `/assets/`
   - `Tested up to` current stable; update changelog

## Roadmap (PRO)
- Multiple banners & scheduling
- Advanced targeting (role, post type, taxonomy)
- Templates & icons
- WooCommerce integration
- JS events: `onExpire`, `onDismiss`

## Branding
WPezo — <https://www.wpezo.com>
