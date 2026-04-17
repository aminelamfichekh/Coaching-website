"""Scan all static-site/*.html for referenced asset paths under assets/,
report any that do NOT exist on disk, and write a fetch queue.

Run:  python scripts/check-missing-assets.py
"""
from __future__ import annotations
import re
import pathlib

ROOT = pathlib.Path(__file__).resolve().parent.parent
SITE = ROOT / "static-site"

# Matches relative paths starting with assets/... (most common from Phase 3 rewrites)
# or absolute /assets/... (if any sneaked in). Also catches CSS url(assets/...).
ASSET_RE = re.compile(
    r"""(?:
        (?:src|href|data-src|data-background-image)\s*=\s*["']
        |
        url\(\s*["']?
    )
    (?P<path>assets/[^"'\s)]+)
    """,
    re.VERBOSE | re.IGNORECASE,
)

HOST_URL = "https://grit.ancorathemes.com"

# Map assets/... path → source URL
def to_source_url(relpath: str) -> str:
    # assets/theme/<x>         → wp-content/themes/grit/<x>
    # assets/plugins/<x>       → wp-content/plugins/<x>
    # assets/images/uploads/<x> → wp-content/uploads/<x>
    # assets/vendor/wp-includes/<x> → wp-includes/<x>
    if relpath.startswith("assets/theme/"):
        sub = relpath[len("assets/theme/"):]
        return f"{HOST_URL}/wp-content/themes/grit/{sub}"
    if relpath.startswith("assets/plugins/"):
        sub = relpath[len("assets/plugins/"):]
        return f"{HOST_URL}/wp-content/plugins/{sub}"
    if relpath.startswith("assets/images/uploads/"):
        sub = relpath[len("assets/images/uploads/"):]
        return f"{HOST_URL}/wp-content/uploads/{sub}"
    if relpath.startswith("assets/vendor/wp-includes/"):
        sub = relpath[len("assets/vendor/wp-includes/"):]
        return f"{HOST_URL}/wp-includes/{sub}"
    return ""


def main():
    refs: set[str] = set()
    for p in sorted(SITE.glob("*.html")):
        text = p.read_text(encoding="utf-8", errors="replace")
        for m in ASSET_RE.finditer(text):
            refs.add(m.group("path"))

    missing = []
    for rel in sorted(refs):
        disk = SITE / rel
        if not disk.exists():
            url = to_source_url(rel)
            if url:
                missing.append((url, str(disk.relative_to(ROOT))))

    # Write to repo-local path (avoid /tmp path confusion between Python/bash on Windows).
    queue_path = ROOT / ".asset-queue.tmp"
    with queue_path.open("w", encoding="utf-8", newline="\n") as f:
        for url, dst in missing:
            # Normalize to forward slashes — bash + curl expect POSIX paths.
            f.write(f"{url}|{dst.replace(chr(92), '/')}\n")

    print(f"Total references: {len(refs)}")
    print(f"Missing on disk:  {len(missing)}")
    print(f"Queue written to: {queue_path}")


if __name__ == "__main__":
    main()
