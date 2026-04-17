---
name: asset-path-rewriter
description: Rewrite absolute WordPress URLs (https://grit.ancorathemes.com/wp-content/..., /wp-includes/...) to relative paths under /static-site/assets/, strip ?ver=... cache-busting query strings, and leave CDN URLs absolute (Google Fonts, Font Awesome, gstatic). Triggers when the user says "make URLs relative", "rewrite asset paths", "strip ?ver", "fix asset links", or when preparing a page to load via file:// or python -m http.server.
---

# asset-path-rewriter

## Mapping table (authoritative)

Apply left-to-right to every `src=`, `href=`, `data-src=`, `data-background-image=`,
and CSS `url(...)` in saved HTML / CSS files.

| Source URL pattern                                       | Rewrite to                              |
| -------------------------------------------------------- | --------------------------------------- |
| `https://grit.ancorathemes.com/wp-content/themes/grit/`  | `assets/theme/`                         |
| `https://grit.ancorathemes.com/wp-content/plugins/<p>/`  | `assets/plugins/<p>/`                   |
| `https://grit.ancorathemes.com/wp-content/uploads/`      | `assets/images/uploads/`                |
| `https://grit.ancorathemes.com/wp-includes/`             | `assets/vendor/wp-includes/`            |
| `//grit.ancorathemes.com/wp-content/plugins/revslider/`  | `assets/plugins/revslider/`             |
| `https://grit.ancorathemes.com/<page>`  (internal link)  | handled by nav-linking (Part B Phase 4) — not this skill |

**Always also:** strip `?ver=<anything>` from the URL after rewrite.
A `?` followed by anything else (e.g. `?autoplay=1` in an embed) is kept.

## Leave absolute (do NOT rewrite, do NOT mirror locally)

External CDNs — we keep the network dependency because (a) mirroring
Google Fonts is brittle, (b) Font Awesome is multi-MB with many variants,
(c) these hosts don't block us.

- `//fonts.googleapis.com/**`
- `//fonts.gstatic.com/**`
- `//use.fontawesome.com/**`
- `//player.vimeo.com/**` (iframe `src`)
- `//www.youtube.com/**`
- `//maps.googleapis.com/**`
- `//cdn.jsdelivr.net/**`, `//unpkg.com/**` (if ever present)

## Protocol-relative URLs

RevSlider uses `//grit.ancorathemes.com/...` (no `https:`). Match both
forms — the rewrite rule must match `https://grit.ancorathemes.com/...`
AND `//grit.ancorathemes.com/...`.

## Within CSS files

Inside mirrored CSS, `url(...)` is usually already relative to the CSS
file's directory. Audit before rewriting — if the original was already
`url(../images/foo.svg)`, leave it. Only rewrite absolute WP URLs.

## Verification

After rewriting a page:

1. `grep -c "grit.ancorathemes.com" file.html` — should only match
   external iframes / intentional kept links, never `wp-content` or `wp-includes`.
2. `grep -c "?ver=" file.html` — should be `0` on internal assets.
3. Open the file with `file://` — check the Network tab in DevTools for 404s.
4. Open with `python -m http.server 8000` and visit `http://localhost:8000/` —
   cross-check.

## Edge cases

- **Background images in inline `style="background-image:url(...)"`** — rewrite
  the same way as `src=`.
- **Preload fonts** (`<link rel='preload' as='font'>`) — rewrite just like CSS.
- **SVG `<use xlink:href='...'>`** — external SVGs sometimes live in
  `plugins/<x>/images/icons.svg#<id>`. Rewrite the URL part; keep the `#id`.
- **Schema.org JSON-LD** — leave absolute URLs (they are identifiers, not
  loadable assets).

## When NOT to trigger

- Rewriting URLs inside source theme files in `/grit/grit/` — out of scope.
- Rewriting links in navigation menus (that is `static-conversion-conventions`
  + Part B Phase 4, not this skill).
