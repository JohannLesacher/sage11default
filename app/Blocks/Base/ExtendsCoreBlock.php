<?php

namespace App\Blocks\Base;

use Illuminate\Support\Facades\Vite;

/**
 * Outils pour étendre un bloc core depuis une classe de app/Blocks (fichiers dans resources/blocks/{folder}/).
 */
trait ExtendsCoreBlock
{
    /**
     * Style de bloc + feuille resources/blocks/{folder}/style.scss : en front, chargée seulement si
     * un bloc utilise ce style (wp_enqueue_block_style chargerait partout, should_load_separate_core_block_assets
     * étant à false) ; dans l'éditeur, toujours chargée, le style pouvant être choisi à tout moment.
     */
    protected static function blockStyle(string $blockName, string $style, string $label, string $folder): void
    {
        $handle = "block-{$folder}";

        register_block_style($blockName, ['name' => $style, 'label' => $label]);
        wp_register_style($handle, Vite::asset("resources/blocks/{$folder}/style.scss"), [], null);

        add_filter("render_block_{$blockName}", function ($content, $block) use ($style, $handle) {
            if (str_contains($block['attrs']['className'] ?? '', "is-style-{$style}")) {
                wp_enqueue_style($handle);
            }

            return $content;
        }, 10, 2);

        add_action('enqueue_block_assets', function () use ($handle) {
            if (is_admin()) {
                wp_enqueue_style($handle);
            }
        });
    }
}
