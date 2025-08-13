# Instant Countdown Banner (by WPezo)

Lightweight, theme-agnostic **countdown banner** for promotions and launches — with **live preview**, **color picker**, **URL targeting**, and a **copy shortcode** button.

> Plugin slug: `instant-countdown-banner`  
> Text domain: `instant-countdown-banner`

## Features
- Top/Bottom + **sticky** mode
- Messages before/after deadline with `{time}` placeholder
- Colors via **WP Color Picker**
- Optional **CTA** (label + URL)
- **Dismiss** (cookie, N days), hide for logged-in users
- **URL targeting** (include/exclude, wildcards `*`, full URLs or paths)
- **Body offset** toggle for sticky mode
- **Live Preview** (admin) + **Copy shortcode**
- i18n-ready (`languages/instant-countdown-banner.pot`)

## Quick start
1. Upload the plugin and activate it.
2. Go to **Settings → Instant Countdown Banner**.
3. Set deadline, messages, colors, CTA, targeting. Preview live. Save.

**Shortcode**
```text
[instant_countdown_banner]
```

**Targeting patterns (examples)**
```
/sale/*
/black-friday
https://example.com/deals/*
```

## Development

### PHP (WPCS/PHPCS)
```powershell
composer install
composer exec phpcs -- --standard=ruleset.xml instant-countdown-banner
composer exec phpcbf -- --standard=ruleset.xml instant-countdown-banner
```

### JS/CSS (WordPress standards)
```powershell
npm install
npm run lint
npm run fix
```

### Internationalization
- Base template: `languages/instant-countdown-banner.pot`
- Create `instant-countdown-banner-<locale>.po/mo` in `languages/`

## Release build
Distribute a **clean ZIP** containing only runtime files inside `instant-countdown-banner/`:

**Keep**
- `instant-countdown-banner.php`, `uninstall.php`
- `includes/` (admin-page.php)
- `assets/js/instant-countdown-banner.min.js`
- `assets/css/instant-countdown-banner.min.css`
- `assets/admin/js/admin.js`, `assets/admin/css/admin.css`
- `languages/instant-countdown-banner.pot`
- `readme.txt` (WordPress.org format) **inside** the plugin folder

**Exclude**
- `node_modules/`, `vendor/`
- `composer.json`, `composer.lock`, `package.json`, `package-lock.json`
- `ruleset.xml`, `.eslintrc.*`, `.eslintignore`, `stylelint.config.*`, `.stylelintignore`
- `assets/marketing/`, `README.md`, `AGENTS.md` (keep in repo, not in the release ZIP)

> Tip: keep `composer.lock` and `package-lock.json` in the **repo**, not in the release ZIP.

## Versioning
- Update both `Version:` header in `instant-countdown-banner.php` and `Stable tag:` in `readme.txt` for each release.

## Branding
Built by **WPezo** — <https://www.wpezo.com>

## License
GPLv2 or later
