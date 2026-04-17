"""Audit: how many shipped upload images are referenced by at least one page?"""
import re
import pathlib

SITE = pathlib.Path("static-site")

imgs_on_disk = set()
for f in SITE.rglob("assets/images/uploads/**/*"):
    if f.is_file():
        imgs_on_disk.add(str(f.relative_to(SITE)).replace("\\", "/"))

REF_PATTERNS = [
    re.compile(r"""(?:src|href|data-src|data-background-image)=['"](assets/images/uploads/[^'"\s]+)['"]"""),
    re.compile(r"""url\(['"]?(assets/images/uploads/[^'")\s]+)['"]?\)"""),
]

referenced = set()
for p in SITE.glob("*.html"):
    text = p.read_text(encoding="utf-8", errors="replace")
    for pat in REF_PATTERNS:
        for m in pat.finditer(text):
            referenced.add(m.group(1))
    # srcset (can have multiple images with width descriptors)
    for m in re.finditer(r"""srcset=['"]([^'"]+)['"]""", text):
        for part in m.group(1).split(","):
            path = part.strip().split()[0]
            if path.startswith("assets/images/uploads/"):
                referenced.add(path)

# Also scan CSS files under assets/
for p in SITE.rglob("assets/**/*.css"):
    if not p.is_file():
        continue
    text = p.read_text(encoding="utf-8", errors="replace")
    for m in re.finditer(r"""url\(['"]?([^'")\s]+)['"]?\)""", text):
        url = m.group(1)
        if url.startswith("../../images/uploads/"):
            referenced.add("assets/" + url.replace("../../", ""))
        elif url.startswith("assets/images/uploads/"):
            referenced.add(url)

used = imgs_on_disk & referenced
unused = imgs_on_disk - referenced

print(f"shipped:     {len(imgs_on_disk)}")
print(f"referenced:  {len(referenced)}")
print(f"used:        {len(used)}")
print(f"unused:      {len(unused)}  (candidates for Phase 5 trim)")
if unused:
    print("\nSample unused (first 15):")
    for f in sorted(unused)[:15]:
        print(f"  {f}")
