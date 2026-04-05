# Sage 11 — Guide de développement du thème

## Structure du thème

```
web/app/themes/default/
├── app/                         # PHP applicatif (PSR-4 : namespace App\)
│   ├── Blocks/                  # Blocs Gutenberg (MB Blocks via MetaBox)
│   ├── Console/
│   │   └── Commands/            # Commandes Acorn WP-CLI
│   │       ├── MakeBlockCommand.php   # wp acorn make:block
│   │       └── MakeHeaderCommand.php  # wp acorn make:header
│   ├── PostTypes/               # CPT et meta boxes (ex: Page.php)
│   ├── Providers/               # Service Providers Acorn/Laravel
│   │   ├── BlockServiceProvider.php   # Auto-enregistrement des blocs
│   │   ├── PostTypesServiceProvider.php
│   │   └── ThemeServiceProvider.php
│   ├── Services/
│   │   └── BlockEngine.php      # Classe abstraite de rendu des blocs
│   ├── View/
│   │   ├── Composers/           # View Composers (+ sous-dossier Blocks/)
│   │   └── Components/          # Blade Components
│   ├── filters.php              # Filtres WordPress
│   └── setup.php                # Setup du thème (enqueue, supports…)
├── config/                      # Configuration Acorn
├── public/build/                # Assets compilés par Vite (ne pas modifier)
├── resources/
│   ├── css/                     # SCSS
│   │   └── blocks/              # Un fichier .scss par bloc
│   ├── js/                      # JavaScript (ESModules)
│   │   └── blocks/              # Un fichier .js par bloc (optionnel)
│   ├── fonts/
│   ├── images/
│   └── views/                   # Templates Blade
│       ├── blocks/              # Vues des blocs (slug.blade.php)
│       ├── components/
│       ├── forms/
│       ├── layouts/
│       ├── partials/
│       └── sections/
├── vite.config.js               # Configuration Vite
└── functions.php                # Bootstrap Acorn
```

## Compilation des assets (Vite)

Ce thème utilise **Vite** (pas Bud).

```bash
# Depuis web/app/themes/default/
npm run dev      # Serveur de développement avec HMR
npm run build    # Build de production
```

### Entry points

| Fichier | Usage |
|---------|-------|
| `resources/css/app.scss` | CSS front-end principal |
| `resources/js/app.js` | JS front-end principal |
| `resources/css/editor.scss` | Styles injectés dans l'éditeur Gutenberg |
| `resources/js/editor.js` | Scripts de l'éditeur Gutenberg |
| `resources/css/blocks/*.scss` | CSS par bloc (scan dynamique) |
| `resources/js/blocks/*.js` | JS par bloc (scan dynamique, optionnel) |

### Alias de chemins

| Alias | Chemin |
|-------|--------|
| `@scripts` | `resources/js` |
| `@styles` | `resources/css` |
| `@fonts` | `resources/fonts` |
| `@images` | `resources/images` |

## Blocs Gutenberg (MB Blocks)

Les blocs utilisent **MetaBox (MB Blocks)** et non le système `block.json` natif. ACF n'est PAS utilisé.

### Architecture

- `app/Services/BlockEngine.php` — classe abstraite qui fournit `renderBlock()` ; résout automatiquement la vue Blade `blocks.<slug>` et lui passe `$data`, `$attributes`, `$is_preview`
- `app/Providers/BlockServiceProvider.php` — scanne automatiquement `app/Blocks/` et enregistre toutes les classes via le filtre `rwmb_meta_boxes`
- `app/Blocks/<NomBloc>.php` — classe du bloc, étend `BlockEngine`, déclare ses champs et ses assets

### Créer un bloc avec la commande

```bash
# Bloc simple (CSS uniquement)
wp acorn make:block nom-du-bloc

# Avec un fichier JS
wp acorn make:block nom-du-bloc --js

# Avec un View Composer dédié
wp acorn make:block nom-du-bloc --vc

# Combiné
wp acorn make:block nom-du-bloc --js --vc
```

La commande génère automatiquement :
1. `app/Blocks/<NomBloc>.php` — classe PHP avec `register_block()`
2. `resources/views/blocks/<slug>.blade.php` — vue Blade
3. `resources/css/blocks/<slug>.scss` — feuille de style
4. *(si `--js`)* `resources/js/blocks/<slug>.js`
5. *(si `--vc`)* `app/View/Composers/Blocks/<NomBloc>.php`

Le bloc est **auto-enregistré** par `BlockServiceProvider` sans modification manuelle.

### Anatomie d'une classe de bloc

```php
namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class MonBloc extends BlockEngine
{
    public static function register_block($meta_boxes)
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
                // wp_enqueue_script_module('mon-bloc', Vite::asset('resources/js/blocks/mon-bloc.js'), [], null);
            },
            'supports' => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
                'reusable'        => true,
            ],
            'fields' => [
                // Champs MetaBox
            ],
        ];

        return $meta_boxes;
    }
}
```

### Vue Blade d'un bloc

```blade
{{-- resources/views/blocks/mon-bloc.blade.php --}}
<article class="block-mon-bloc {{ $attributes['className'] ?? '' }}">
    {{-- $data contient les valeurs des champs MetaBox --}}
    <InnerBlocks />
</article>
```

Variables disponibles dans la vue :
- `$data` — valeurs des champs (`rwmb_get_value()` déjà résolu)
- `$attributes` — attributs Gutenberg (`className`, `align`…)
- `$is_preview` — booléen, `true` en mode prévisualisation éditeur

## Blade Templates

### Layouts

```blade
{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html @php(language_attributes())>
  <head>
    @include('partials.head')
  </head>
  <body @php(body_class())>
    @include('sections.header')
    <main>
      @yield('content')
    </main>
    @include('sections.footer')
  </body>
</html>
```

### Page template

```blade
{{-- resources/views/page.blade.php --}}
@extends('layouts.app')

@section('content')
  @while(have_posts()) @php(the_post())
    @include('partials.page-header')
    @include('partials.content-page')
  @endwhile
@endsection
```

## View Composers

```php
namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Navigation extends Composer
{
    protected static $views = ['partials.navigation'];

    public function with(): array
    {
        return [
            'primaryMenu' => $this->primaryMenu(),
        ];
    }

    protected function primaryMenu(): array
    {
        return wp_get_nav_menu_items('primary') ?: [];
    }
}
```

Les composers sont **auto-découverts** depuis `app/View/Composers/`.

## Commandes Acorn disponibles

| Commande | Options | Description |
|----------|---------|-------------|
| `wp acorn make:block <name>` | `--js`, `--vc` | Génère un bloc MB Blocks complet |
| `wp acorn make:header` | `--fixed`, `--align=`, `--lang`, `--cta` | Scaffold du View Composer Header |

## Bonnes pratiques

1. Créer les blocs via `wp acorn make:block` — ne jamais écrire la structure à la main
2. Utiliser `BlockEngine::renderBlock()` comme `render_callback` — ne pas dupliquer la logique de rendu
3. Utiliser les View Composers pour la logique de données, pas les templates Blade
4. Déclarer les hooks WordPress dans les Service Providers, pas dans `functions.php`
5. Suivre PSR-12, tout typer (paramètres et retours)
