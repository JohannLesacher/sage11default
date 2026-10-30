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
- **Update (2026-10-30)**: Pre-commit line above obsolete — tooling aligned on Radicle: Mago (`composer format` / `lint`, replaces Pint), oxfmt + oxlint (`npm run format` / `lint`); CI runs both. Husky hook not re-checked
- **Remotes**: `origin` = JohannLesacher/sage11default (starter template), `upstream` = roots/sage (`upstream/main`)

## Architectural decisions

- **Atomic Design for CSS**: components organized by complexity (if applicable)
- **`@include()` for Blade**: components included with `@include()`, not `<x-component>`
- **Extended CPTs config-based**: post types and taxonomies defined in `config/post-types.php`, auto-registered
- **Storybook as source of truth**: if configured, it's the source of truth for components
- **Fork CI divergence (2026-10-28)**: `.github/renovate.json` deliberately removed; CI matrix kept at Node 20 / PHP 8.2 — on upstream merges keep these, take only action version bumps
- **`<section>` tag rule**: ACF blocks and macro components (page section organisms, excluding header/footer) use `<section>` as root tag
- **SVG upload (2026-10-29)**: WP 7.1 has no native SVG support → `app/filters.php` allows `svg` mime only for `manage_options` and sanitizes on `wp_handle_upload_prefilter` with `enshrined/svg-sanitize` (remote refs removed, invalid file rejected)
- **Update CI (2026-10-29)**: line above obsolete — CI now on `master`, Node 24 / PHP 8.3 (PHP 8.2 was below composer `php >=8.3`, Node 20 frozen without reason). Keep aligned with real stack, not frozen
- **Theme settings access (2026-10-29)**: always via `App\Services\ThemeSettings` → `get($key, $default)` / `translated($key, $default)` (Polylang lang → `_fr` fallback); never `get_option('theme_settings')` directly
- **`config/theme.php` (2026-10-29)**: header options (`fixed`, `align`, `lang`, `cta`) read by Header composer + `body_class` filter in `app/filters.php`
- **Starter content kept on purpose**: CPT `evenement` = example CPT of the starter; image size `lowres` = blur-up placeholder; `config/external.scss` empty on purpose (third-party CSS). Splide CSS stays in `blocks/slider.scss` (loaded only with the block)
- **`config/theme.php` scope (2026-10-29)**: also `menus` (register_nav_menus) and `image_sizes` (add_image_size, `label` → shown in editor). `make:header` command removed (header options live in config)
- **Vite paths (2026-10-29)**: `base` derived from theme folder name, `APP_URL` from env or Bedrock `.env` `WP_HOME` → theme can be duplicated/renamed without editing vite.config.js
- **Block architecture (2026-10-29)**: `app/Blocks/Base/` (not scanned) holds `MetaBoxBlock` (subclass implements `definition()`, id = kebab class name, `register()` fed to rwmb_meta_boxes) and `NativeBlock` (metadata in `resources/blocks/{name}/block.json`, single source PHP + editor JS via `@blocks` alias). `BlockEngine` and `register_block()` removed. `make:block [--native] [--js] [--vc] [--force]`
- **Block assets (2026-10-29)**: `LoadsAssets` trait auto-loads `css/blocks/{slug}.scss` + `js/blocks/{slug}.js` if they exist: in head when `has_block()`, else at render; always in editor. Before this, MB `enqueue_assets` and native `style_handles` loaded every block CSS on every page
- **Block composers (2026-10-29)**: extend `App\View\BlockComposer` (`field()`, `attribute()`, public `wrapperAttributes($class, $extra)` → `get_block_wrapper_attributes`). Views: `<div {!! $wrapperAttributes('block-x', [...]) !!}>`; composers return raw values (escaped by wrapper). `Common` only exposes it to every `blocks.*` view
- **Animated blocks list (2026-10-29)**: single source `config('theme.animation.blocks')` → PHP attributes filter + `window.theme.animationBlocks` printed before editor scripts in `app/setup.php`
- **Update native blocks (2026-10-29)**: no PHP class needed — `BlockServiceProvider` registers every `resources/blocks/*/block.json` with `App\Blocks\Base\NativeBlock` (instance per block); optional `App\Blocks\{Name} extends NativeBlock` overrides it (user found empty classes odd). `make:block --native` generates no class. New block assets need `npm run dev`/`build` before render (Vite manifest)

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
- **Logo**: `partials/logo.blade.php` (composer `Logo`) — theme settings `logo` image if set, else `@svg('logo')` + sr-only site name; used in header and footer
- **Navigation partial**: `partials/navigation.blade.php` (recursive, `sub-menu` children, `aria-current`) used by header and footer menus
- **JSX enabled (2026-10-29)**: line "No JSX in editor scripts" obsolete. `.jsx` editor files use the standard automatic runtime: `wordpressJsx()` plugin in vite.config.js compiles JSX before `@roots/vite-plugin` (else dev emits `require()`), `react/jsx-runtime` aliased to `resources/js/lib/react-jsx-runtime.js` which imports WP's `ReactJSXRuntime` via `externalMappings` (deps: `react-jsx-runtime`). Never bundle React / `@vitejs/plugin-react` (duplicate React → invalid hook call in editor)
- **Full native blocks (2026-10-29)**: MB Blocks dropped for blocks (MetaBox kept for settings pages / post metas). Lines above about MB Blocks fields / `data` attr / `resources/js/admin/blocks/` are obsolete. One block = `resources/blocks/{name}/block.json` + `editor.jsx` (auto-imported via `import.meta.glob` in editor.js) + `views/blocks/{name}.blade.php`. Attribute-driven blocks: `InspectorControls` + `ServerPreview` (`resources/js/admin/components/server-preview.jsx`, ServerSideRender `skipBlockSupportAttributes`) → Blade stays single HTML source; `$is_preview` = `wp_is_serving_rest_request()`. `make:block {name} [--inner] [--js] [--vc]`. Old `meta-box/*` content converted with a one-shot wp eval-file script (data → typed attrs)
- **Block editing modes (2026-10-29)**: 3 patterns, pick per block — `ServerPreview` (render depends on PHP: carte, texte-defilant), inline `RichText` mirroring Blade markup (text typed by editor: key-figure), JSX preview from REST via `useSelect`/`core-data` (data lists: evenements). `make:block` default / `--inline` / `--inner`. Core block extensions = class in `app/Blocks/` with static `boot()` (example `CoreButton`: `register_block_style` + `wp_enqueue_block_style` + `render_block_core/button` filter with Blade partial). Note: core/button already ships `outline` style
- **Block colocation (2026-10-29)**: everything for a block in `resources/blocks/{name}/` (`block.json`, `editor.jsx`, `view.blade.php` = view `block::{name}.view`, `style.scss`, `script.js`); PHP logic in `app/Blocks/{Name}.php` extending `App\Blocks\Base\BlockComposer` (auto-bound to the view by `App\Blocks\Base\Block`, optional static `boot()`). Core block extensions = `app/Blocks/Core{Name}.php` + `resources/blocks/core-{name}/` (no block.json), trait `ExtendsCoreBlock::blockStyle()` loads style only where used. Paths `resources/views/blocks`, `resources/{css,js}/blocks`, `app/View/Composers/Blocks`, `NativeBlock` are obsolete. `make:block --php` replaces `--vc`. Built files prefixed by block folder (`carte-style-*.css`)
- **Block layout, final (2026-10-29)**: supersedes "Block colocation" line above. `resources/blocks/{name}/` = block.json, editor.jsx, style.scss, script.js only. View stays Sage: `resources/views/blocks/{name}.blade.php`; logic in composer `app/View/Composers/Blocks/{Name}.php` (extends `App\View\BlockComposer`, auto-discovered by Acorn) only if needed. No Block/NativeBlock class: `BlockServiceProvider` registers, renders (passes `attributes`, `content`, `block`, `is_preview`, `wrapperAttributes` closure) and loads assets. `app/Blocks/` = core block extensions only (`CoreButton`, `CoreGallery`, trait `Base/ExtendsCoreBlock`). User dislikes generic `view.blade.php` names and composers outside `app/View/Composers` (not Sage spirit). `make:block --vc` for composer
- **Breakpoints & tokens (2026-10-29)**: single source theme.json — `settings.viewport` (mobile 480, tablet 782, WP semantics) + `settings.custom.breakpoints.large` (1024). Vite `themeJsonTokens()` generates `_theme.scss` (`$breakpoints`) and `resources/js/config/breakpoints.js`; SCSS `@include mixins.breakpoint(tablet, up|down)` (down = `width <=`, up = `width >`), PHP `wp_get_global_settings(['viewport', $name])`. Only hard-coded query left: `max-width: 781px` in responsive.scss (matches core/columns). Layout widths = `--wp--style--global--content-size`/`wide-size` + `--wp--custom--layout--extra-wide-size` (user wants to keep extra-wide even if unused). Links/headings in theme.json `styles.elements`. Classes: `.skip-link`, `body.has-open-menu`

