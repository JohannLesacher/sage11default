<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Roots\Acorn\Sage\SageServiceProvider;

class ThemeServiceProvider extends SageServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        add_action('init', function () {
            register_block_style('core/gallery', [
                'name' => 'logos',
                'label' => 'Logos',
            ]);

            wp_register_style('gallery-logos', Vite::asset('resources/css/blocks/gallery-logos.scss'), [], null);
        });

        // Site : chargée seulement si une galerie utilise le style « Logos »
        // (wp_enqueue_block_style chargerait partout, should_load_separate_core_block_assets étant à false)
        add_filter('render_block_core/gallery', function ($content, $block) {
            if (str_contains($block['attrs']['className'] ?? '', 'is-style-logos')) {
                wp_enqueue_style('gallery-logos');
            }

            return $content;
        }, 10, 2);

        // Éditeur : toujours chargée, le style pouvant être choisi à tout moment
        add_action('enqueue_block_assets', function () {
            if (is_admin()) {
                wp_enqueue_style('gallery-logos');
            }
        });
    }
}
