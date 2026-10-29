<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class Carte extends BlockEngine
{
    public static function boot(): void
    {
        add_filter('register_block_type_args', function ($args, $name) {
            if ($name === 'meta-box/carte') {
                $args['attributes']['data']['role'] = 'content';
            }

            return $args;
        }, 10, 2);
    }

    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Carte',
            'id' => 'carte',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'location-alt',
            'description' => 'Carte centrée sur une adresse (fournisseur choisi dans les réglages du thème).',
            'keywords' => ['carte', 'map', 'openstreetmap', 'google maps', 'adresse'],
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('carte', Vite::asset('resources/css/blocks/carte.scss'), [], null);
            },
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'customClassName' => true,
            ],
            'fields' => [
                [
                    'id' => 'adresse',
                    'name' => 'Nom du lieu',
                    'type' => 'text',
                    'desc' => 'Non affiché : sert de titre à la carte pour les lecteurs d\'écran.',
                ],
                [
                    'id' => 'latitude',
                    'name' => 'Latitude',
                    'type' => 'number',
                    'step' => 'any',
                    'required' => true,
                    'desc' => 'Sur openstreetmap.org, clic droit sur le lieu > Afficher l\'adresse : les coordonnées s\'affichent à gauche.',
                ],
                [
                    'id' => 'longitude',
                    'name' => 'Longitude',
                    'type' => 'number',
                    'step' => 'any',
                    'required' => true,
                ],
                [
                    'id' => 'zoom',
                    'name' => 'Zoom',
                    'type' => 'number',
                    'min' => 10,
                    'max' => 18,
                    'std' => 15,
                ],
            ],
        ];

        return $meta_boxes;
    }
}
