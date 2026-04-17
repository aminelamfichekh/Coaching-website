"""Remove the header cart + search widgets from every static-site/*.html.

Uses a div-balanced finder, not plain regex, so nested <div>s inside each
widget are handled correctly.

Targets:
  - <div class="sc_layouts_item ... elementor-widget-trx_sc_layouts_cart" data-widget_type="trx_sc_layouts_cart.default"> ... </div>
  - <div class="sc_layouts_item ... elementor-widget-trx_sc_layouts_search" data-widget_type="trx_sc_layouts_search.default"> ... </div>
  - Any <div id="sc_layouts_cart_panel_..."> ... </div> (the slide-out cart panel)
  - Any <div id="sc_layouts_search_panel_..."> ... </div> (if a search panel exists)
"""
from __future__ import annotations
import re
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "static-site"

# Openers we want to remove with their nested content.
# Each entry is (name, regex for opening <div ...>).
OPENERS = [
    ("cart-widget",
     re.compile(r'<div[^>]*data-widget_type="trx_sc_layouts_cart\.default"[^>]*>')),
    ("search-widget",
     re.compile(r'<div[^>]*data-widget_type="trx_sc_layouts_search\.default"[^>]*>')),
    ("cart-panel",
     re.compile(r'<div[^>]*id="sc_layouts_cart_panel_[^"]+"[^>]*>')),
    ("search-panel",
     re.compile(r'<div[^>]*id="sc_layouts_search_panel_[^"]+"[^>]*>')),
]

DIV_OPEN = re.compile(r"<div\b[^>]*>")
DIV_CLOSE = re.compile(r"</div>")


def find_balanced_close(html: str, open_start: int, open_end: int) -> int | None:
    """Given the opening-tag span [open_start:open_end], return the index
    right after the matching </div>.  Returns None if unbalanced."""
    depth = 1
    pos = open_end
    while pos < len(html):
        next_open = DIV_OPEN.search(html, pos)
        next_close = DIV_CLOSE.search(html, pos)
        if next_close is None:
            return None  # unbalanced
        if next_open and next_open.start() < next_close.start():
            depth += 1
            pos = next_open.end()
        else:
            depth -= 1
            pos = next_close.end()
            if depth == 0:
                return pos
    return None


def strip_widget(html: str, opener: re.Pattern, label: str) -> tuple[str, int]:
    """Repeatedly find `opener` and cut from open-start to matching </div>."""
    count = 0
    while True:
        m = opener.search(html)
        if not m:
            break
        end = find_balanced_close(html, m.start(), m.end())
        if end is None:
            # Stop — cannot guarantee safe removal.
            print(f"    WARN: unbalanced at {label}: {html[m.start():m.start()+80]}...")
            break
        html = html[:m.start()] + html[end:]
        count += 1
    return html, count


def process(path: pathlib.Path) -> dict:
    html = path.read_text(encoding="utf-8", errors="replace")
    orig_len = len(html)
    stats: dict[str, int] = {}
    for label, opener in OPENERS:
        html, n = strip_widget(html, opener, label)
        stats[label] = n
    if len(html) != orig_len:
        path.write_text(html, encoding="utf-8", newline="")
    stats["bytes_removed"] = orig_len - len(html)
    return stats


def main():
    pages = sorted(SITE.glob("*.html"))
    hdr = f"{'file':<62} {'cart':>5} {'srch':>5} {'cpnl':>5} {'spnl':>5} {'bytes':>10}"
    print(hdr)
    print("-" * len(hdr))
    tot = {"cart-widget": 0, "search-widget": 0, "cart-panel": 0, "search-panel": 0, "bytes_removed": 0}
    for p in pages:
        r = process(p)
        print(f"{p.name:<62} {r['cart-widget']:>5} {r['search-widget']:>5} "
              f"{r['cart-panel']:>5} {r['search-panel']:>5} {r['bytes_removed']:>10}")
        for k in tot:
            tot[k] += r.get(k, 0)
    print("-" * len(hdr))
    print(f"{'TOTAL':<62} {tot['cart-widget']:>5} {tot['search-widget']:>5} "
          f"{tot['cart-panel']:>5} {tot['search-panel']:>5} {tot['bytes_removed']:>10}")


if __name__ == "__main__":
    main()
