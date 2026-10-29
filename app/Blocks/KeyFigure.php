<?php

namespace App\Blocks;

use App\Blocks\Base\MetaBoxBlock;

class KeyFigure extends MetaBoxBlock
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

    protected static function definition(): array
    {
        return [
            'title' => 'Chiffre clé',
            'icon' => 'chart-bar',
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
    }
}
