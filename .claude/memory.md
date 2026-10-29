# Memory — Sage 11 Default

> Shared in the repo. Update when you discover patterns, errors, or important decisions.
> Structure: stable facts at the top, changing things at the bottom.
> **To fill in empty sections**: ask Claude Code to analyze the codebase.

## Update protocol

- **Only append**, never overwrite or remove existing data
- Add to the appropriate section (Key Facts, Learned patterns, Recurring errors)
- If a fact contradicts an existing one, add a note with date and context
- Maximum 1-2 lines per addition (concise and useful)

## Key Facts

- **Bundler**: Vite (NOT Bud). `vite.config.js` with `@tailwindcss/vite`, `laravel-vite-plugin`, `@roots/vite-plugin`
- **PHP**: 8.3 (DDEV), theme minimum 8.2
- **WordPress**: 6.x + Bedrock
- **CPTs**: `evenement` (slug: `evenements`) — avec meta `date_event`
- **Taxonomies**: aucune pour l'instant
- **Pre-commit**: Husky → `npm run format:all:check` (Prettier + Pint + Blade Formatter)
- **Remotes**: `origin` = JohannLesacher/sage11default (starter template), `upstream` = roots/sage (`upstream/main`)

## Architectural decisions

- **Atomic Design for CSS**: components organized by complexity (if applicable)
- **`@include()` for Blade**: components included with `@include()`, not `<x-component>`
- **Extended CPTs config-based**: post types and taxonomies defined in `config/post-types.php`, auto-registered
- **Storybook as source of truth**: if configured, it's the source of truth for components
- **Fork CI divergence (2026-10-28)**: `.github/renovate.json` deliberately removed; CI matrix kept at Node 20 / PHP 8.2 — on upstream merges keep these, take only action version bumps
- **`<section>` tag rule**: ACF blocks and macro components (page section organisms, excluding header/footer) use `<section>` as root tag

## Components créés

## Learned patterns

- **BlockServiceProvider auto-discovery**: scans `app/Blocks/` and calls `register_block()` on each class — no manual registration needed, just create the file
- **`!is_admin()` wrap for `wp_enqueue_script_module`**: always wrap JS enqueue in `if (!is_admin())` inside `enqueue_assets` to avoid editor conflicts
- **wysiwyg field with `'raw' => true`**: needed so MetaBox returns raw HTML instead of escaped content; required for `{!! !!}` rendering in Blade
- **`$is_preview` for editor placeholders**: use `@elseif ($is_preview)` branches to show placeholder content in Gutenberg editor when fields are empty
- **Block template path convention**: `BlockEngine::renderBlock()` resolves view as `blocks.{block-id}` (e.g. `blocks.card` → `resources/views/blocks/card.blade.php`)
- **No JSX in editor scripts**: write `.js` with `import { createElement as el } from '@wordpress/element'` (same as `resources/js/admin/blocks/*-editor.js`) — no React installed, no JSX config in Vite
- **BlockServiceProvider `boot()` on `init` (2026-10-28)**: `boot()` (native blocks, `register_block_type`) runs once on `init`; `register_block()` stays in `rwmb_meta_boxes` (applied once by MetaBox on `init` prio 20; reason = native blocks must not depend on MetaBox being active)
- **Native block assets (WP 7.0)**: `wp_register_style`/`wp_register_script_module` in `boot()` + `style_handles`/`view_script_module_ids` → loaded only where block is rendered, style also in editor
- **Composer method vs view var name clash**: a public composer method overrides view data of the same key (e.g. `titles()` vs `titles`) — pass render data under another name (`tabTitles`)
- **Native composers can extend `Common`**: e.g. `Slider extends Common` to reuse `blockGap()`; `$blockClass()`/`$blockStyle()` work in native child blocks too
- **Server-built tabs nav**: render_callback 3rd arg `WP_Block $block` → read `$block->inner_blocks[*]->attributes` to build parent markup (Tabs nav titles); unique ids via `wp_unique_id()` in the callback, not in composer
- **Splide options via `data-splide` JSON** built in Slider composer; `@use "@splidejs/splide/css/core"` in SCSS inlines core CSS
- **MB Blocks field values**: stored in block attr `data` → composers read `$this->view->getData()['data'][$key]` (KeyFigure, TexteDefilant)
- **Generic block set (2026-10-28)**: native parent/child `sur-mesure/slider`→`slide`, `sur-mesure/tabs`→`tab` (child `parent` restricted, editors in `resources/js/admin/blocks/`); MB blocks `key-figure`, `texte-defilant`
- **Tabs mobile accordion**: `has-mobile-accordion` class (<768px) hides nav, shows `.block-tab__header` toggles; JS adds `is-ready`, without JS first panel shown via `:not(.is-ready)`
- **WP 7.0 core blocks overlap (2026-10-28)**: `core/tabs` (→ `tab-list` + `tab-panels` → `tab-panel`, Interactivity API, no responsive/accordion CSS) and `core/accordion` exist → `sur-mesure/tabs` mostly duplicates core; only extra = mobile accordion. No core slider. Check `wp-includes/blocks/` before building a custom block. **Same day: `sur-mesure/tabs` deleted, use `core/tabs`** (Tabs-related lines above are obsolete)
- **Render test without browser**: `wp eval 'echo do_blocks("<!-- wp:sur-mesure/tabs ... -->");'` from project root
- **InnerBlocks over wysiwyg**: for any free-form editorial content in a block, always use `<InnerBlocks />` — never `wysiwyg` MetaBox field. CTA uses separate `text` + `url` fields.

## Recurring errors to avoid

- **Client work pushed to template origin (2026-10-28)**: 3 client-project commits landed on `origin/master` by mistake; archived in `archive/client-work`, origin reset. Check remote before pushing from client clones
- **`react/jsx-runtime` unresolved on build (2026-10-28)**: caused by `.jsx` files (Vite 8 automatic JSX runtime); fixed by converting `admin/*.jsx` to `.js` + `el()`
- **`MakeBlockCommand` composer stub missing import (2026-10-28)**: generated composer lacks `use Roots\Acorn\View\Composer;` → fatal; add it manually until stub fixed
- **Polylang not installed in default theme**: use `__('…', 'sage')`, never `pll__()`
- **`\n` inside PHP heredoc JSON in `wp eval` tests**: becomes real newline → invalid block JSON → attrs silently empty
- **Horizontal InnerBlocks editor hides appender (2026-10-28)**: `overflow-x: auto` on slider editor layout pushed the + button out of view → use `flex-wrap: wrap` + `.block-list-appender { flex: 0 0 100% }` on its own row. **Update same day**: user prefers slides stacked vertically in editor (big slides) → no horizontal/grid layout in slider editor, default vertical stacking
- **Firefox importmap quickfix broke Interactivity API (2026-10-28)**: `app/filters.php` removed whole `WP_Script_Modules::add_hooks` → no `wp-script-module-data-*` printed → `core/tabs` JS error `tabsList is undefined`. Fixed: only move the 3 `wp_footer` print hooks (import map, modules, preloads) to `wp_head`, keep the rest. Affected every Interactivity core block (`core/accordion`, navigation overlay, image lightbox…) — those not re-tested after fix. Works because Sage renders Blade child sections (blocks) before layout's `wp_head`, so modules are known in head
- **Force-push on master blocked by hook**: user must run `git push --force-with-lease ...` manually

## Important files
