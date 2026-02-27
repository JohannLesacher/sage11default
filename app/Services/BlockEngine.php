<?php

namespace App\Services;

use Throwable;
use function Roots\view;

abstract class BlockEngine {
    /**
     * @throws Throwable
     */
    public static function renderBlock( $attributes, $is_preview, $block ): void {
        $blockName = explode( '/', $block->name )[1];

        echo view( "blocks.$blockName", [
            'data'       => $attributes['data'] ?? null,
            'attributes' => $attributes,
            'is_preview' => $is_preview
        ] )->render();
    }
}
