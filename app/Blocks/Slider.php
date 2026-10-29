<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;

use function Roots\view;

class Slider
{
    public static function boot(): void
    {
        wp_register_style('theme/slider', Vite::asset('resources/css/blocks/slider.scss'), [], null);
        wp_register_script_module('theme/slider', Vite::asset('resources/js/blocks/slider.js'), [], null);

        register_block_type('sur-mesure/slider', [
            'api_version' => 3,
            'attributes' => [
                'perPage' => ['type' => 'number', 'default' => 3],
                'perPageTablet' => ['type' => 'number'],
                'perPageMobile' => ['type' => 'number'],
                'loop' => ['type' => 'boolean', 'default' => false],
                'arrows' => ['type' => 'boolean', 'default' => true],
                'pagination' => ['type' => 'boolean', 'default' => false],
                'autoplay' => ['type' => 'boolean', 'default' => false],
                'interval' => ['type' => 'number', 'default' => 5],
                'label' => ['type' => 'string', 'default' => ''],
            ],
            'supports' => [
                'align' => ['wide', 'full'],
                'spacing' => ['blockGap' => true, 'margin' => ['top', 'bottom']],
            ],
            'style_handles' => ['theme/slider'],
            'view_script_module_ids' => ['theme/slider'],
            'render_callback' => [self::class, 'renderSlider'],
        ]);

        register_block_type('sur-mesure/slide', [
            'api_version' => 3,
            'parent' => ['sur-mesure/slider'],
            'attributes' => [
                'animateOnScroll' => ['type' => 'boolean', 'default' => false],
                'animationType' => ['type' => 'string', 'default' => 'fade-up'],
            ],
            'supports' => [
                'color' => ['background' => true, 'text' => true],
                'spacing' => ['padding' => true],
            ],
            'render_callback' => [self::class, 'renderSlide'],
        ]);
    }

    public static function renderSlider(array $attributes, string $content): string
    {
        return view('blocks.slider', compact('attributes', 'content'))->render();
    }

    public static function renderSlide(array $attributes, string $content): string
    {
        return view('blocks.slide', compact('attributes', 'content'))->render();
    }
}
