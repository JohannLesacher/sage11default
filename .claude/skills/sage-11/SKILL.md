---
name: sage-11
description: >
  Référence complète pour le développement de thèmes WordPress avec la stack
  Roots : Sage 11, Acorn, Bedrock, MetaBox (MB Blocks), Polylang, WooCommerce.
  Utiliser ce skill pour toute tâche liée à ce projet : créer un bloc Gutenberg,
  un CPT, un View Composer, un Service Provider, un Service, modifier theme.json,
  gérer les assets Vite, écrire des requêtes WP_Query, intégrer WooCommerce ou
  Polylang, ou déboguer des problèmes courants. Déclencher dès que la conversation
  mentionne Sage, Acorn, MB Blocks, MetaBox, Roots, ou une tâche WordPress sur ce projet.
---

# Sage 11 — Skill de développement

## Conventions globales

- PHP : PSR-12, tout typer (paramètres et retours)
- Pas de commentaires superflus — le code doit se lire seul
- Zéro logique dans les vues Blade — tout passe par les View Composers ou les Services
- Jamais `{{ }}` pour afficher du texte — toujours `{!! !!}` (problèmes d'encodage avec les accents français)
- Pas de `@php` dans les vues
- Hooks WordPress déclarés dans les Service Providers, jamais dans `functions.php`
- `get_post_meta()` plutôt que les helpers MetaBox (`rwmb_meta`, `rwmb_get_value`)

---

## Structure du thème

```
web/app/themes/default/
├── app/
│   ├── Blocks/                  # Blocs Gutenberg (MB Blocks)
│   ├── PostTypes/               # Meta boxes par post type (ex: Page.php)
│   ├── Providers/               # Service Providers Acorn/Laravel
│   ├── Services/                # Services métier (logique, hooks)
│   │   └── BlockEngine.php      # Classe abstraite de rendu des blocs
│   ├── View/
│   │   ├── Composers/           # View Composers (+ sous-dossier Blocks/)
│   │   └── Components/
│   ├── translations.php         # pll_register_string() si Polylang actif
│   └── setup.php                # after_setup_theme, enqueues globaux
├── config/
│   └── post-types.php           # Déclaration CPT et taxonomies
├── public/build/                # Assets compilés Vite
├── resources/
│   ├── css/
│   │   ├── app.scss
│   │   ├── editor.scss
│   │   ├── config/              # variables, reset, fonts, external
│   │   ├── base/                # global, layout, animation
│   │   ├── sections/            # header, footer
│   │   ├── templates/           # CSS par template (ex: woocommerce.scss)
│   │   └── blocks/              # Un .scss par bloc (scan Vite auto)
│   ├── js/
│   │   ├── app.js
│   │   ├── editor.js
│   │   └── blocks/              # Un .js par bloc (scan Vite auto, optionnel)
│   └── views/
│       ├── blocks/
│       ├── components/
│       ├── layouts/
│       ├── partials/
│       ├── sections/
│       └── woocommerce/         # Si WooCommerce actif
├── theme.json
├── vite.config.js
└── functions.php
```

---

## 1. Post Types et Taxonomies

### Config : `config/post-types.php`

Les CPT et taxonomies sont déclarés dans ce fichier de config, via la librairie [extended-cpts](https://github.com/johnbillion/extended-cpts).

```php
return [
    'post_types' => [
        'evenement' => [
            'menu_icon'     => 'dashicons-calendar-alt',
            'supports'      => ['title', 'editor', 'thumbnail'],
            'public'        => true,
            'show_in_rest'  => true,
            'has_archive'   => true,
            'menu_position' => 4,
            'names' => [
                'singular' => 'Événement',
                'plural'   => 'Événements',
                'slug'     => 'evenements',
            ],
            'admin_cols' => [
                'date_event' => [
                    'title'    => "Date de l'événement",
                    'meta_key' => 'date_event',
                    'function' => function () {
                        global $post;
                        $date = get_post_meta($post->ID, 'date_event', true);
                        if ($date) {
                            $date_obj = DateTime::createFromFormat('Ymd', $date);
                            echo $date_obj ? $date_obj->format('d/m/Y') : esc_html($date);
                        }
                    },
                ],
            ],
        ],
    ],
    'taxonomies' => [],
];
```

### Service Provider : `PostTypesServiceProvider`

- Lit `config/post-types.php` et appelle `register_extended_post_type()` / `register_extended_taxonomy()`
- Scanne `app/PostTypes/` et appelle `addMetas()` sur chaque classe via le filtre `rwmb_meta_boxes`

### Meta boxes : `app/PostTypes/<PostType>.php`

```php
namespace App\PostTypes;

class Page
{
    private string $post_type = 'page';

    public function addMetas(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title'      => 'Réglages',
            'post_types' => $this->post_type,
            'fields'     => [
                [
                    'id'   => 'display_title',
                    'name' => 'Afficher le titre de la page ?',
                    'type' => 'checkbox',
                    'std'  => 1,
                ],
            ],
        ];

        return $meta_boxes;
    }
}
```

> **Important :** après tout ajout ou modification de CPT ou taxonomie, vider les permaliens : `wp rewrite flush` ou dans l'admin → Réglages → Permaliens → Enregistrer.

---

## 2. Blocs Gutenberg (MB Blocks)

Les blocs utilisent **MetaBox MB Blocks**, pas `block.json`. ACF n'est pas utilisé.

### Créer un bloc — TOUJOURS passer par la commande

> **Règle absolue :** ne jamais créer les fichiers d'un bloc manuellement. Toujours utiliser `wp acorn make:block` — c'est déterministe, ça génère la structure correcte et ça évite les erreurs de registration.
```bash
wp acorn make:block nom-du-bloc          # CSS uniquement
wp acorn make:block nom-du-bloc --js     # Avec JS
wp acorn make:block nom-du-bloc --vc     # Avec View Composer
wp acorn make:block nom-du-bloc --js --vc
```

La commande génère : classe PHP, vue Blade, SCSS, JS (optionnel), Composer (optionnel). Aucune registration manuelle nécessaire.

**Workflow correct pour créer un bloc :**
1. Exécuter `wp acorn make:block nom-du-bloc [--js] [--vc]` via bash
2. Modifier les fichiers générés pour ajouter champs, vue, CSS. Le but est d'avoir un bloc prêt à l'emploi.
3. Ne jamais créer les fichiers manuellement, même si bash n'est pas disponible — demander à l'utilisateur d'exécuter la commande et attendre sa confirmation

### Anatomie d'une classe de bloc

```php
namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class MonBloc extends BlockEngine
{
    public static function register_block(array $meta_boxes): array
    {
        $meta_boxes[] = [
            'title'           => 'Mon Bloc',
            'id'              => 'mon-bloc',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'icon'            => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets'  => function () {
                wp_enqueue_style('mon-bloc', Vite::asset('resources/css/blocks/mon-bloc.scss'), [], null);
                if (!is_admin()) {
                    wp_enqueue_script_module('mon-bloc-js', Vite::asset('resources/js/blocks/mon-bloc.js'), [], null);
                }
            },
            'supports' => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
            ],
            'fields' => [],
        ];

        return $meta_boxes;
    }
}
```

> `wp_enqueue_script_module` (ES modules natifs) est toujours wrappé dans `!is_admin()` pour éviter les conflits éditeur.

### Vue Blade

Pas de commentaire de chemin en haut du fichier — c'est inutile.

```blade
<section class="block-mon-bloc {{ $attributes['className'] ?? '' }}">
    {{-- $data = champs MetaBox, $attributes = attrs Gutenberg, $is_preview = bool --}}
</section>
```

### InnerBlocks vs champs MetaBox

> **`<InnerBlocks />` est le choix par défaut pour tout contenu éditorial.** Le champ `wysiwyg` MetaBox est à proscrire — il isole le contenu de l'expérience Gutenberg et prive le rédacteur des blocs natifs.

| Situation | Approche |
|-----------|----------|
| Tout contenu que le rédacteur composerait avec des blocs | `<InnerBlocks />` |
| Texte court non mis en forme (titre, label, slug…) | Champ `text` MetaBox |
| URL, lien externe | Champ `url` MetaBox |
| Données structurées non-éditoriales (options, coordonnées…) | Champ MetaBox adapté |

**Exemples concrets :**
```
Bloc Card (titre + contenu libre + CTA) :
❌ titre (text) + contenu (wysiwyg) + cta_label (text) + cta_url (url)
✓  titre (text) + <InnerBlocks /> pour le contenu + cta_label (text) + cta_url (url)

Bloc Hero (visuel + texte + bouton) :
❌ image (single_image) + texte (wysiwyg) + bouton (text + url)
✓  image (single_image) + <InnerBlocks /> pour le texte et le bouton
```

**Règle simple :** doute → `<InnerBlocks />`. Le champ `wysiwyg` n'a quasiment aucun cas d'usage légitime.

---

## 3. Champs MetaBox

> **Référence complète :** lire `references/metabox-fields.md` pour la liste des 40+ types de champs, leurs settings et les patterns de récupération. À charger dès qu'une tâche implique de déclarer ou lire des champs MetaBox.

Types les plus courants : `text`, `textarea`, `checkbox`, `select`, `select_advanced`, `date`, `single_image`, `image_advanced`, `post`, `group` (extension MB Group requise).

---

## 4. Fichiers de configuration (`config/`)

Acorn expose le dossier `config/` du thème via le helper `config()`, comme dans Laravel. On peut y créer autant de fichiers que nécessaire.

### Fichiers existants

| Fichier | Usage |
|---------|-------|
| `config/post-types.php` | Déclaration CPT et taxonomies (lu par `PostTypesServiceProvider`) |
| `config/app.php` | Valeurs générales du thème (nom, debug, etc.) |
| Tout autre fichier | Accessible via `config('nom-fichier.cle')` |

### Créer un fichier de config

```php
// config/mon-config.php
return [
    'api_key' => env('MON_API_KEY', ''),
    'option'  => true,
];
```

### Lire une valeur

```php
config('mon-config.api_key')
config('mon-config.option', false) // avec valeur par défaut
config('app.name')
```

### Cas d'usage typiques

- Options globales du thème qui dépendent de variables d'environnement (`.env`)
- Constantes de configuration partagées entre plusieurs Services
- Tout ce qui ne mérite pas une option WordPress mais doit être configurable par projet

---

## 5. View Composers

Auto-découverts depuis `app/View/Composers/`. Les Composers de blocs sont dans `app/View/Composers/Blocks/`.

### Conventions

- Toute la logique de données est dans le Composer, jamais dans la vue
- Accès aux données MetaBox via `get_post_meta()`, pas les helpers rwmb
- Accès aux données du bloc : `$this->view->getData()['data'][...]`
- Avec Acorn dernière version (Laravel récent) : déclarer des méthodes publiques directement, sans `with()`. Les méthodes sont appelables dans la vue comme `$maMethode()`

### Composer standard

```php
namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    protected static $views = ['partials.content', 'partials.page-header'];

    public function shouldDisplayTitle(): bool
    {
        if (is_page()) {
            return (bool) get_post_meta(get_the_ID(), 'display_title', true);
        }

        return true;
    }
}
```

### Composer de bloc (accès aux champs MetaBox)

```php
namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class MonBloc extends Composer
{
    protected static $views = ['blocks.mon-bloc'];

    public function membres(): array
    {
        return $this->view->getData()['data']['membres'] ?? [];
    }
}
```

```blade
@foreach($membres() as $membre)
    <div>{{ $membre['nom'] }}</div>
@endforeach
```

### Composer avec `with()` (ancienne syntaxe, toujours valide)

```php
public function with(): array
{
    return [
        'images' => $this->view->getData()['data']['images'][0] ?? [],
    ];
}
```

---

## 6. Services et Service Providers

Pattern systématique : un **Service** contient la logique, un **ServiceProvider** enregistre les hooks et injecte le Service via le container.

### Service Provider

```php
namespace App\Providers;

use App\Services\MonService;
use Illuminate\Support\ServiceProvider;

class MonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MonService::class, fn() => new MonService());
    }

    public function boot(MonService $service): void
    {
        add_action('init', [$service, 'maMethode']);
        add_filter('mon_filtre', [$service, 'autreMethode'], 10, 2);
    }
}
```

### Service

```php
namespace App\Services;

class MonService
{
    public function maMethode(): void
    {
        // logique
    }
}
```

### Enregistrement dans `functions.php`

```php
Application::configure()
    ->withProviders([
        ThemeServiceProvider::class,
        PostTypesServiceProvider::class,
        BlockServiceProvider::class,
        MonServiceProvider::class,
    ])
    ->boot();
```

> `ThemeServiceProvider` étend `SageServiceProvider` d'Acorn — ne rien y mettre de spécifique, il bootstrappe Acorn.

---

## 7. Assets (Vite)

### Entry points

| Fichier | Usage |
|---------|-------|
| `resources/css/app.scss` | CSS front principal |
| `resources/js/app.js` | JS front principal |
| `resources/css/editor.scss` | Styles éditeur Gutenberg |
| `resources/js/editor.js` | Scripts éditeur Gutenberg |
| `resources/css/blocks/*.scss` | CSS par bloc (scan auto) |
| `resources/js/blocks/*.js` | JS par bloc (scan auto) |

### `vite.config.js`

```js
import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import { wordpressPlugin } from '@roots/vite-plugin'
import { globSync } from 'glob'

if (!process.env.APP_URL) {
  process.env.APP_URL = 'https://monsite.test'
}

export default defineConfig({
  base: '/app/themes/default/public/build/',
  plugins: [
    laravel({
      input: [
        'resources/css/app.scss',
        'resources/js/app.js',
        'resources/css/editor.scss',
        'resources/js/editor.js',
        ...globSync('resources/{css,js}/blocks/*.{scss,js}'),
      ],
      refresh: true,
    }),
    wordpressPlugin(),
  ],
  resolve: {
    alias: {
      '@scripts': '/resources/js',
      '@styles':  '/resources/css',
      '@fonts':   '/resources/fonts',
      '@images':  '/resources/images',
    },
  },
})
```

### Enqueues conditionnels (setup.php)

```php
// CSS/JS global → géré par setup.php via Vite
// CSS/JS par bloc → géré dans enqueue_assets du bloc (chargé uniquement si le bloc est présent)

// Enqueue conditionnel hors bloc :
add_action('wp_enqueue_scripts', function () {
    if (has_block('woocommerce/cart') || is_cart()) {
        wp_enqueue_script_module('cart-js', Vite::asset('resources/js/cart.js'), [], null);
    }
});
```

### Commandes

```bash
npm run dev      # Dev avec HMR
npm run build    # Build production
```

---

## 8. theme.json

`theme.json` est édité manuellement (non généré par Vite).

**Principes :**
- `defaultPalette: false` et `defaultFontSizes: false` → désactive les presets WordPress par défaut
- `defaultGradients: false`, `defaultDuotone: false` → idem
- `appearanceTools: true` → active les outils de mise en page Gutenberg
- `fluid: false` au niveau global, avec `fluid: { min, max }` uniquement sur les grandes tailles
- Les variables CSS sont générées automatiquement par WordPress sur `:root`

### Variables CSS générées

Pattern : `--wp--preset--{category}--{slug}`

```scss
.mon-element {
    color: var(--wp--preset--color--bordeaux);
    font-size: var(--wp--preset--font-size--large);
    font-family: var(--wp--preset--font-family--instrument);
}
```

Toujours utiliser ces variables CSS (pas de valeurs hardcodées) pour les couleurs, tailles de police et familles.

### Structure minimale

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": true,
    "layout": { "contentSize": "720px", "wideSize": "816px" },
    "color": {
      "defaultPalette": false,
      "defaultGradients": false,
      "defaultDuotone": false,
      "palette": [
        { "slug": "noir", "color": "#000000", "name": "Noir" }
      ]
    },
    "typography": {
      "defaultFontSizes": false,
      "customFontSize": true,
      "fluid": false,
      "fontFamilies": [
        { "name": "Inter", "slug": "inter", "fontFamily": "Inter, sans-serif" }
      ],
      "fontSizes": [
        { "name": "Large", "slug": "large", "size": "36px", "fluid": { "min": "24px", "max": "36px" } }
      ]
    }
  }
}
```

---

## 9. Polylang (optionnel)

Polylang n'est pas toujours actif. Ne l'utiliser que si le projet est multilingue.

### Enregistrer les chaînes traduisibles

```php
// app/translations.php
if (function_exists('pll_register_string')) {
    pll_register_string('slogan', 'Là où les idées prennent force.', 'theme');
}
```

### Dans les vues

```blade
{{-- Toujours {!! !!} pour éviter les problèmes d'encodage --}}
{!! pll__('Voir tous les événements') !!}
```

### Dans les Composers / Services

```php
pll__('Suivant &raquo;')
pll_current_language()   // 'fr' | 'en'
pll_home_url()           // URL de base de la langue courante
```

### WP_Query avec Polylang

Les requêtes `WP_Query` et `get_posts` fonctionnent sans configuration particulière — Polylang filtre automatiquement par langue courante.

---

## 10. WooCommerce (optionnel)

WooCommerce n'est pas toujours actif. Utiliser le package [sage-woocommerce](https://github.com/generoi/sage-woocommerce).

### Structure

```
resources/views/woocommerce/
├── archive-product.blade.php
├── single-product.blade.php
└── content-product-custom-image.blade.php
resources/css/templates/
├── woocommerce.scss
├── woocommerce-single-product.scss
└── woocommerce-account.scss
app/Providers/WooCommerceServiceProvider.php
app/Services/WooCommerceShop.php
app/Services/WooCommerceSingleProduct.php
app/Services/WooCommerceAccount.php
```

### Service Provider pattern

```php
class WooCommerceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(WooCommerceShop::class, fn() => new WooCommerceShop());
    }

    public function boot(WooCommerceShop $shop, WooCommerceSingleProduct $single): void
    {
        add_action('init', [$shop, 'removeWrappers'], 1, 2);
        add_action('init', [$shop, 'removeBreadcrumbs'], 1, 2);
        add_action('wp_enqueue_scripts', [$shop, 'removeWooCommerceStyles'], 1, 2);
        add_action('wp_enqueue_scripts', [$shop, 'addWooCommerceStyles'], 1, 2);
        // ...
    }
}
```

### Bonnes pratiques WooCommerce

- Supprimer les styles WooCommerce par défaut : `add_filter('woocommerce_enqueue_styles', '__return_empty_array')`
- Supprimer les wrappers natifs et les recréer en Blade
- Enqueuer les CSS WooCommerce via Vite (`resources/css/templates/woocommerce.scss`)
- Activer les supports dans `setup.php` : `wc-product-gallery-zoom`, `wc-product-gallery-lightbox`, `wc-product-gallery-slider`
- Utiliser `view('woocommerce.mon-template', [...])→render()` pour les templates custom dans les hooks WooCommerce

---

## 11. WP_Query

Utiliser `get_posts()` pour des listes simples, `WP_Query` quand on a besoin de la pagination ou des métadonnées de résultat (`max_num_pages`, etc.).

### Conventions

- `'fields' => 'ids'` quand on n'a besoin que des IDs (perf)
- Les dates MetaBox stockées en `Ymd` se comparent en `NUMERIC`
- Décomposer les meta_query complexes en méthodes privées

### Exemple : query avec filtres et meta_query imbriquée

```php
private function getConcertIds(array $filters): array
{
    return get_posts([
        'post_type'      => 'concert',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value_num',
        'meta_key'       => 'date',
        'order'          => 'ASC',
        'fields'         => 'ids',
        'meta_query'     => $this->buildMetaQuery($filters),
    ]);
}

