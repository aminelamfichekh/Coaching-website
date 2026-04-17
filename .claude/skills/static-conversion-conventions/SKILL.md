---
name: static-conversion-conventions
description: Project-specific conventions for the Grit Business Coach WordPress-to-static conversion — folder layout, page-naming rules from WP slugs, dynamic-feature policy (booking calendar, WooCommerce cart, contact form, search, wishlist, mailchimp), and the "write a new file over patching" edit style. Triggers at the start of any session on this project, when creating new pages, when deciding how to handle a WP-AJAX widget, or when unsure where something belongs.
---

# static-conversion-conventions

## Folder layout

```
/static-site/
  index.html                  ← /business-coach/
  our-services.html           ← /our-services/
  services-<slug>.html        ← /services/<slug>/
  appointment.html            ← /appointment/
  blog.html                   ← /blog/
  blog-<post-slug>.html       ← /<single-post-slug>/
  shop.html                   ← /shop/
  shop-<product-slug>.html    ← /product/<product-slug>/
  contact.html                ← /contact/
  /assets/
    /theme/                   ← from wp-content/themes/grit/
      /css/  /js/  /images/  /fonts/
    /plugins/
      /<plugin>/              ← one folder per WP plugin used
        /css/  /js/  /images/
    /images/uploads/YYYY/MM/  ← preserved from wp-content/uploads/
    /vendor/wp-includes/      ← jQuery, jQuery UI, MediaElement
  NOTES.md                    ← list of dynamic TODOs
  README.md                   ← how to run locally
/_original_backup/            ← (gitignored) full copy of the original theme
```

## Naming rules

- WP URL path → kebab-case `.html`, trailing slash dropped.
- Nested paths flatten with a dash: `/services/coaching/` → `services-coaching.html`.
- `/business-coach/` is the home page → `index.html`.
- Never create subdirectories for pages. `/assets/` is the ONLY place
  subdirectories live.

## Dynamic-feature policy

The live demo has several features that rely on `admin-ajax.php` or
WooCommerce backend. They **cannot** go fully static.

| Feature                  | Policy                                                                                                   |
| ------------------------ | -------------------------------------------------------------------------------------------------------- |
| Booked calendar          | Keep the rendered HTML + CSS. Strip the JS endpoint nonces. Flag in NOTES.md. Never fake the endpoint.    |
| WooCommerce shop / cart  | Keep page layout. Cart buttons become no-ops. Flag in NOTES.md.                                          |
| Contact Form 7           | Keep the form markup. Submission will fail silently. Flag in NOTES.md with clear TODO.                    |
| MailChimp subscribe      | Same as CF7.                                                                                             |
| WP search                | Remove search handler, keep the icon. Flag in NOTES.md.                                                  |
| Wishlist (TI WC)         | Keep UI, buttons become no-ops. Flag in NOTES.md.                                                        |
| Revolution Slider        | Fully static — works with the inline JSON block + rs6 assets. Should run identically.                    |
| Elementor frontend       | Fully static — no backend calls after render.                                                            |
| Superfish menu           | Fully static.                                                                                            |
| Google Fonts / FA        | Stay on CDN (see asset-path-rewriter).                                                                   |
| Instagram feed (SB)      | Needs an API token. Strip the dynamic call; keep the static placeholder markup. Flag in NOTES.md.        |
| Vimeo/YouTube iframes    | Keep the iframe verbatim.                                                                                |
| Google Map               | If it's an iframe embed, keep it. If it's a JS widget requiring an API key, flag and comment out.        |

**Never fake an endpoint.** If a feature is not achievable statically,
document it and move on.

## Editing style

- **Write new files rather than patching in place.** When regenerating a
  stripped/rewritten page, produce the new version as a complete file —
  do not do dozens of tiny `Edit` ops on the old one. Patching a page
  with interleaved strip+rewrite passes is where we introduce subtle
  animation breakage.
- For CSS and JS copied from the theme, never reformat, lint, or minify.
- For *hand-written* files (README, NOTES, new navigation snippets),
  normal formatting is fine.

## Baseline commit discipline

- Commit after each Part B phase finishes, not mid-phase.
- Commit message format: `Phase <N>: <what-happened>` — e.g.
  `Phase 2: Extract Business Coach pages and assets`.
- Do not commit `/_original_backup/` (it is in `.gitignore`).

## Deliverables at end of Part B

1. `/static-site/` runnable via `python -m http.server 8000`.
2. `NOTES.md` enumerating every non-static feature with a clear TODO.
3. `README.md` with one-line local-run instructions.
4. Pixel-for-pixel match with
   `https://grit.ancorathemes.com/business-coach/` at 1920×1080 and
   375×812 viewports on key pages.

## When NOT to trigger

- Projects that are not this specific Grit conversion.
- Generic static-site advice — this skill is project-specific rules only.
