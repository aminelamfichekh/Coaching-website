"""Phase 3: Strip WP-specific markup and rewrite asset URLs to relative paths.

Runs over every static-site/*.html. Never touches:
- data-* attributes
- inline script blocks (TRX_ADDONS_STORAGE, setREVStartSize/RS_MODULES, Elementor)
- <style id='elementor-frontend-inline-css'>
- Script order and defer attributes
- Class names and markup structure

Does NOT yet rewrite internal page links (e.g. /our-services/ → our-services.html).
That is Phase 4.

Run from the repo root:  python scripts/phase3-strip-and-rewrite.py
"""
from __future__ import annotations
import re
import pathlib
import sys

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "static-site"

# ------------------------------------------------------------------
# 1. STRIP — regex patterns for elements to remove entirely
# ------------------------------------------------------------------
# Each pattern matches a single line or block and we delete the whole match.
# DOTALL flag so `.` matches newlines inside <script>...</script> blocks.

STRIP_PATTERNS = [
    # Cloudflare email-decode injection (appears multiple times)
    r'<script[^>]*data-cfasync="false"[^>]*cloudflare-static/email-decode[^>]*></script>\s*',

    # WP emoji — settings JSON, loader script, loader JS, inline styles, release script
    r"<script[^>]*id=['\"]wp-emoji-settings['\"][^>]*>[^<]*</script>\s*",
    r"<script[^>]*id=['\"]wp-emoji-release-js['\"][^>]*></script>\s*",
    r"<script[^>]*>(?:(?!</script>).)*?_wpemojiSettings(?:(?!</script>).)*?</script>\s*",
    r"<script[^>]*>(?:(?!</script>).)*?wp-emoji-settings(?:(?!</script>).)*?</script>\s*",
    r"<script[^>]*>\s*!function\(e,a,t\)\{.*?wp-emoji.*?</script>\s*",
    r"<style[^>]*id=['\"]wp-emoji-styles-inline-css['\"][^>]*>.*?</style>\s*",

    # oEmbed / RSD / REST / feeds / pingback
    r"<link[^>]*rel=['\"]alternate['\"][^>]*type=['\"]application/rss\+xml['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]alternate['\"][^>]*type=['\"]application/json\+oembed['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]alternate['\"][^>]*type=['\"]text/xml\+oembed['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]alternate['\"][^>]*type=['\"]application/json['\"][^>]*wp-json[^>]*/?>\s*",
    r"<link[^>]*rel=['\"]https://api\.w\.org/['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]EditURI['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]pingback['\"][^>]*/?>\s*",
    r"<link[^>]*rel=['\"]https://api\.w\.org/['\"][^>]*/?>\s*",

    # wp-embed
    r"<script[^>]*id=['\"]wp-embed-js['\"][^>]*></script>\s*",

    # WPML / hreflang — strip (single-language static site)
    r"<link[^>]*rel=['\"]alternate['\"][^>]*hreflang=['\"][^'\"]+['\"][^>]*/?>\s*",

    # robots noindex meta (WP admin-only preview flag)
    r"<meta[^>]+name=['\"]robots['\"][^>]*content=['\"]noindex[^'\"]*['\"][^>]*/?>\s*",

    # Generator metas
    r"<meta[^>]+name=['\"]generator['\"][^>]*/?>\s*",

    # Admin bar
    r"<link[^>]*id=['\"]admin-bar-css['\"][^>]*/?>\s*",
    r"<link[^>]*id=['\"]dashicons-css['\"][^>]*/?>\s*",
    r"<style[^>]*id=['\"]admin-bar-inline-css['\"][^>]*>.*?</style>\s*",
    r"<div[^>]*id=['\"]wpadminbar['\"][^>]*>.*?</div>\s*",

    # WP-AJAX nonce inline vars — these endpoints won't exist on static
    # (keep related CSS; only strip the *-js-extra var blobs that carry the nonce)
    r"<script[^>]*id=['\"]booked-functions-js-extra['\"][^>]*>.*?</script>\s*",
    r"<script[^>]*id=['\"]booked-fea-js-js-extra['\"][^>]*>.*?</script>\s*",
    r"<script[^>]*id=['\"]booked-wc-fe-functions-js-extra['\"][^>]*>.*?</script>\s*",
    r"<script[^>]*id=['\"]quickcal-functions-js-extra['\"][^>]*>.*?</script>\s*",
    r"<script[^>]*id=['\"]quickcal-fea-js-js-extra['\"][^>]*>.*?</script>\s*",

    # trx_demo panel (floating "buy/demo" switcher on Ancora's live site)
    r"<script[^>]*id=['\"]trx_demo_panels-js(?:-extra)?['\"][^>]*>.*?</script>\s*",
    r"<link[^>]*id=['\"]trx_demo_(?:panels|icons|icons_animation)-css['\"][^>]*/?>\s*",
    # TRX_DEMO_STORAGE holds the entire sales-panel HTML (buy theme, bestsellers, hide)
    # in a <script> block that gets injected into the DOM at runtime. Strip whole block.
    r"<script[^>]*>(?:(?!</script>).)*?TRX_DEMO_STORAGE(?:(?!</script>).)*?</script>\s*",
    # trx_demo bootstrap div if present
    r"<div[^>]*class=['\"][^'\"]*trx_demo_panels?[^'\"]*['\"][^>]*>.*?</div>\s*",
]

