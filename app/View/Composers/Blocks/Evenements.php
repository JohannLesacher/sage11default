<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;
use WP_Post;

class Evenements extends BlockComposer
{
    protected static $views = ['blocks.evenements'];

    private ?array $evenements = null;

    public function evenements(): array
    {
        return $this->evenements ??= array_map(fn (WP_Post $post) => [
            'titre' => get_the_title($post),
            'url' => get_permalink($post),
            'date' => get_the_date('', $post),
            'datetime' => get_the_date('c', $post),
            'image' => $this->attribute('afficherImage', true)
                ? get_the_post_thumbnail($post, 'medium_large', ['class' => 'block-evenements__image'])
                : '',
        ], get_posts([
            'post_type' => 'evenement',
            'numberposts' => max(1, min(12, (int) $this->attribute('nombre', 3))),
            'no_found_rows' => true,
        ]));
    }
}
