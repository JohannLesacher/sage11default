<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class KeyFigure extends BlockEngine
{
    public static function boot(): void
    {
        add_filter('register_block_type_args', function ($args, $name) {
            if ($name === 'meta-box/key-figure') {
                $args['attributes']['valueColor'] = ['type' => 'string'];
                $args['attributes']['customValueColor'] = ['type' => 'string'];
            }

            return $args;
        }, 10, 2);
    }

    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Chiffre clé',
            'id' => 'key-figure',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'side',
            'icon' => 'chart-bar',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('key-figure', Vite::asset('resources/css/blocks/key-figure.scss'), [], null);
            },
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'customClassName' => true,
                'color' => [
                    'text' => true,
                    'background' => true,
                ],
                'spacing' => [
                    'padding' => true,
                ],
                'typography' => [
                    'textAlign' => true,
                ],
            ],
            'fields' => [
                [
                    'id' => 'prefixe',
                    'name' => 'Préfixe',
                    'type' => 'text',
                    'placeholder' => '+',
                ],
                [
                    'id' => 'valeur',
                    'name' => 'Valeur',
                    'type' => 'text',
                    'placeholder' => '120',
                ],
                [
                    'id' => 'unite',
                    'name' => 'Unité',
                    'type' => 'text',
                    'placeholder' => 'k€',
                ],
                [
                    'id' => 'libelle',
                    'name' => 'Libellé',
                    'type' => 'textarea',
                    'rows' => 2,
                ],
            ],
        ];

        return $meta_boxes;
    }
}
