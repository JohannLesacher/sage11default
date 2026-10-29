<?php

/**
 * Theme filters.
 */

namespace App;

use enshrined\svgSanitize\Sanitizer;

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
 * Allow SVG (administrateurs uniquement, WordPress ne le gère pas nativement)
 */
add_filter('upload_mimes', function ($mimes) {
    if (current_user_can('manage_options')) {
        $mimes['svg'] = 'image/svg+xml';
    }

    return $mimes;
});

/**
 * Assainit les SVG à l'upload (scripts, handlers on*, références externes)
 */
add_filter('wp_handle_upload_prefilter', function ($file) {
    if (strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'svg') {
        return $file;
    }

    $sanitizer = new Sanitizer;
    $sanitizer->removeRemoteReferences(true);

    $clean = $sanitizer->sanitize((string) file_get_contents($file['tmp_name']));

    if ($clean === false) {
        $file['error'] = __('Ce fichier SVG est invalide et ne peut pas être importé.', 'sage');

        return $file;
    }

    file_put_contents($file['tmp_name'], $clean);

    return $file;
});

/**
 * Animation option
 *
 * Liste à garder synchronisée avec ALLOWED_BLOCKS dans resources/js/admin/animation.js
 * (sur-mesure/slide déclare ses propres attributs dans App\Blocks\Slider).
 */
add_filter('register_block_type_args', function ($args, $name) {
    $allowed_blocks = [
        'core/heading',
        'core/group',
        'core/columns',
        'core/column',
        'core/image',
        'core/button',
        'core/cover',
        'core/accordion-item',
    ];

    if (in_array($name, $allowed_blocks, true)) {
        $args['attributes']['animateOnScroll'] = [
            'type' => 'boolean',
            'default' => false,
        ];
        $args['attributes']['animationType'] = [
            'type' => 'string',
            'default' => 'fade-up',
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

/**
 * Custom block pattern category
 */
add_action('init', function () {
    register_block_pattern_category('cereal-one', [
        'label' => __('Cereal One', 'sage'),
    ]);
});

add_filter('image_size_names_choose', function ($sizes) {
    return array_merge($sizes, [
        'very-large' => __('Très large', 'sage'),
    ]);
});

/**
 * Classe body du header fixe (config/theme.php)
 */
add_filter('body_class', function (array $classes) {
    if (config('theme.header.fixed')) {
        $classes[] = 'has-fixed-header';
    }

    return $classes;
});
