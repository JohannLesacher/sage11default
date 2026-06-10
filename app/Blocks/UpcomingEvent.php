<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class UpcomingEvent extends BlockEngine
{
    public static function register_block($meta_boxes)
    {
        $meta_boxes[] = [
            'title' => 'Upcoming Event',
            'id' => 'upcoming-event',
            'type' => 'block',
            'category' => 'sur-mesure',
            'context' => 'normal',
            'icon' => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets' => function () {
                wp_enqueue_style('upcoming-event', Vite::asset('resources/css/blocks/upcoming-event.scss'), [], null);
            },
            'supports' => [
                'align' => true,
                'anchor' => true,
                'customClassName' => true,
                'reusable' => true,
            ],
            'fields' => [
                [
                    'id' => 'image',
                    'name' => 'Image',
                    'type' => 'single_image',
                    'image_size' => 'large',
                ],
                [
                    'id' => 'title',
                    'name' => 'Title',
                    'type' => 'text',
                ],
                [
                    'id' => 'date',
                    'name' => 'Date',
                    'type' => 'date',
                ],
                [
                    'id' => 'time',
                    'name' => 'Time',
                    'type' => 'time',
                ],
                [
                    'id' => 'price',
                    'name' => 'Price',
                    'type' => 'text',
                ],
                [
                    'id' => 'cta_url',
                    'name' => 'CTA (Link)',
                    'type' => 'url',
                ],
                [
                    'id' => 'cta_text',
                    'name' => 'CTA (Text)',
                    'type' => 'text',
                ],
            ],
        ];

        return $meta_boxes;
    }
}
