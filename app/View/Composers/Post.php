<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class Post extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var string[]
     */
    protected static $views = [
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Retrieve the post title.
     */
    public function title(): string
    {
        if ($this->view->name() !== 'partials.page-header') {
            return get_the_title();
        }

        if (is_home()) {
            $home = get_option('page_for_posts');

            if (is_numeric($home) && (int) $home > 0) {
                return get_the_title((int) $home);
            }

            return __('Latest Posts', 'sage');
        }

        if (is_archive()) {
            return get_the_archive_title();
        }

        if (is_search()) {
            return sprintf(
                /* translators: %s is replaced with the search query */
                __('Search Results for %s', 'sage'),
                get_search_query(),
            );
        }

        if (is_404()) {
            return __('Not Found', 'sage');
        }

        return get_the_title();
    }

    /**
     * Retrieve the pagination links.
     */
    public function pagination(): string
    {
        return wp_link_pages([
            'echo' => 0,
            'before' => '<p>' . __('Pages:', 'sage'),
            'after' => '</p>',
        ]);
    }

    /**
     * Should display title
     */
    public function shouldDisplayTitle(): bool
    {
        // Page jamais enregistrée depuis l'ajout de la metabox : on applique le défaut (affiché)
        $id = get_the_ID();

        if (is_page() && $id !== false && metadata_exists('post', $id, 'display_title')) {
            return (bool) get_post_meta($id, 'display_title', true);
        }

        return true;
    }
}
