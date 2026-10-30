# sage11default

Thème de base WordPress construit sur [Sage 11](https://roots.io/sage/) (Blade, Vite, [Acorn](https://roots.io/acorn/)), pensé comme point de départ pour des projets Bedrock. Outillage aligné sur [Radicle](https://roots.io/radicle/).

## Prérequis

- PHP ≥ 8.3, Composer
- Node ≥ 24
- WordPress (Bedrock) avec MetaBox pour les réglages et métas
- WP-CLI pour les scripts de traduction

## Installation

```sh
composer install
npm install
npm run dev     # serveur Vite + HMR
npm run build   # assets de production dans public/build/
```

## Commandes

| Commande                    | Rôle                                                        |
| --------------------------- | ----------------------------------------------------------- |
| `composer format`           | Vérifie le formatage PHP (Mago)                             |
| `composer format:fix`       | Formate le PHP                                              |
| `composer lint`             | Analyse le PHP (Mago)                                       |
| `npm run format`            | Formate JS, JSX, SCSS, JSON, Markdown (oxfmt)               |
| `npm run format:check`      | Vérifie le formatage sans écrire                            |
| `npm run lint`              | Analyse le JS (oxlint) ; `lint:fix` corrige automatiquement |
| `npm run translate`         | Génère `resources/lang/sage.pot` et met à jour les `.po`    |
| `npm run translate:compile` | Compile les `.mo` et les JSON de traduction JS              |
| `wp acorn make:block`       | Génère un bloc natif (voir plus bas)                        |

La CI (`.github/workflows/main.yml`) lance le formatage, le lint et le build.

## Structure

```
app/
  Blocks/            extensions de blocs core (Core{Name}.php)
  Providers/         BlockServiceProvider, ThemeServiceProvider
  View/Composers/    composers de vues, Blocks/ pour les blocs
config/theme.php     réglages du thème (header, tailles d'images, langues…)
resources/
  blocks/{name}/     block.json, editor.jsx, style.scss, script.js
  css/ js/           assets globaux
  views/blocks/      rendu Blade des blocs
theme.json           palette, typographie, tailles de layout, points de rupture
```

## Blocs

Tous les blocs sont natifs (JSX via le runtime React de WordPress, rendu serveur en Blade). Ils sont enregistrés automatiquement depuis `resources/blocks/` dans la catégorie `sur-mesure`.

```sh
wp acorn make:block "Mon bloc"            # aperçu serveur (ServerPreview)
wp acorn make:block "Mon bloc" --inline   # édition RichText en place
wp acorn make:block "Mon bloc" --inner    # InnerBlocks
# options : --js (script front), --vc (View Composer)
```

Après création d'un bloc, relancer `npm run dev` ou `npm run build` pour que Vite connaisse ses assets.

## Tokens

Les points de rupture et tailles de layout viennent de `theme.json` : mixin SCSS `breakpoint()` et `resources/js/config/breakpoints.js` côté JS. Ne pas les dupliquer en dur.
