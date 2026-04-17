# .claude/skills/ — Grit WP-to-Static Skills

Skills that travel with this project. Claude auto-loads them when a
user prompt matches the `description:` field in each `SKILL.md`.

## Installed skills (6)

| # | Skill                                                                   | Source    | Purpose                                                                                                                                |
| - | ----------------------------------------------------------------------- | --------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| 1 | [skill-creator](skill-creator/SKILL.md)                                 | Anthropic | Scaffold and improve skills. Used once to build the 5 custom skills below; kept in place for future edits.                              |
| 2 | [wp-to-static-extractor](wp-to-static-extractor/SKILL.md)               | Custom    | Download rendered HTML + assets from the live Business Coach demo. Encodes the Cloudflare-bypass `curl -A ...` recipe and verification checks. |
| 3 | [wp-markup-stripper](wp-markup-stripper/SKILL.md)                       | Custom    | Strip WP emoji/oEmbed/admin-bar/`?ver=` cruft while keeping `data-*`, RevSlider JSON, `TRX_ADDONS_STORAGE`, and Elementor inline config. |
| 4 | [animation-preservation-checklist](animation-preservation-checklist/SKILL.md) | Custom    | Pre-edit checklist to avoid silent animation regressions (script order, `defer`, `data-*`, RevSlider block, class names).                  |
| 5 | [asset-path-rewriter](asset-path-rewriter/SKILL.md)                     | Custom    | Rewrite absolute WP URLs to relative `/assets/**`, strip `?ver=`, leave Google Fonts / Font Awesome / Vimeo absolute.                     |
| 6 | [static-conversion-conventions](static-conversion-conventions/SKILL.md) | Custom    | Folder layout, WP-slug→filename rules, dynamic-feature policy (booking, cart, forms, search), and the "write a new file over patching" rule. |

## Attribution

`skill-creator/` is pulled from https://github.com/anthropics/skills
(Apache 2.0). See `skill-creator/ATTRIBUTION.md` for details.

The five custom skills were written for this project from a live audit of
`https://grit.ancorathemes.com/business-coach/` on 2026-04-17 and from
the Grit v1.10 theme source in `/grit/grit/`.

## How these skills are used

- Claude reads `description:` fields at session start and auto-triggers
  a skill when the user's prompt matches.
- Skills can be invoked explicitly by name.
- Skills stay version-controlled with the codebase — update the relevant
  `SKILL.md` when the extraction recipe or file layout changes.
