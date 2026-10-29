<?php

namespace App\Blocks;

use App\Blocks\Base\MetaBoxBlock;

class TexteDefilant extends MetaBoxBlock
{
    protected static function definition(): array
    {
        return [
            'title' => 'Texte défilant',
            'icon' => 'text',
            'supports' => [
                'align' => ['full', 'wide'],
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
                    'fontSize' => true,
                ],
            ],
            'fields' => [
                [
                    'id' => 'texte',
                    'name' => 'Texte',
                    'type' => 'text',
                    'placeholder' => 'Texte défilant…',
                ],
                [
                    'id' => 'separateur',
                    'name' => 'Séparateur',
                    'type' => 'text',
                    'placeholder' => '•',
                    'desc' => 'Affiché entre chaque répétition du texte.',
                ],
                [
                    'id' => 'vitesse',
                    'name' => 'Vitesse',
                    'type' => 'select',
                    'options' => [
                        'lent' => 'Lent',
                        'normal' => 'Normal',
                        'rapide' => 'Rapide',
                    ],
                    'std' => 'normal',
                ],
                [
                    'id' => 'direction',
                    'name' => 'Direction',
                    'type' => 'select',
                    'options' => [
                        'gauche' => 'Gauche',
                        'droite' => 'Droite',
                    ],
                    'std' => 'gauche',
                ],
                [
                    'id' => 'pause',
                    'name' => 'Pause au survol',
                    'type' => 'checkbox',
                    'std' => 0,
                ],
                [
                    'id' => 'tag',
                    'name' => 'Élément HTML',
                    'type' => 'select',
                    'options' => [
                        'div' => 'div',
                        'p' => 'p',
                        'h1' => 'h1',
                        'h2' => 'h2',
                        'h3' => 'h3',
                        'span' => 'span',
                    ],
                    'std' => 'div',
                ],
            ],
        ];
    }
}