private function buildMetaQuery(array $filters): array
{
    $metaQuery = [
        'relation' => 'AND',
        [
            'relation' => 'OR',
            ['key' => 'date',     'value' => date('Ymd'), 'compare' => '>=', 'type' => 'NUMERIC'],
            ['key' => 'date_fin', 'value' => date('Ymd'), 'compare' => '>=', 'type' => 'NUMERIC'],
        ],
    ];

    if ($filters['zone'] && $filters['zone'] !== 'all') {
        $metaQuery[] = ['key' => 'zone', 'value' => $filters['zone'], 'compare' => '='];
    }

    return $metaQuery;
}
```

### Exemple : WP_Query avec pagination dans un Composer

```php
private WP_Query $query;

public function with(): array
{
    $paged = get_query_var('paged') ?: (get_query_var('page') ?: 1);

    $this->query = new WP_Query([
        's'              => get_query_var('search_term'),
        'posts_per_page' => 12,
        'paged'          => $paged,
        'post_type'      => ['post', 'campagne'],
    ]);

    return [
        'results'    => $this->getResults(),
        'pagination' => $this->getPagination(),
    ];
}
```

---

## 12. Troubleshooting

### Cache Acorn

Vider le cache après : ajout d'un Provider, d'un Composer, modification de config.

```bash
wp acorn optimize:clear   # Vide tout (config, views, routes) — à privilégier
wp acorn cache:clear      # Cache applicatif uniquement
wp acorn view:clear       # Cache Blade uniquement
```

### Permaliens

Après tout ajout ou modification de CPT, taxonomie ou règle de réécriture :

```bash
wp rewrite flush
# Ou : admin → Réglages → Permaliens → Enregistrer
```

### Vite HMR ne fonctionne pas

Vérifier que `APP_URL` est défini dans `.env` et correspond au vhost local. Vite en a besoin pour le proxy HMR.

```bash
APP_URL=https://monsite.test
```

### Encodage / accents

Ne jamais utiliser `{{ }}` pour du texte — toujours `{!! !!}`. Les doubles accolades échappent les entités HTML ce qui casse les accents et caractères spéciaux français.

```blade
{{-- ❌ --}}
{{ $titre }}

{{-- ✓ --}}
{!! $titre !!}
```

### `wp_enqueue_script_module` dans les blocs

Toujours wrapper dans `!is_admin()` pour éviter les conflits avec l'éditeur Gutenberg :

```php
'enqueue_assets' => function () {
    wp_enqueue_style('mon-bloc', Vite::asset('resources/css/blocks/mon-bloc.scss'), [], null);
    if (!is_admin()) {
        wp_enqueue_script_module('mon-bloc-js', Vite::asset('resources/js/blocks/mon-bloc.js'), [], null);
    }
},
```

### Composer ou Provider non pris en compte

1. Vérifier que le Provider est bien enregistré dans `functions.php`
2. Vider le cache Acorn : `wp acorn optimize:clear`
3. Pour un Composer : vérifier que `$views` correspond exactement au nom de la vue Blade (`blocks.mon-bloc` pour `resources/views/blocks/mon-bloc.blade.php`)
