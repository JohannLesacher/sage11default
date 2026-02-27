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
remove_action( 'after_setup_theme', [ wp_script_modules(), 'add_hooks' ] );
add_action( 'wp_head', function () {
    wp_script_modules()->print_import_map();
    wp_script_modules()->print_enqueued_script_modules();
    wp_script_modules()->print_script_module_preloads();
    echo "\r\n";
}, 8 );
