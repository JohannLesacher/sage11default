<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class Menu extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Menu',
            'id' => 'menu',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('menu', Vite::asset('resources/css/blocks/menu.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'title',
                    'name' => 'Title',
                    'type' => 'text',
                ],
                [
                    'id' => 'subtitle',
                    'name' => 'Subtitle',
                    'type' => 'text',
                ],
                [
                    'id' => 'icon',
                    'name' => 'Icon',
                    'type' => 'single_image',
                    'image_size' => 'thumbnail',
                ],
                [
                    'id' => 'items',
                    'name' => 'Menu items',
                    'type' => 'group',
                    'clone' => true,
                    'sort_clone' => true,
                    'add_button' => 'Add menu item',
                    'fields' => [
                        [
                            'id' => 'item_title',
                            'name' => 'Title',
                            'type' => 'text',
                        ],
                        [
                            'id' => 'item_subtitle',
                            'name' => 'Subtitle',
                            'type' => 'text',
                        ],
                        [
                            'id' => 'item_price',
                            'name' => 'Price',
                            'type' => 'text',
                        ],
                    ],
                ],
                [
                    'id' => 'pdf_menu',
                    'name' => 'PDF menu',
                    'type' => 'select',
                    'options' => [
                        'wine' => 'Wine & Sake menu (theme settings PDF)',
                        'food' => 'Food menu (theme settings PDF)',
                    ],
                    'placeholder' => '— Custom link below —',
                ],
                [
                    'id' => 'cta_url',
                    'name' => 'Download Button (Link)',
                    'type' => 'url',
                    'desc' => 'Used only if no PDF menu is selected above.',
                ],
                [
                    'id' => 'cta_text',
                    'name' => 'Download Button (Text)',
                    'type' => 'text',
                ],
            ],
        ];

        return $meta_boxes;
    }
}
