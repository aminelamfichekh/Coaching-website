---
name: animation-preservation-checklist
description: Pre-edit checklist for any change to HTML, CSS, or JS under /static-site/. Triggers before editing files in /static-site/, when working on visuals, fixing layout, or touching <script>/<style> blocks in converted pages. Prevents silent animation regressions in the Grit theme by locking in rules that are easy to forget (don't reformat CSS, don't reorder scripts, don't strip data-* attrs, don't touch the RevSlider JSON block, don't alter defer attributes).
---

# animation-preservation-checklist

The Grit theme's animations live in a tangled web of GSAP, Swiper v8,
Magnific Popup, Revolution Slider sr6, Elementor frontend, superfish menu,
and `trx_addons` components. They break silently when disrupted.

Before editing any file in `/static-site/`, walk this checklist.

## The 10-point checklist

1. **Don't reformat CSS.** No prettifier, no `--write` on CSS files, no
   reordering declarations. The theme CSS is load-bearing whitespace in
   places — reformatting is never necessary for this task.

2. **Don't reorder `<script>` tags** in any `.html`. Required order:
   `jquery → jquery-migrate → jquery-ui (core + datepicker) → magnific →
   gsap → elementor webpack runtime → elementor frontend-modules →
   elementor frontend → trx_addons/__scripts.js →
   trx_addons/components/** → themes/grit/js/__scripts.js →
   skins/default/skin.js`.

3. **Don't touch `defer="defer"`.** Not add, not remove, not move. Some
   scripts rely on DOM-ready; others rely on defer. Match source.

4. **Keep every `data-*` attribute.** Do not "tidy up" attributes you
   don't recognize — `data-elementor-settings`, `data-tp-*` (RevSlider),
   `data-swiper-*`, `data-gsap-*`, `data-aos`, `data-parallax`,
   `data-effect`, `data-widget_type`. All of these are read at runtime.

5. **Keep the RevSlider inline block intact.** Anything around
   `setREVStartSize`, `RS_MODULES.modules[...]`, `var htmlDivCss`,
   `revapi...`. Treat it as opaque — copy/paste verbatim.

6. **Keep `TRX_ADDONS_STORAGE` verbatim.** Every key is read somewhere
   in `trx_addons`. Don't trim for "unused" fields.

7. **Keep `<style id='elementor-frontend-inline-css'>` intact.**
   It contains the page's CSS custom properties (`--e-*`). Stripping
   breaks colors and typography.

8. **Don't rename structural classes.** `.page_wrap`, `.top_panel`,
   `.footer_wrap`, `.sidebar`, `.trx_addons_columns_wrap`, `.menu_mobile_inner`,
   `.sc_item_*` are selectors consumed by theme JS.

9. **Don't merge or minify files** that were not already merged/minified.
   Keep `__scripts.js` and `__scripts-full.js` separate if source had them
   separate.

10. **Test in a real browser before claiming success.** Type checks and
    syntax parses are not enough. Open the file, compare against
    `https://grit.ancorathemes.com/business-coach/` side-by-side, and
    scroll the entire page to trigger every intersection-observer and
    GSAP ScrollTrigger.

## Red flags — stop and ask the user

- "Let me just tidy up this stylesheet" → NO.
- "I'll rewrite this JS init in a cleaner way" → NO.
- "This `data-*` looks unused, I'll remove it" → NO.
- "These scripts can be reordered for better performance" → NO.
- Any change to the RevSlider block → verify with user.

## When animations DO break

Diagnose in this order — do not skip steps:

1. Open browser console. Look for `jQuery is not defined`, `gsap is not
   defined`, `Swiper is not a constructor`, or 404s on JS files.
2. Compare `<script>` URLs against the live demo.
3. Compare `<script>` order against the live demo.
4. Check `TRX_ADDONS_STORAGE` is present and has the same keys.
5. Check the RevSlider `setREVStartSize` + `RS_MODULES` block is present.
6. Check for late-loading CSS that hasn't finished when JS initializes
   (should be fixed by keeping original `<link>` order).

## When NOT to trigger

- Editing files outside `/static-site/`.
- Pure content edits (text, copy).
- Editing README/NOTES/docs.
