"""Rewrite protocol-relative URLs (//example.com/...) to https://example.com/...

Protocol-relative URLs work fine when a page is served over https:// or
http:// — the browser inherits the page's protocol.  But when a page is
opened directly by double-clicking the .html file (file:// protocol),
the browser resolves // to file:// and requests fail.

Fixing these makes the static site work in all three modes:
  - https://  (hosted)
  - http://   (local dev server)
  - file://   (just opened the .html file)
"""
import re
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "docs"

# Matches href="//host/..." / src="//host/..." / url(//host/...) / rel="preload" etc.
# Captures the //host/path portion and prepends https:.
ATTR = re.compile(r"""(?P<pre>(?:src|href|action|data-src|data-href)=["'])//(?=[a-z0-9])""", re.IGNORECASE)
URL_FUNC = re.compile(r"""(?P<pre>url\(["']?)//(?=[a-z0-9])""", re.IGNORECASE)

total = {"html_attr": 0, "css_url": 0}

# Pass 1: HTML
for p in sorted(SITE.glob("*.html")):
    html = p.read_text(encoding="utf-8", errors="replace")
    new, a = ATTR.subn(r"\g<pre>https://", html)
    new, u = URL_FUNC.subn(r"\g<pre>https://", new)
    if new != html:
        p.write_text(new, encoding="utf-8", newline="")
    total["html_attr"] += a
    total["css_url"] += u

# Pass 2: CSS files under assets/ (they can have url(//...) too)
for p in SITE.rglob("assets/**/*.css"):
    if not p.is_file():
        continue
    css = p.read_text(encoding="utf-8", errors="replace")
    new, a = ATTR.subn(r"\g<pre>https://", css)
    new, u = URL_FUNC.subn(r"\g<pre>https://", new)
    if new != css:
        p.write_text(new, encoding="utf-8", newline="")
    total["html_attr"] += a
    total["css_url"] += u

print(f"Rewritten in attributes: {total['html_attr']}")
print(f"Rewritten in url(...):  {total['css_url']}")
