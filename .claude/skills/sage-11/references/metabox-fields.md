# MetaBox — Référence des types de champs

## Champs de base (UI WordPress native)

| Type | Clé | Usage |
|------|-----|-------|
| Checkbox | `checkbox` | Oui/Non |
| Checkbox list | `checkbox_list` | Choix multiples |
| Radio | `radio` | Choix unique |
| Select | `select` | Dropdown simple ou multiple |
| Text | `text` | Texte ligne unique |
| Textarea | `textarea` | Texte multiligne |

## Champs avancés

| Type | Clé | Usage |
|------|-----|-------|
| Color picker | `color` | Couleur |
| Date picker | `date` | Date (stockée en `Ymd` par défaut) |
| Datetime picker | `datetime` | Date + heure |
| Time picker | `time` | Heure |
| Select advanced | `select_advanced` | Dropdown Select2 |
| Switch | `switch` | Toggle on/off style iOS |
| WYSIWYG editor | `wysiwyg` | Éditeur WordPress — **à éviter**, préférer `<InnerBlocks />` |
| Button group | `button_group` | Sélection via boutons |
| Image select | `image_select` | Sélection par image |
| Slider | `slider` | Valeur numérique par glisser |
| oEmbed | `oembed` | YouTube, Vimeo, etc. |
| Key Value | `key_value` | Paires clé/valeur libres |
| Hidden | `hidden` | Valeur cachée |
| Custom HTML | `custom_html` | HTML statique dans l'interface admin |

## Champs HTML5 (UI navigateur, sans librairie)

> UI variable selon OS/navigateur — à utiliser avec précaution.

| Type | Clé | Usage |
|------|-----|-------|
| Email | `email` | Adresse e-mail |
| Number | `number` | Nombre |
| URL | `url` | URL |
| Range | `range` | Slider natif navigateur |

## Champs WordPress

| Type | Clé | Usage |
|------|-----|-------|
| Post | `post` | Sélection d'un post |
| User | `user` | Sélection d'un utilisateur |
| Taxonomy | `taxonomy` | Sélection de termes (assigne les termes au post) |
| Taxonomy advanced | `taxonomy_advanced` | Sélection de termes (stocke les IDs en meta) |

## Champs upload

| Type | Clé | Usage |
|------|-----|-------|
| Single image | `single_image` | Une image (media popup) — retourne un ID |
| Image advanced | `image_advanced` | Galerie d'images (media popup) — retourne un tableau d'IDs |
| Image upload | `image_upload` | Galerie par drag & drop |
| File advanced | `file_advanced` | Fichiers multiples (media popup) |
| File upload | `file_upload` | Fichiers par drag & drop |
| File input | `file_input` | URL de fichier + sélection media |
| Video | `video` | Vidéos multiples |

## Champs de mise en page (UI uniquement)

| Type | Clé | Usage |
|------|-----|-------|
| Group | `group` | Groupe imbriqué de champs — nécessite l'extension MB Group |
| Divider | `divider` | Séparateur horizontal |
| Heading | `heading` | Titre de section |
| Tab | `tab` | Onglets — nécessite l'extension MB Tabs |

## Settings communs utiles

```php
[
    'id'           => 'mon_champ',
    'name'         => 'Mon champ',
    'type'         => 'text',
    'desc'         => 'Description affichée sous le champ',
    'std'          => 'Valeur par défaut',
    'placeholder'  => 'Placeholder',
    'required'     => true,
    'clone'        => true,        // Champ répétable
    'sort_clone'   => true,        // Drag & drop des clones
    'max_clone'    => 5,           // Limite de répétitions
    'clone_as_multiple' => false,  // Stocker les clones en plusieurs lignes DB
]
```

## Récupération des valeurs dans les Composers

```php
// Valeur simple
get_post_meta(get_the_ID(), 'mon_champ', true)

// Image single_image (retourne un ID d'attachment)
$image_id = get_post_meta(get_the_ID(), 'mon_image', true);
wp_get_attachment_image($image_id, 'large')

// Champ clonable (retourne un tableau)
$items = get_post_meta(get_the_ID(), 'items', true);

// Depuis un bloc (dans un Composer de bloc)
$data = $this->view->getData()['data'];
$image_ids = $data['images'][0]; // single_image dans un bloc retourne un tableau
```
