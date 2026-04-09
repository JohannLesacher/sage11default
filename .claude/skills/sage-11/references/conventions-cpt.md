# Conventions CPT — retours d'expérience

## 1. Désactiver l'éditeur sur un CPT

Ne jamais masquer le champ titre via CSS (`#titlediv { display: none }`). Agir directement sur `supports`.

- Retirer `editor` de `supports` supprime l'éditeur (Gutenberg et classique)
- Retirer `title` de `supports` supprime le champ titre de l'écran d'édition
- `show_in_rest: false` n'est pas nécessaire pour désactiver Gutenberg si `editor` n'est pas dans `supports`

```php
// CPT purement data-driven (MetaBox), sans éditeur ni titre visible
'supports' => ['thumbnail'],

// CPT avec titre visible mais sans éditeur
'supports' => ['title', 'thumbnail'],
```

---

## 2. Post thumbnail vs champ single_image MetaBox

Utiliser le **post thumbnail natif** pour l'image principale d'un CPT. Réserver `single_image` MetaBox aux cas où l'image est secondaire, rôle spécifique, ou dans un groupe répétable.

| Situation | Approche |
|-----------|----------|
| Image principale / photo de couverture | Post thumbnail (`'thumbnail'` dans `supports`) |
| Plusieurs images avec des rôles distincts | `single_image` ou `image_advanced` MetaBox |
| Image dans un groupe répétable | `single_image` MetaBox |

```php
// config/post-types.php
'supports' => ['title', 'thumbnail'],

// Récupération dans un Composer ou Service
$thumbnail_id  = get_post_thumbnail_id($post_id);
$thumbnail_url = get_the_post_thumbnail_url($post_id, 'large');
$has_thumbnail = has_post_thumbnail($post_id);
```

---

## 3. Auto-générer le titre d'un CPT

Quand le titre doit être construit à partir d'autres champs (et non saisi manuellement), utiliser le filtre `wp_insert_post_data`. Il intercepte la donnée avant insertion en base — pas d'appel récursif, pas de `remove_action` nécessaire.

Les valeurs MetaBox sont disponibles dans `$_POST` au moment où ce filtre s'exécute.

```php
// app/PostTypes/Temoignage.php
public function autoTitle(array $data, array $postarr): array
{
    if ($data['post_type'] !== $this->post_type) {
        return $data;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $data;
    }

    $prenom     = sanitize_text_field($_POST['prenom'] ?? '');
    $nom        = sanitize_text_field($_POST['nom'] ?? '');
    $entreprise = sanitize_text_field($_POST['entreprise'] ?? '');

    $titre = trim("$prenom $nom");
    if ($entreprise) {
        $titre .= " / $entreprise";
    }

    if ($titre) {
        $data['post_title'] = $titre;
        $data['post_name']  = sanitize_title($titre);
    }

    return $data;
}
```

> Le champ `title` peut être retiré de `supports` — le `post_title` est toujours stocké en base et affiché dans le listing admin.

---

## 4. Hooks dans PostTypesServiceProvider

Ne pas faire de méthode générique qui scanne `app/PostTypes/` pour appeler des hooks (ex : `loadPostTypesHooks()`). Préférer **l'injection explicite** des classes PostType dans `boot()` via le container Laravel, et lister les hooks directement à la suite du filtre `rwmb_meta_boxes`.

```php
// app/Providers/PostTypesServiceProvider.php
use App\PostTypes\Temoignage;

public function boot(Temoignage $temoignage): void
{
    add_action('init', function (): void {
        // registration CPT...
    }, 100);

    add_filter('rwmb_meta_boxes', [$this, 'loadPostTypesMetas']);
    add_filter('wp_insert_post_data', [$temoignage, 'autoTitle'], 10, 2);
}
```

**Pourquoi :** le ServiceProvider est le point d'entrée unique pour les hooks — avoir la liste complète ici permet de comprendre d'un coup d'œil ce que fait le thème, sans suivre des appels indirects dans chaque classe PostType.
