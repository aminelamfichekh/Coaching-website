"""Remove the 'Shop' entry from the main nav (desktop + sticky + mobile)
across every docs/*.html.

Targets the exact <li>...<a href="shop.html"><span>Shop</span></a></li>
pattern that WP Menus emit.  Footer/sidebar copies with the same pattern
are also removed — user asked to remove Shop from every top-of-page nav.
"""
import re
import pathlib

SITE = pathlib.Path(__file__).resolve().parent.parent / "docs"

SHOP_LI = re.compile(
    r'<li[^>]*>\s*<a[^>]*href=["\']shop\.html["\'][^>]*>\s*<span>Shop</span>\s*</a>\s*</li>',
    re.IGNORECASE,
)

total = 0
for p in sorted(SITE.glob("*.html")):
    html = p.read_text(encoding="utf-8", errors="replace")
    new, n = SHOP_LI.subn("", html)
    if n:
        p.write_text(new, encoding="utf-8", newline="")
    print(f"{p.name:<62} removed {n} <li>")
    total += n
print(f"TOTAL: {total} 'Shop' menu items removed")
