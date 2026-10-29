<?php

namespace App\Blocks\Base;

use Illuminate\Support\Facades\Vite;

/**
 * Assets d'un bloc, s'ils existent : resources/css/blocks/{slug}.scss (front + éditeur)
 * et resources/js/blocks/{slug}.js (module, front uniquement).
 *
 * Front : chargés dans le <head> si le contenu contient le bloc, sinon au rendu (bloc dans un pattern, un widget…).
 */
trait LoadsAssets
{
    protected static function loadAssets(string $slug, string $blockName, bool $onlyIfPresent): void
    {
        if ($onlyIfPresent && ! is_admin() && ! has_block($blockName)) {
            return;
        }

        if (file_exists(resource_path("css/blocks/{$slug}.scss"))) {
            wp_enqueue_style("block-{$slug}", Vite::asset("resources/css/blocks/{$slug}.scss"), [], null);
        }

        if (! is_admin() && file_exists(resource_path("js/blocks/{$slug}.js"))) {
            wp_enqueue_script_module("block-{$slug}", Vite::asset("resources/js/blocks/{$slug}.js"), [], null);
        }
    }
}
