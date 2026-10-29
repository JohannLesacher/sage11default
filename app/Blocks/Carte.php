<?php

namespace App\Blocks;

use App\Blocks\Base\MetaBoxBlock;

class Carte extends MetaBoxBlock
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

    protected static function definition(): array
    {
        return [
            'title' => 'Carte',
            'context' => 'normal',
            'icon' => 'location-alt',
            'description' => 'Carte centrée sur une adresse (fournisseur choisi dans les réglages du thème).',
            'keywords' => ['carte', 'map', 'openstreetmap', 'google maps', 'adresse'],
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
    }
}
