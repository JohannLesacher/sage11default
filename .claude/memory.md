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

## Architectural decisions

- **Atomic Design for CSS**: components organized by complexity (if applicable)
- **`@include()` for Blade**: components included with `@include()`, not `<x-component>`
- **Extended CPTs config-based**: post types and taxonomies defined in `config/post-types.php`, auto-registered
- **Storybook as source of truth**: if configured, it's the source of truth for components
- **`<section>` tag rule**: ACF blocks and macro components (page section organisms, excluding header/footer) use `<section>` as root tag

## Components créés

## Learned patterns

## Recurring errors to avoid

## Important files
