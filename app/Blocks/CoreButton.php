<?php

namespace App\Blocks;

use App\Blocks\Base\ExtendsCoreBlock;
use WP_HTML_Tag_Processor;

/**
 * Extension de core/button (fichiers dans resources/blocks/core-button/) :
 * - style de bloc « Flèche » (is-style-fleche)
 * - lien ouvert dans un nouvel onglet → mention pour les lecteurs d'écran (filtre de rendu)
 */
class CoreButton
{
    use ExtendsCoreBlock;

    public static function boot(): void
    {
        static::blockStyle('core/button', 'fleche', 'Flèche', 'core-button');

        add_filter('render_block_core/button', [static::class, 'render']);
    }

    public static function render(string $content): string
    {
        $html = new WP_HTML_Tag_Processor($content);

        if (! $html->next_tag('a') || $html->get_attribute('target') !== '_blank') {
            return $content;
        }

        return preg_replace('#</a>#', '<span class="sr-only"> (s’ouvre dans un nouvel onglet)</span></a>', $content, 1);
    }
}
