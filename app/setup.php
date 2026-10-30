<?php

/**
 * Theme setup.
 */

namespace App;

use App\Services\ThemeConfig;
use Illuminate\Support\Facades\Vite;

/**
 * Inject styles into the block editor.
 */
add_filter(
    'block_editor_settings_all',
    /**
     * @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    function (array $settings): array {
        $style = Vite::asset('resources/css/editor.scss');

        $styles = is_array($settings['styles'] ?? null) ? $settings['styles'] : [];
        $styles[] = [
            'css' => "@import url('{$style}')",
        ];
        $settings['styles'] = $styles;

        return $settings;
    },
);

/**
 * Inject scripts into the block editor.
 *
 * @return void
 */
add_action('admin_head', function () {
    if (! get_current_screen()?->is_block_editor()) {
        return;
    }

    if (! Vite::isRunningHot()) {
        $dependencies = json_decode(Vite::content('editor.deps.json'), true);

        foreach (is_array($dependencies) ? $dependencies : [] as $dependency) {
            if (is_string($dependency) && $dependency !== '' && ! wp_script_is($dependency)) {
                wp_enqueue_script($dependency);
            }
        }
    }
    // Config partagée avec les scripts éditeur (source unique : config/theme.php)
    printf('<script>window.theme = %s;</script>', wp_json_encode([
        'animationBlocks' => ThemeConfig::animationBlocks(),
    ]));

    echo Vite::withEntryPoints([
        'resources/js/editor.js',
    ])->toHtml();
});

/**
 * Disable on-demand block asset loading.
 *
 * @link https://core.trac.wordpress.org/ticket/61965
 */
add_filter('should_load_separate_core_block_assets', '__return_false');

/**
 * Register the initial theme setup.
 *
 * @return void
 */
add_action(
    'after_setup_theme',
    function () {
        /**
         * Load theme translations (`npm run translate`).
         */
        load_theme_textdomain('sage', get_theme_file_path('resources/lang'));

        /**
         * Disable full-site editing support.
         *
         * @link https://wptavern.com/gutenberg-10-5-embeds-pdfs-adds-verse-block-color-options-and-introduces-new-patterns
         */
        remove_theme_support('block-templates');

        /**
         * Register the navigation menus.
         *
         * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
         */
        register_nav_menus(ThemeConfig::menus());

        /**
         * Disable the default block patterns.
         *
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#disabling-the-default-block-patterns
         */
        remove_theme_support('core-block-patterns');

        /**
         * Enable plugins to manage the document title.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
         */
        add_theme_support('title-tag');

        /**
         * Enable post thumbnail support.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        /**
         * Register custom image sizes (config/theme.php).
         */
        foreach (ThemeConfig::imageSizes() as $name => $size) {
            add_image_size($name, $size['width'], $size['height'], $size['crop'] ?? false);
        }

        /**
         * Enable responsive embed support.
         *
         * @link https://developer.wordpress.org/block-editor/how-to-guides/themes/theme-support/#responsive-embedded-content
         */
        add_theme_support('responsive-embeds');

        /**
         * Enable HTML5 markup support.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
         */
        add_theme_support('html5', [
            'caption',
            'comment-form',
            'comment-list',
            'gallery',
            'search-form',
            'script',
            'style',
        ]);

        /**
         * Enable selective refresh for widgets in customizer.
         *
         * @link https://developer.wordpress.org/reference/functions/add_theme_support/#customize-selective-refresh-widgets
         */
        add_theme_support('customize-selective-refresh-widgets');

        /**
         * Enable wide alignment support.
         *
         * @link https://developer.wordpress.org/block-editor/developers/themes/theme-support/#align-wide
         */
        add_theme_support('align-wide');

        /*
         * Excerpt support
         */
        add_post_type_support('page', 'excerpt');
    },
    20,
);

/**
 * Register the theme sidebars.
 *
 * @return void
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>',
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary',
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer',
    ] + $config);
});
