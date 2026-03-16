<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Post Types
    |--------------------------------------------------------------------------
    |
    | Post types to be registered with Extended CPTs
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'post_types' => [
        'evenement' => [
            'menu_icon'      => 'dashicons-calendar-alt',
            'supports'       => [ 'title', 'editor', 'thumbnail' ],
            'public'         => true,
            'show_in_rest'   => true,
            'show_in_menu'   => true,
            'show_ui'        => true,
            'has_archive'    => true,
            'menu_position'  => 4,
            'names'          => [
                'singular' => 'Événement',
                'plural'   => 'Événements',
                'slug'     => 'evenements',
            ],
            'labels'         => [
                'singular'                   => 'Événement',
                'plural'                     => 'Événements',
                'menu_name'                  => 'Événements',
                'name'                       => 'Événements',
                'singular_name'              => 'Événement',
                'name_admin_bar'             => 'Événement',
                'search_items'               => 'Rechercher des événements',
                'popular_items'              => 'Événements populaires',
                'all_items'                  => 'Tous les événements',
                'archives'                   => 'Archives des événements',
                'parent_item'                => 'Événement parent',
                'parent_item_colon'          => 'Événement parent :',
                'edit_item'                  => 'Modifier l\'événement',
                'view_item'                  => 'Voir l\'événement',
                'update_item'                => 'Mettre à jour',
                'add_new_item'               => 'Ajouter un événement',
                'new_item_name'              => 'Nom du nouvel événement',
                'separate_items_with_commas' => 'Séparer par des virgules',
                'add_or_remove_items'        => 'Ajouter ou supprimer',
                'choose_from_most_used'      => 'Choisir parmi les plus utilisés',
                'not_found'                  => 'Aucun événement trouvé',
                'no_terms'                   => 'Aucun événement',
                'filter_by_item'             => 'Filtrer par événement',
                'most_used'                  => 'Plus utilisés',
                'back_to_items'              => '&larr; Retour aux événements',
                'item_link'                  => 'Lien de l\'événement',
                'item_link_description'      => 'Le lien vers l\'événement.',
                'no_item'                    => 'Aucun événement',
                'filter_by'                  => 'Filtrer par',
            ],
            'admin_cols'     => [
                'date_event' => [
                    'title'    => 'Date de l\'événement',
                    'meta_key' => 'date_event',
                    'function' => function () {
                        global $post;
                        $date = get_post_meta( $post->ID, 'date_event', true );
                        if ( $date ) {
                            $date_obj = DateTime::createFromFormat( 'Ymd', $date );
                            echo $date_obj ? $date_obj->format( 'd/m/Y' ) : esc_html( $date );
                        }
                    },
                ],
            ],
            'site_sortables' => [
                'date_event' => [
                    'meta_key' => 'date_event',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Taxonomies
    |--------------------------------------------------------------------------
    |
    | Taxonomies to be registered with Extended CPTs library
    | <https://github.com/johnbillion/extended-cpts>
    |
    */

    'taxonomies' => [
    ],
];
