"""Remove the Tidio chat widget and advanced-popups modal from every page.

Kills:
  - <script src="https://code.tidio.co/...">  (Tidio chat + presale-chat popup)
  - <div class="adp-popup ...">...</div>      (advanced-popups modal content)
  - <script id="advanced-popups-js">          (runtime that fires the modal)
  - <link id='advanced-popups-css'>           (modal styles)
  - <link rel="preload" ...advanced-popups... font>

The mobile slide-out menu (<div id="popup" class="sc_layouts sc_layouts_panel...">)
is NOT touched — that's the hamburger drawer, not a popup.
"""
import re
import pathlib

SITE = pathlib.Path(__file__).resolve().parent.parent / "docs"

# Patterns that can be removed with a flat regex (single element, no nesting).
FLAT_STRIPS = [
    # Tidio (<script ... src="...tidio.co..." ...></script>) — self-closing style
    re.compile(r"""<script[^>]*src=["'][^"']*tidio\.co[^"']*["'][^>]*></script>\s*""", re.IGNORECASE),
    # advanced-popups JS
    re.compile(r"""<script[^>]*id=["']advanced-popups-js["'][^>]*></script>\s*""", re.IGNORECASE),
    # advanced-popups CSS
    re.compile(r"""<link[^>]*id=["']advanced-popups-css["'][^>]*/?>\s*""", re.IGNORECASE),
    # advanced-popups font preload
    re.compile(r"""<link[^>]*href=["'][^"']*advanced-popups[^"']*\.woff2?["'][^>]*/?>\s*""", re.IGNORECASE),
]

# Div-balanced: the modal container.
ADP_OPEN = re.compile(r'<div[^>]*class="adp-popup\b[^"]*"[^>]*>')
DIV_OPEN = re.compile(r"<div\b[^>]*>")
DIV_CLOSE = re.compile(r"</div>")


def find_balanced_close(html, start, end):
    depth = 1
    pos = end
    while pos < len(html):
        o = DIV_OPEN.search(html, pos)
        c = DIV_CLOSE.search(html, pos)
        if c is None:
            return None
        if o and o.start() < c.start():
            depth += 1
            pos = o.end()
        else:
            depth -= 1
            pos = c.end()
            if depth == 0:
                return pos
    return None


def strip_adp(html):
    n = 0
    while True:
        m = ADP_OPEN.search(html)
        if not m:
            break
        end = find_balanced_close(html, m.start(), m.end())
        if end is None:
            break
        html = html[:m.start()] + html[end:]
        n += 1
    return html, n


total = {"tidio+popup-css/js": 0, "adp-modal": 0, "bytes": 0}
for p in sorted(SITE.glob("*.html")):
    html = p.read_text(encoding="utf-8", errors="replace")
    before = len(html)

    flat_hits = 0
    for pat in FLAT_STRIPS:
        html, n = pat.subn("", html)
        flat_hits += n

    html, adp_hits = strip_adp(html)

    if len(html) != before:
        p.write_text(html, encoding="utf-8", newline="")

    total["tidio+popup-css/js"] += flat_hits
    total["adp-modal"] += adp_hits
    total["bytes"] += before - len(html)
    print(f"{p.name:<62} flat={flat_hits:>2} adp-modal={adp_hits:>2} bytes={before - len(html):>6}")

print("-" * 80)
print(f"TOTAL  flat(tidio+popup css/js/font)={total['tidio+popup-css/js']}  "
      f"adp-modal={total['adp-modal']}  bytes={total['bytes']}")
