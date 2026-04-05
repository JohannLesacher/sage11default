# CLAUDE.md — Thème Sage 11 (`default`)

## Stack

| Couche | Technologie |
|--------|-------------|
| Thème | [Sage 11](https://roots.io/sage/) (Roots) |
| Framework PHP | [Acorn 5](https://roots.io/acorn/) (Laravel pour WordPress) |
| Templates | Blade (Laravel) |
| Build | Vite 7 + laravel-vite-plugin + @roots/vite-plugin |
| CSS | SCSS (Sass) |
| JS | ESModules natif |
| PHP | >= 8.2 |
| Node | >= 20.0.0 |

## Structure du thème

```
web/app/themes/default/
├── app/                     # PHP applicatif (PSR-4 : namespace App\)
│   ├── Blocks/              # Classes de blocs Gutenberg personnalisés
│   ├── PostTypes/           # Classes de types de contenu (ex: Page.php avec meta boxes)
│   ├── Providers/           # Service providers Acorn/Laravel
│   ├── Services/            # Services injectables
│   ├── View/                # View composers et composants Blade
│   ├── filters.php          # Filtres WordPress
│   └── setup.php            # Setup du thème (enqueue, supports, etc.)
├── config/                  # Configuration Acorn
├── public/build/            # Assets compilés (généré par Vite, ne pas modifier)
├── resources/
│   ├── css/                 # SCSS — entry points : app.scss, editor.scss
│   │   └── blocks/          # SCSS par bloc (chargés dynamiquement par Vite)
│   ├── js/                  # JS — entry points : app.js, editor.js
│   │   └── blocks/          # JS par bloc
│   ├── fonts/
│   ├── images/
│   └── views/               # Templates Blade
│       ├── blocks/          # Vues des blocs Gutenberg
│       ├── components/      # Composants Blade réutilisables
│       ├── forms/
│       ├── layouts/         # Layouts principaux (app.blade.php, etc.)
│       ├── partials/        # Partials (header, footer, etc.)
│       ├── sections/
│       └── *.blade.php      # Templates de pages (index, single, page, 404...)
├── vendor/                  # Dépendances Composer du thème
├── theme.json               # Configuration du thème WordPress (couleurs, typo, espacements)
├── vite.config.js           # Configuration Vite
└── functions.php            # Point d'entrée WordPress (bootstrap Acorn)
```

## Commandes de développement

```bash
# Depuis web/app/themes/default/
npm run dev        # Serveur Vite en mode watch (HMR)
npm run build      # Build de production
```

## Vite — Alias de chemins

| Alias | Chemin réel |
|-------|-------------|
| `@scripts` | `resources/js` |
| `@styles` | `resources/css` |
| `@fonts` | `resources/fonts` |
| `@images` | `resources/images` |

## Entrées Vite (entry points)

- `resources/css/app.scss` + `resources/js/app.js` — front-end principal
- `resources/css/editor.scss` + `resources/js/editor.js` — éditeur Gutenberg
- `resources/css/blocks/*.scss` + `resources/js/blocks/*.js` — blocs (scan dynamique)

## Blocs Gutenberg personnalisés

Les blocs sont déclarés dans `app/Blocks/`. La vue Blade correspondante se place dans `resources/views/blocks/`.

## Types de contenu (Post Types)

Les classes dans `app/PostTypes/` gèrent l'enregistrement des CPT et leurs meta boxes. Exemple : `Page.php` ajoute des meta boxes sur le type `page`.

## Linting PHP

```bash
# Depuis web/app/themes/default/
composer lint        # Vérifie avec Laravel Pint
composer lint:fix    # Corrige automatiquement
```

## Traductions

```bash
npm run translate       # Génère le .pot et met à jour les .po
npm run translate:compile  # Compile les .mo et .json
```

## URL locale

`https://sage11default.test` (définie dans `vite.config.js`)

## Documentation interne

| Sujet | Fichier |
|-------|---------|
| Acorn (Service Providers, Blade, DI, Facades, Console, Collections) | [`docs/acorn.md`](docs/acorn.md) |
| Sage 11 (structure, blocs MB Blocks, Vite, View Composers, commandes) | [`docs/sage.md`](docs/sage.md) |

## Dépendances notables

- `roots/acorn` — framework Laravel adapté à WordPress
- `log1x/navi` — gestion des menus de navigation
- `blade-ui-kit/blade-icons` — icônes SVG en composants Blade
- `johnbillion/extended-cpts` — enregistrement simplifié des CPT
- `@splidejs/splide` — slider/carousel JS
