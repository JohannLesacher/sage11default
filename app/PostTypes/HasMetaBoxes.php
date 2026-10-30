<?php

namespace App\PostTypes;

/**
 * Classe de app/PostTypes qui déclare des meta boxes MetaBox (filtre rwmb_meta_boxes).
 */
interface HasMetaBoxes
{
    /**
     * @param  array<array<string, mixed>>  $meta_boxes
     * @return array<array<string, mixed>>
     */
    public function addMetas(array $meta_boxes): array;
}
