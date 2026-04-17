---
name: wp-markup-stripper
description: Strip WordPress-specific markup from HTML pages saved from grit.ancorathemes.com while preserving layout, animation, and theme behavior. Triggers on "strip the WP markup", "clean this HTML", "make the page static", "remove wp-admin bar", or when editing files in /static-site/*.html. Encodes the exact STRIP list (wp-emoji, oEmbed/RSD/REST links, admin bar, ?ver=, Cloudflare email-decode, booked AJAX vars) AND the KEEP list (data-* attrs, RevSlider JSON, TRX_ADDONS_STORAGE, Elementor config, booked inline CSS) so cleanup does not silently break animations or theming.
---

# wp-markup-stripper

## Purpose

Cleans WP-rendered HTML into portable static HTML without touching the
markup that drives the Grit theme's visual behavior.

**Golden rule:** if you are unsure whether to strip something, keep it and
flag in `NOTES.md`. Over-stripping breaks the demo silently; over-keeping
is visible as leftover junk and easy to fix later.

---

## STRIP — always remove

### Emoji / REST / oEmbed / RSD / feeds

```html
<script ...wp-includes/js/wp-emoji-release.min.js...></script>
<script>window._wpemojiSettings = ...;</script>
<link rel="alternate" type="application/rss+xml" ...>
<link rel="alternate" type="application/json" href=".../wp-json/..." />
<link rel="https://api.w.org/" href=".../wp-json/" />
<link rel="EditURI" href=".../xmlrpc.php?rsd" ...>
<link rel="alternate" type="application/json+oembed" ...>
<link rel="alternate" type="text/xml+oembed" ...>
<link rel="pingback" href=".../xmlrpc.php" />
```

### Admin bar and admin-only scripts

```html
<!-- anything matching: -->
#wpadminbar ... </div>
<link ... id='admin-bar-css' ... />
<link ... id='dashicons-css' ... />
<script ... id='admin-bar-js-*' ... ></script>
body.admin-bar { ... }  /* inline style rule */
```

### Cloudflare injection

```html
<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
```

Appears multiple times — remove every occurrence.

### WordPress emoji CSS

```html
<style id='wp-emoji-styles-inline-css'>...</style>
```

### Asset query strings

Strip `?ver=<anything>` from every `src=` and `href=` attribute. The file
URL without `?ver=` resolves to the same asset; `?ver=` is WP cache busting.

Keep `?` followed by actual query params (e.g. YouTube embed `?autoplay=1`).

### WP-AJAX bootstrap blocks

The `booked` inline scripts expose `admin-ajax.php` endpoints that will
404 on the static site. Strip the endpoint vars but keep any CSS/markup
blocks that influence layout — see KEEP list below for the distinction.

Strip:

```html
<script id="booked-functions-js-extra">var booked_js_vars = {...};</script>
<script id="booked-fea-js-js-extra">var booked_fea_vars = {...};</script>
<script id="booked-wc-fe-functions-js-extra">var booked_wc_variables = {...};</script>
<link rel="alternate" hreflang="x-default" .../>  <!-- WPML hreflang -->
<meta name='robots' content='noindex, nofollow' />  <!-- if present -->
```

### Page-builder edit links

```html
<link rel='https://api.w.org/...' />
<meta name="generator" content="WordPress ..." />
<meta name="generator" content="WooCommerce ..." />
<meta name="generator" content="Elementor ..." />
```

---

## KEEP — never strip (these drive the demo)

### data-* attributes

Every `data-*` on every element. Elementor, Swiper, Magnific Popup,
RevSlider, GSAP, and the Grit theme all read them at runtime.
Examples you WILL see and MUST keep:

```
data-elementor-type, data-elementor-id, data-widget_type,
data-elementor-settings, data-elementor-post-type,
data-slick, data-swiper-*, data-aos, data-parallax, data-effect,
data-gsap-*, data-tp-*, data-rsparallaxlevel
```

### Inline config blocks

```html
<!-- Grit theme's global config — MUST keep -->
<script>var TRX_ADDONS_STORAGE = {...};</script>

<!-- Elementor CSS variables block -->
<style id='elementor-frontend-inline-css'>...</style>

<!-- Revolution Slider inline JSON config -->
<script type="text/javascript">
  var htmlDivCss = '...';
  var htmlDiv = document.createElement('div'); ...
  setREVStartSize({...}); ...
  RS_MODULES.modules["rev_slider_..."] = {...};
</script>

<!-- Booked inline CSS — drives calendar colors, keep even though booking is non-functional -->
<style id='booked-css-inline-css'>...</style>
```

### Script order and defer attrs

Do **not** reorder `<script>` tags. The theme depends on this sequence:

```
jquery → jquery-migrate → jquery-ui → magnific → gsap → elementor modules
→ trx_addons/__scripts.js → trx_addons/components/*
→ themes/grit/js/__scripts.js → skins/default/skin.js
```

Keep `defer="defer"` exactly where it is. Don't add or remove it.

### Body and page-wrap classes

Classes like `body.body_style_wide`, `.page_wrap`, `.top_panel`,
`.footer_wrap` are selectors used by the theme JS (see `TRX_ADDONS_STORAGE`
keys `page_wrap_class`, `header_wrap_class`). Do not rename them.

### WooCommerce / wishlist markup

Even on a static cart page, the markup is load-bearing for layout.
Keep the classes; the JS will just fail gracefully (button clicks become
no-ops). Flag non-working cart in `NOTES.md`.

---

## Ambiguous — ask before stripping

- `<link rel='https://schemas.wp.org/...'/>` — usually safe to strip but
  flag once per session.
- `<meta property='og:*'>` — keep (good for SEO if site is ever served).
- `wpcf7` (Contact Form 7) inline vars — the form won't submit but the
  layout is styled by the classes. Keep the form markup, strip only
  the endpoint vars.
- `sitepress-multilingual-cms` (WPML) — unless we are serving multiple
  languages, strip the hreflang links but keep nothing else.

---

## Workflow

For each file in `/static-site/*.html`:

1. Read the file.
2. Strip everything in the STRIP list.
3. Double-check each rule in the KEEP list was NOT hit.
4. Write a new version of the file (don't patch in place — see
   `static-conversion-conventions`).
5. Do a visual check in the browser before committing.

## When NOT to trigger

- Generic HTML cleanup tasks unrelated to this theme.
- Minification — out of scope.
- CSS reformatting — never reformat theme CSS.
