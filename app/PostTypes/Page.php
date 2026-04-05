<?php

namespace App\PostTypes;

class Page {
    private string $post_type = 'page';

    public function addMetas( $meta_boxes ) {
        $meta_boxes[] = [
            'title'      => 'Réglages',
            'post_types' => $this->post_type,
            'fields'     => [
                [
                    'id'      => 'display_title',
                    'name'    => 'Afficher le titre de la page ?',
                    'type'    => 'checkbox',
                    'std'     => 1,
                ],
            ],
        ];

        return $meta_boxes;
    }
}
