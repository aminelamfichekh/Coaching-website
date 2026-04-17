"""Remove the 5 demo-variant <li> entries from the Home dropdown.

Keeps only 'Business Coach' (the current page). The 5 variants were
already neutralized to href="#" data-todo="demo-variant" in Phase 4,
so we target that exact marker to avoid any collateral damage.
"""
import re
import pathlib

SITE = pathlib.Path(__file__).resolve().parent.parent / "static-site"

# One <li>...<a href="#" data-todo="demo-variant"><span>...</span></a></li>
# No nested <li>, so a non-greedy match on the li body is safe.
VARIANT_LI = re.compile(
    r"""<li[^>]*>\s*<a[^>]*data-todo=["']demo-variant["'][^>]*>.*?</a>\s*</li>""",
    re.DOTALL,
)

total = 0
for p in sorted(SITE.glob("*.html")):
    html = p.read_text(encoding="utf-8", errors="replace")
    new, n = VARIANT_LI.subn("", html)
    if n:
        p.write_text(new, encoding="utf-8", newline="")
    print(f"{p.name:<62} removed {n} <li>")
    total += n
print(f"TOTAL: {total} demo-variant <li> removed")