## Recurring errors to avoid

- **Client work pushed to template origin (2026-10-28)**: 3 client-project commits landed on `origin/master` by mistake; archived in `archive/client-work`, origin reset. Check remote before pushing from client clones
- **`react/jsx-runtime` unresolved on build (2026-10-28)**: caused by `.jsx` files (Vite 8 automatic JSX runtime); fixed by converting `admin/*.jsx` to `.js` + `el()`
- **`MakeBlockCommand` composer stub missing import (2026-10-28)**: generated composer lacks `use Roots\Acorn\View\Composer;` → fatal; add it manually until stub fixed
- **Polylang not installed in default theme**: use `__('…', 'sage')`, never `pll__()`
- **`\n` inside PHP heredoc JSON in `wp eval` tests**: becomes real newline → invalid block JSON → attrs silently empty
- **Horizontal InnerBlocks editor hides appender (2026-10-28)**: `overflow-x: auto` on slider editor layout pushed the + button out of view → use `flex-wrap: wrap` + `.block-list-appender { flex: 0 0 100% }` on its own row. **Update same day**: user prefers slides stacked vertically in editor (big slides) → no horizontal/grid layout in slider editor, default vertical stacking
- **Firefox importmap quickfix broke Interactivity API (2026-10-28)**: `app/filters.php` removed whole `WP_Script_Modules::add_hooks` → no `wp-script-module-data-*` printed → `core/tabs` JS error `tabsList is undefined`. Fixed: only move the 3 `wp_footer` print hooks (import map, modules, preloads) to `wp_head`, keep the rest. Affected every Interactivity core block (`core/accordion`, navigation overlay, image lightbox…) — those not re-tested after fix. Works because Sage renders Blade child sections (blocks) before layout's `wp_head`, so modules are known in head
- **Force-push on master blocked by hook**: user must run `git push --force-with-lease ...` manually
- **`{{ }}` double-encodes WordPress entities (2026-10-29)**: Blade `e()` re-encodes `&rsquo;` (wptexturize) / `&amp;` (bloginfo) → `&amp;rsquo;` on screen; for WP data use `{!! esc_html() !!}` / `{!! esc_attr() !!}` (no double encoding). `{{ }}` OK for technical values (align, slugs)
- **Update (2026-10-29)**: `MakeBlockCommand` composer stub import fixed (line above obsolete); command now refuses to overwrite existing files without `--force`
- **JSX dev scan (2026-10-29)**: Vite dep scan ignores `oxc` config and looks for `react/jsx-dev-runtime` → "Failed to run dependency scan" + no pre-bundling; fixed with `optimizeDeps.rolldownOptions.transform.jsx` (`automatic`, `development: false`)
- **Mago without `php-version` targets PHP 8.4 (2026-10-30)**: `mago format` rewrote `(new X())->m()` to `new X()->m()` → parse error on PHP 8.3 in `BlockServiceProvider.php`; keep `php-version = "8.3"` in `mago.toml`, run `php -l` after mass reformat

## Important files
