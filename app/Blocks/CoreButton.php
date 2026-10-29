<?php

namespace App\Blocks;

use Illuminate\Support\Facades\Vite;
use WP_HTML_Tag_Processor;

use function Roots\view;

/**
 * Exemple d'extension d'un bloc core (boot() appelé sur init par BlockServiceProvider) :
 * - style de bloc « Flèche » (classe is-style-fleche, CSS dans resources/css/blocks/core-button.scss)
 * - rendu modifié : lien ouvert dans un nouvel onglet → mention pour les lecteurs d'écran (vue Blade)
 */
class CoreButton
{
    public static function boot(): void
    {
        register_block_style('core/button', ['name' => 'fleche', 'label' => 'Flèche']);

        wp_enqueue_block_style('core/button', [
            'handle' => 'block-core-button',
            'src' => Vite::asset('resources/css/blocks/core-button.scss'),
        ]);

        add_filter('render_block_core/button', [static::class, 'render']);
    }

    public static function render(string $content): string
    {
        $html = new WP_HTML_Tag_Processor($content);

        if (! $html->next_tag('a') || $html->get_attribute('target') !== '_blank') {
            return $content;
        }

        return preg_replace('#</a>#', view('partials.new-tab-label')->render().'</a>', $content, 1);
    }
}
