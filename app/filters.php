<?php

/**
 * Theme filters.
 */

namespace App;

use App\Services\ThemeConfig;
use enshrined\svgSanitize\Sanitizer;

/**
 * Quickfix for script module loading bug on Firefox
 *
 * @see https://wordpress.org/support/topic/importmap-wordpress-interactivity/
 */
add_action(
    'after_setup_theme',
    function () {
        $modules = wp_script_modules();

        remove_action('wp_footer', [$modules, 'print_import_map']);
        remove_action('wp_footer', [$modules, 'print_enqueued_script_modules']);
        remove_action('wp_footer', [$modules, 'print_script_module_preloads']);

        add_action(
            'wp_head',
            function () use ($modules) {
                $modules->print_import_map();
                $modules->print_enqueued_script_modules();
                $modules->print_script_module_preloads();
                echo "\r\n";
            },
            8,
        );
    },
    11,
);

/**
 * Allow SVG (administrateurs uniquement, WordPress ne le gère pas nativement)
 */
add_filter(
    'upload_mimes',
    /**
     * @param  array<string, string>  $mimes
     * @return array<string, string>
     */
    function (array $mimes): array {
        if (current_user_can('manage_options')) {
            $mimes['svg'] = 'image/svg+xml';
        }

        return $mimes;
    },
);

/**
 * Assainit les SVG à l'upload (scripts, handlers on*, références externes)
 */
add_filter(
    'wp_handle_upload_prefilter',
    /**
     * @param  array<mixed>  $file
     * @return array<mixed>
     */
    function (array $file): array {
        $name = $file['name'] ?? null;
        $path = $file['tmp_name'] ?? null;

        if (! is_string($name) || ! is_string($path) || strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'svg') {
            return $file;
        }

        $sanitizer = new Sanitizer();
        $sanitizer->removeRemoteReferences(true);

        $clean = $sanitizer->sanitize((string) file_get_contents($path));

        if ($clean === false) {
            $file['error'] = __('Ce fichier SVG est invalide et ne peut pas être importé.', 'sage');

            return $file;
        }

        file_put_contents($path, $clean);

        return $file;
    },
);

/**
 * Animation option (blocs listés dans config/theme.php)
 */
add_filter(
    'register_block_type_args',
    /**
     * @param  array<string, mixed>  $args
     * @return array<string, mixed>
     */
    function (array $args, string $name): array {
        if (in_array($name, ThemeConfig::animationBlocks(), true)) {
            $attributes = is_array($args['attributes'] ?? null) ? $args['attributes'] : [];
            $attributes['animateOnScroll'] = [
                'type' => 'boolean',
                'default' => false,
            ];
            $attributes['animationType'] = [
                'type' => 'string',
                'default' => 'fade-up',
            ];
            $args['attributes'] = $attributes;
        }

        return $args;
    },
    10,
    2,
);

/**
 * Custom block category
 */
add_filter(
    'block_categories_all',
    /**
     * @param  array<array<string, mixed>>  $categories
     * @return array<array<string, mixed>>
     */
    function (array $categories): array {
        return [
            [
                'slug' => 'sur-mesure',
                'title' => __('Sur-mesure', 'sage'),
                'icon' => null,
            ],
            ...$categories,
        ];
    },
);

/**
 * Custom block pattern category
 */
add_action('init', function () {
    register_block_pattern_category('cereal-one', [
        'label' => __('Cereal One', 'sage'),
    ]);
});

add_filter(
    'image_size_names_choose',
    /**
     * @param  array<string, string>  $sizes
     * @return array<string, string>
     */
    function (array $sizes): array {
        foreach (ThemeConfig::imageSizes() as $name => $size) {
            if (($size['label'] ?? null) !== null) {
                $sizes[$name] = $size['label'];
            }
        }

        return $sizes;
    },
);

/**
 * Classe body du header fixe (config/theme.php)
 */
add_filter(
    'body_class',
    /**
     * @param  string[]  $classes
     * @return string[]
     */
    function (array $classes): array {
        if (ThemeConfig::header()['fixed']) {
            $classes[] = 'has-fixed-header';
        }

        return $classes;
    },
);
