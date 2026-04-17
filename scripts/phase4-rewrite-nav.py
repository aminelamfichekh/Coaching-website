"""Phase 4: Rewrite internal page links to local .html filenames.

Leaves asset URLs alone (Phase 3 handled those).
Leaves external URLs absolute (subdomains, feeds, wp-json, etc.).
Unmapped internal pages stay absolute and are reported at the end — the
user sees them in the summary and decides if they want to fetch more.

Run from repo root: python scripts/phase4-rewrite-nav.py
"""
from __future__ import annotations
import re
import pathlib
import sys

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "static-site"

HOST = "https://grit.ancorathemes.com"

# Mapping from WP URL path → local .html filename.
# Trailing slash optional in match (handled by regex below).
SLUG_MAP = {
    "":                                                       "index.html",   # homepage
    "business-coach":                                         "index.html",
    "our-services":                                           "our-services.html",
    "services":                                               "services.html",
    "appointment":                                            "appointment.html",
    "blog":                                                   "blog.html",
    "shop":                                                   "shop.html",
    "contact":                                                "contact.html",
    "category/media":                                         "blog-category-media.html",
    # Blog posts
    "sharing-startup-ideas-is-a-healthy-strategy":            "blog-sharing-startup-ideas-is-a-healthy-strategy.html",
    "trending-courses-on-online-webinar-charts":              "blog-trending-courses-on-online-webinar-charts.html",
    "why-is-having-an-online-coach-training-worth-it":        "blog-why-is-having-an-online-coach-training-worth-it.html",
    "top-5-reasons-to-attend-a-business-coachs-seminar":      "blog-top-5-reasons-to-attend-a-business-coachs-seminar.html",
    "how-leader-can-encourage-the-team-to-work-efficiently":  "blog-how-leader-can-encourage-the-team-to-work-efficiently.html",
    "if-your-mind-needs-a-vacation-so-does-your-body":        "blog-if-your-mind-needs-a-vacation-so-does-your-body.html",
    "must-have-motivational-books-for-a-businessman":         "blog-must-have-motivational-books-for-a-businessman.html",
    "whats-your-role-model-implement-it-in-business":         "blog-whats-your-role-model-implement-it-in-business.html",
    # Products
    "product/being-a-coach":                                  "shop-being-a-coach.html",
    "product/how-to-prosper":                                 "shop-how-to-prosper.html",
    "product/improving-income":                               "shop-improving-income.html",
    "product/lifechanging-stories":                           "shop-lifechanging-stories.html",
    "product/strategy-of-success":                            "shop-strategy-of-success.html",
    "product/the-best-team":                                  "shop-the-best-team.html",
    "product/the-management":                                 "shop-the-management.html",
    "product/the-startup":                                    "shop-the-startup.html",
    # Service detail pages
    "services/motivation":                                    "services-motivation.html",
    "services/leadership":                                    "services-leadership.html",
    "services/communication":                                 "services-communication.html",
    "services/teamwork":                                      "services-teamwork.html",
    "services/effective-speaking":                            "services-effective-speaking.html",
    "services/relevant-goals":                                "services-relevant-goals.html",
    "services/helpful-beliefs":                               "services-helpful-beliefs.html",
    "services/personal-growth":                               "services-personal-growth.html",
    # Additional blog posts
    "a-great-opportunity-to-re-charge-and-re-consider":       "blog-a-great-opportunity-to-re-charge-and-re-consider.html",
    "nothing-is-worth-your-effort-if-youre-not-ready":        "blog-nothing-is-worth-your-effort-if-youre-not-ready.html",
    "our-favorite-business-podcasts-in-2022":                 "blog-our-favorite-business-podcasts-in-2022.html",
    "what-we-like-about-teamwork-at-a-coworking":             "blog-what-we-like-about-teamwork-at-a-coworking.html",
    "quote-post":                                             "blog-quote-post.html",
    "a-few-positive-ways-to-look-at-routine":                 "blog-a-few-positive-ways-to-look-at-routine.html",
    "keeping-yourself-busy-with-online-courses":              "blog-keeping-yourself-busy-with-online-courses.html",
}

# WP archive / pagination / dead paths — neutralize to href="#".
# We match a whole href='...'  or href="..."  attribute and replace it.
ARCHIVE_SLUG_PREFIXES = (
    "author/",
    "category/",
    "tag/",
    "product-category/",
    "product-tag/",
    "services_group/",   # service group archives we never fetched
    "shop/page/",        # pagination
    "cart/",             # woocommerce cart — non-functional static
)

ARCHIVE_RE = re.compile(
    rf"""href=(['"]){re.escape(HOST)}/(?P<slug>(?:{"|".join(re.escape(p) for p in ARCHIVE_SLUG_PREFIXES)})[^'"#?]*)(?:[?#][^'"]*)?\1""",
    re.IGNORECASE,
)

# Dropped demo subdomains (other variants — out of scope)
SUBDOMAIN_RE = re.compile(
    r"""href=(['"])https://(?:public-speaker|life-coach|yoga-instructor|nutrition-coach|personal-trainer)\.grit\.ancorathemes\.com/?\1""",
    re.IGNORECASE,
)

