<?php

/**
 * Theme filters.
 */

namespace App;

/**
 * Quickfix for script module loading bug on Firefox
 *
 * @see https://wordpress.org/support/topic/importmap-wordpress-interactivity/
 */
add_action('after_setup_theme', function () {
    $modules = wp_script_modules();

    remove_action('wp_footer', [$modules, 'print_import_map']);
    remove_action('wp_footer', [$modules, 'print_enqueued_script_modules']);
    remove_action('wp_footer', [$modules, 'print_script_module_preloads']);

    add_action('wp_head', function () use ($modules) {
        $modules->print_import_map();
        $modules->print_enqueued_script_modules();
        $modules->print_script_module_preloads();
        echo "\r\n";
    }, 8);
}, 11);

/**
 * Allow SVG
 */
add_filter('upload_mimes', function ($mimes) {
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
});

/**
 * Animation option
 */
add_filter('register_block_type_args', function ($args, $name) {
    $allowed_blocks = ['core/heading', 'core/group', 'core/columns', 'core/image'];
    if (in_array($name, $allowed_blocks)) {
        $args['attributes']['animateOnScroll'] = [
            'type' => 'boolean',
            'default' => false,
        ];
    }

    return $args;
}, 10, 2);

/**
 * Custom block category
 */
add_filter('block_categories_all', function ($categories) {
    return [
        [
            'slug' => 'sur-mesure',
            'title' => __('Sur-mesure', 'sage'),
            'icon' => null,
        ],
        ...$categories,
    ];
});

add_filter('image_size_names_choose', function ($sizes) {
    return array_merge($sizes, [
        'very-large' => __('Très large', 'sage'),
    ]);
});
