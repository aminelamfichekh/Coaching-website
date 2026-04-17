# TREE.md — Phase 1 Discovery (updated post-amendment)

Generated: 2026-04-17 · Revised after user decisions
Target demo: https://grit.ancorathemes.com/business-coach/

---

## Top-level layout (current)

```
Grit_v1.10/
├── .claude/                     ← skills + settings — KEEP
├── .git/                        ← → github.com/aminelamfichekh/Coaching-website
├── .gitignore
├── documentation.html           ← Ancora docs (4KB) — KEEP (reference)
├── Licensing/                   ← GPL + license README — KEEP (legal)
├── grit/grit/                   ← WP theme source (28MB) — KEEP (reference)
├── grit-child/grit-child/       ← Child theme overrides (302KB) — KEEP (reference, re-evaluate in Phase 5)
├── plugins/                     ← booked.zip + revslider.zip (11MB tracked) — KEEP
├── _plugin-source/              ← extracted from plugins/*.zip (gitignored, ~24MB)
│   ├── booked/                  ← booked calendar plugin source
│   └── revslider/               ← RevSlider sr6 runtime + admin source
├── _old-conversion-attempt/     ← archived prior developer's conversion (44KB) — KEEP until Phase 5
├── TREE.md                      ← this file
└── [future] _original_backup/   ← created at Phase 2 start (gitignored)
└── [future] static-site/        ← Phase 2 output
```

---

## Key finding — the theme is single-skin

Verified: `grit/grit/skins/` contains only `default/`, and `skins.json`
declares only `{"default": {...}}`. All six live demos (Business Coach +
Public Speaker, Life Coach, Yoga Instructor, Nutrition Coach, Personal
Trainer) share the **same** theme code and `default` skin. They differ
only in imported *content* (pages, Elementor layouts, RevSlider exports,
product data, theme-options colors).

Consequence: **there are no sibling demo folders to delete.** The other
five demos live on separate subdomains we never fetch.

---

## Demo variants

| Variant          | URL                                                | In scope? |
| ---------------- | -------------------------------------------------- | --------- |
| Business Coach   | `https://grit.ancorathemes.com/business-coach/`    | **YES**   |
| Public Speaker   | `https://public-speaker.grit.ancorathemes.com/`    | no        |
| Life Coach       | `https://life-coach.grit.ancorathemes.com/`        | no        |
| Yoga Instructor  | `https://yoga-instructor.grit.ancorathemes.com/`   | no        |
| Nutrition Coach  | `https://nutrition-coach.grit.ancorathemes.com/`   | no        |
| Personal Trainer | `https://personal-trainer.grit.ancorathemes.com/`  | no        |

Phase 2 only fetches from `grit.ancorathemes.com`.

---

## Decisions applied

### `/website/` → archived

Moved to `/_old-conversion-attempt/` via `git mv`. It's a prior
developer's abandoned conversion attempt with the wrong color palette
(orange/blue; live demo is green/beige). Kept as reference through
Phase 5; may delete later if nothing useful surfaces.

### `/grit-child/` → keep as reference

Child theme overrides. Tracked in git. Re-evaluated in Phase 5 after
`/static-site/` is self-sufficient.

### `/plugins/*.zip` → extracted to `/_plugin-source/`

Both zips extracted to `/_plugin-source/{booked,revslider}/` as local
reference. Gitignored (the zips themselves are already tracked in
`/plugins/`). Regenerate anytime with:

```
unzip plugins/booked.zip    -d _plugin-source/
unzip plugins/revslider.zip -d _plugin-source/
```

Useful assets confirmed present:
- `_plugin-source/booked/assets/{css,js}/...` — calendar frontend
  styles and JS for the booking widget visual rendering.
- `_plugin-source/revslider/` — RevSlider admin + public runtime;
  fallback for sr6 assets if Cloudflare blocks mid-scrape.

---

## Phase 2 plan — amended

**Scope change:** "delete other demos" is removed (nothing to delete).
Phase 2 is now purely: fetch Business Coach pages and build
`/static-site/`.

**Asset source-of-truth order** (look locally first, fall back to live
server):

1. **Local theme:** `grit/grit/` for theme CSS/JS/fonts/icons.
2. **Local plugins:** `_plugin-source/{booked,revslider}/` for those
   plugins' CSS/JS.
3. **Local child theme:** `grit-child/grit-child/` for overrides.
4. **Live server (Cloudflare-safe `curl -A ...`):** only for things that
   aren't in local files — page-specific HTML, `wp-content/uploads/`
   images, inline config blocks (`TRX_ADDONS_STORAGE`, RevSlider JSON),
   and plugins not in the local bundle (Elementor, trx_addons,
   woocommerce, jQuery from wp-includes).

**Phase 2 step sequence:**

1. Backup the current tree to `/_original_backup/` (minus `.git/` and
   itself). Gitignored.
2. Create `/static-site/` with the layout defined in
   `.claude/skills/static-conversion-conventions/SKILL.md`.
3. Fetch Business Coach pages from the live server:
   `/`, `/our-services/`, `/appointment/`, `/blog/`, `/shop/`, `/contact/`,
   plus linked blog posts, service detail pages, and product pages
   discovered during extraction.
4. For each page: save HTML, then for each referenced asset URL:
   - If the file exists under `grit/grit/...` or `_plugin-source/...`,
     copy it from there into `/static-site/assets/...`.
   - Otherwise fetch from the live server.
5. Do NOT strip, rewrite, or edit HTML yet. Phase 2 is raw capture.
   Stripping (`wp-markup-stripper`) and rewriting (`asset-path-rewriter`)
   come in Phase 3/4.

---

## Git workflow agreed

Push at these checkpoints only:

- [x] After TREE.md update + Phase 2 plan amendment (**now**)
- [ ] After Phase 2 complete
- [ ] After Phase 3 complete
- [ ] After Phase 5 complete

Between pushes, commit locally as often as useful. Descriptive messages.