# Strip canonical + og:url meta tags that point to live demo
STRIP_META_RE = [
    re.compile(r"""<link[^>]+rel=['"]canonical['"][^>]*grit\.ancorathemes\.com[^>]*/?>\s*""", re.IGNORECASE),
    re.compile(r"""<meta[^>]+property=['"]og:url['"][^>]*grit\.ancorathemes\.com[^>]*/?>\s*""", re.IGNORECASE),
    re.compile(r"""<meta[^>]+name=['"]twitter:url['"][^>]*grit\.ancorathemes\.com[^>]*/?>\s*""", re.IGNORECASE),
]

# Rewrite escaped URLs inside inline JS (TRX_ADDONS_STORAGE, etc.)
# so runtime-composed asset paths stay local.
ESC_INLINE_SUBS = [
    (re.compile(r"https:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/themes\\/grit\\/"),
     r"assets\/theme\/"),
    (re.compile(r"https:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/plugins\\/"),
     r"assets\/plugins\/"),
    (re.compile(r"https:\\/\\/grit\.ancorathemes\.com\\/wp-content\\/uploads"),
     r"assets\/images\/uploads"),
    (re.compile(r"https:\\/\\/grit\.ancorathemes\.com\\/wp-includes\\/"),
     r"assets\/vendor\/wp-includes\/"),
]

# Build one regex that captures host + path + optional fragment, e.g.
#   href="https://grit.ancorathemes.com/blog/#top"
# and we rewrite the path portion using SLUG_MAP.
LINK_RE = re.compile(
    rf"""(?P<prefix>href=['"]){re.escape(HOST)}(?:/(?P<path>[^"'#?]*?)/?)?(?P<suffix>[?#][^'"]*)?(?P<end>['"])"""
)


def rewrite(match: re.Match) -> str:
    path = match.group("path") or ""  # bare host ("https://grit.ancorathemes.com") → path=""
    suffix = match.group("suffix") or ""
    if path in SLUG_MAP:
        return f"{match.group('prefix')}{SLUG_MAP[path]}{suffix}{match.group('end')}"
    # Not in map — leave absolute so it still works when clicked online.
    return match.group(0)


def process(path: pathlib.Path):
    html = path.read_text(encoding="utf-8", errors="replace")
    orig = html

    # 1. Rewrite known page URLs to local .html
    html, n_nav = LINK_RE.subn(rewrite, html)

    # 2. Neutralize WP archive pages
    html, n_arch = ARCHIVE_RE.subn(
        lambda m: f'href="#" data-todo="archive-page" data-orig="{m.group("slug")}"',
        html,
    )

    # 3. Neutralize dropped-demo subdomains
    html, n_sub = SUBDOMAIN_RE.subn(
        'href="#" data-todo="demo-variant"', html
    )

    # 4. Strip canonical / og:url / twitter:url tags pointing at live site
    n_meta = 0
    for pat in STRIP_META_RE:
        html, k = pat.subn("", html)
        n_meta += k

    # 5. Rewrite escaped URLs inside inline JS (TRX_ADDONS_STORAGE etc.)
    n_esc = 0
    for pat, repl in ESC_INLINE_SUBS:
        html, k = pat.subn(repl, html)
        n_esc += k

    # 6. Neutralize <form action="..."> that posts to the live site
    html, n_form = re.subn(
        rf"""action=(['"]){re.escape(HOST)}/[^'"]*\1""",
        'action="#" data-todo="form-submit"', html,
    )
    # Track under 'archive' to keep the report tidy
    n_arch += n_form

    if html != orig:
        path.write_text(html, encoding="utf-8", newline="")
    return {"nav": n_nav, "archive": n_arch, "subdomain": n_sub, "meta": n_meta, "esc": n_esc}


def main():
    pages = sorted(SITE.glob("*.html"))
    header = f"{'file':<65} {'nav':>5} {'arch':>5} {'sub':>5} {'meta':>5} {'esc':>5}"
    print(header)
    print("-" * len(header))
    totals = {"nav": 0, "archive": 0, "subdomain": 0, "meta": 0, "esc": 0}
    for p in pages:
        r = process(p)
        print(f"{p.name:<65} {r['nav']:>5} {r['archive']:>5} {r['subdomain']:>5} {r['meta']:>5} {r['esc']:>5}")
        for k in totals:
            totals[k] += r[k]
    print("-" * len(header))
    print(f"{'TOTAL':<65} {totals['nav']:>5} {totals['archive']:>5} {totals['subdomain']:>5} {totals['meta']:>5} {totals['esc']:>5}")

    # Report unmapped internal URLs so the user sees what's still absolute.
    leftover: dict[str, int] = {}
    for p in pages:
        html = p.read_text(encoding="utf-8", errors="replace")
        for m in re.finditer(rf"""href=['"]{re.escape(HOST)}/([^"'#?]+?)/?(?=['"#?])""", html):
            slug = m.group(1)
            if slug not in SLUG_MAP:
                leftover[slug] = leftover.get(slug, 0) + 1
    if leftover:
        print("\nUnmapped internal URLs (kept absolute — will still resolve online):")
        for slug, n in sorted(leftover.items(), key=lambda x: -x[1]):
            print(f"  {n:>4}x  /{slug}/")


if __name__ == "__main__":
    main()