# Compile once
STRIP_RE = [re.compile(p, re.IGNORECASE | re.DOTALL) for p in STRIP_PATTERNS]

# ------------------------------------------------------------------
# 2. URL rewriting — substitute absolute WP URLs with relative paths
# ------------------------------------------------------------------
# Order matters: longest prefix first.
# We match both https://grit.ancorathemes.com/... and //grit.ancorathemes.com/...
HOST_ALT = r"(?:https?:)?//grit\.ancorathemes\.com"

URL_SUBS = [
    (re.compile(rf"{HOST_ALT}/wp-content/themes/grit/"), "assets/theme/"),
    (re.compile(rf"{HOST_ALT}/wp-content/plugins/"), "assets/plugins/"),
    (re.compile(rf"{HOST_ALT}/wp-content/uploads/"), "assets/images/uploads/"),
    (re.compile(rf"{HOST_ALT}/wp-includes/"), "assets/vendor/wp-includes/"),
]

# Strip WP cache-buster query strings: ?ver=..., ?_=N.
# Leave other query params (e.g. YouTube ?autoplay=1) untouched.
VER_RE = re.compile(r"\?(?:ver|_)=[^'\"\s&<>)]+")

# Some inline JS config objects carry full URLs; rewrite those too,
# including escaped forms like \/wp-content\/
ESC_URL_SUBS = [
    (re.compile(rf"https?:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/themes\\/grit\\/"),
     r"assets\/theme\/"),
    (re.compile(rf"https?:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/plugins\\/"),
     r"assets\/plugins\/"),
    (re.compile(rf"https?:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/uploads\\/"),
     r"assets\/images\/uploads\/"),
    (re.compile(rf"https?:\\/\\/grit\.ancorathemes\.com\\/wp-includes\\/"),
     r"assets\/vendor\/wp-includes\/"),
]

# ------------------------------------------------------------------
# Workflow
# ------------------------------------------------------------------
def process(path: pathlib.Path) -> dict:
    html = path.read_text(encoding="utf-8", errors="replace")
    original_len = len(html)

    strips = 0
    for pat in STRIP_RE:
        html, n = pat.subn("", html)
        strips += n

    url_rewrites = 0
    for pat, repl in URL_SUBS:
        html, n = pat.subn(repl, html)
        url_rewrites += n

    for pat, repl in ESC_URL_SUBS:
        html, n = pat.subn(repl, html)
        url_rewrites += n

    vers = len(VER_RE.findall(html))
    html = VER_RE.sub("", html)

    path.write_text(html, encoding="utf-8", newline="")

    return {
        "file": path.name,
        "size_before": original_len,
        "size_after": len(html),
        "strips": strips,
        "url_rewrites": url_rewrites,
        "ver_stripped": vers,
    }


def main():
    pages = sorted(SITE.glob("*.html"))
    if not pages:
        print("No HTML found in static-site/", file=sys.stderr)
        sys.exit(1)

    print(f"{'file':<62} {'strips':>7} {'urls':>7} {'ver=':>6} {'size delta':>12}")
    print("-" * 100)

    total = {"strips": 0, "url_rewrites": 0, "ver_stripped": 0}
    for p in pages:
        r = process(p)
        delta = r["size_after"] - r["size_before"]
        print(f"{r['file']:<62} {r['strips']:>7} {r['url_rewrites']:>7} "
              f"{r['ver_stripped']:>6} {delta:>+12}")
        total["strips"] += r["strips"]
        total["url_rewrites"] += r["url_rewrites"]
        total["ver_stripped"] += r["ver_stripped"]

    print("-" * 100)
    print(f"{'TOTAL':<62} {total['strips']:>7} {total['url_rewrites']:>7} "
          f"{total['ver_stripped']:>6}")


if __name__ == "__main__":
    main()
