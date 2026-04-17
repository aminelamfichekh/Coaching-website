---
name: wp-to-static-extractor
description: Download the server-rendered HTML and referenced assets from the live Business Coach demo at grit.ancorathemes.com for the static conversion. Triggers when the user says "fetch the page", "extract the live demo", "mirror that asset", "download business-coach", "grab the HTML from ancorathemes", or similar. Encodes the Cloudflare workaround (a browser User-Agent is required — plain curl is blocked), the page-slug-to-filename mapping, the asset-mirroring rules, and a verification checklist so we do not silently save a Cloudflare challenge page as content.
---

# wp-to-static-extractor

## Target

- Base URL: `https://grit.ancorathemes.com/`
- Only the **Business Coach** demo is in scope. Ignore links to subdomains
  `public-speaker.grit.ancorathemes.com`, `life-coach.*`, `yoga-instructor.*`,
  `nutrition-coach.*`, `personal-trainer.*`.

## Cloudflare workaround (CRITICAL — verified 2026-04-17)

The server sits behind Cloudflare. A plain `curl https://grit.ancorathemes.com/business-coach/`
returns HTTP 200 with a challenge page titled `Attention Required! | Cloudflare`
(about 4.5 KB, ~89 lines, contains `/cdn-cgi/styles/cf.errors.css`). The real
page is ~2375 lines.

**Exact working command:**

```bash
curl -s -L \
  -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36" \
  -H "Accept: text/html,application/xhtml+xml" \
  -H "Accept-Language: en-US,en;q=0.9" \
  "https://grit.ancorathemes.com/<slug>/" \
  -o "static-site/<slug>.html"
```

Flags matter: `-s` silent, `-L` follow redirects, `-A` sets User-Agent.
The two `-H` headers (Accept + Accept-Language) round out the browser
fingerprint. Do not drop them — bare `-A` sometimes still gets challenged.

### Verification — run AFTER every download

1. `wc -l file.html` — expect 1000+ lines for real pages; sub-200 is almost
   always a block page.
2. `grep -c "Attention Required! | Cloudflare" file.html` — must be `0`.
3. `grep -c "cf-error-details" file.html` — must be `0`.
4. `grep -c "wp-content" file.html` — must be >0 for any real Grit page.

If any check fails, do not proceed. Retry once. If it still fails, escalate
with the user before saving.

### If the UA workaround breaks

Escalation order — try one at a time, not all at once:

1. Add `-H "Referer: https://grit.ancorathemes.com/"`
2. Add `-b /tmp/cf-jar.txt -c /tmp/cf-jar.txt` (cookie persistence)
3. Switch to `wget --user-agent="..."` (sometimes parses CF differently)
4. Use a headless browser (Playwright/Chromium) — last resort

## Page-slug to filename mapping

Convert WP URL path to kebab-case `.html` under `/static-site/`:

| WP URL                              | Static filename              |
| ----------------------------------- | ---------------------------- |
| `/business-coach/`                  | `index.html`                 |
| `/our-services/`                    | `our-services.html`          |
| `/services/<slug>/`                 | `services-<slug>.html`       |
| `/appointment/`                     | `appointment.html`           |
| `/blog/`                            | `blog.html`                  |
| `/<post-slug>/`                     | `blog-<post-slug>.html`      |
| `/shop/`                            | `shop.html`                  |
| `/product/<slug>/`                  | `shop-<slug>.html`           |
| `/contact/`                         | `contact.html`               |

Trailing slashes dropped. Flatten hierarchy — we are NOT creating
subdirectories for pages. Subdirectories are only for `/assets/`.

## Asset mirroring rules

Every referenced CSS/JS/image/font URL under `grit.ancorathemes.com`
needs a local copy. Run the same `curl -A ...` recipe for each asset.

- `wp-content/themes/grit/**`       → `assets/css/theme/**`, `assets/js/theme/**`, `assets/images/theme/**` (split by extension)
- `wp-content/plugins/<plugin>/**`  → `assets/css/<plugin>/**`, `assets/js/<plugin>/**`, `assets/images/<plugin>/**`
- `wp-content/uploads/**`           → `assets/images/uploads/**` (preserve the `YYYY/MM/` path under `uploads/`)
- `wp-includes/**`                  → `assets/vendor/wp-includes/**` (jQuery, jQuery UI, MediaElement live here)

**Strip `?ver=...` from the filename when saving.** The query string is
metadata for WP cache busting — the raw file is identical.

**Leave absolute (do NOT mirror):**
- `//fonts.googleapis.com/...`
- `//fonts.gstatic.com/...`
- `//use.fontawesome.com/...`
- `//unpkg.com/...`, `//cdn.jsdelivr.net/...`

## Known per-page asset oddities

- Revolution Slider injects inline `<script>` JSON *and* references
  `//grit.ancorathemes.com/wp-content/plugins/revslider/sr6/assets/...`
  (note the leading `//`). Preserve the protocol-relative form when
  rewriting — or convert to relative.
- `TRX_ADDONS_STORAGE` is an inline `<script>` block, not a file. Keep it.
- `booked-*-js-extra` inline vars contain WP AJAX nonces tied to `admin-ajax.php`.
  These endpoints will 404 on the static site. Keep the markup but flag
  in `NOTES.md` that the booking flow is non-functional.

## Output

For each extraction:
- Save the HTML to `/static-site/<filename>.html`.
- Download every first-party asset referenced by that page (dedupe across pages).
- Record in a running list: `(page, assets-pulled-count, file-size, verification-pass)`.

## When NOT to trigger

- General "scrape a website" requests that are not about this theme.
- Extraction of demo variants other than Business Coach.
- Writing new HTML from scratch (that's not extraction).
