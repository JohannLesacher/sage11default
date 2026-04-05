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

/**
 * Allow SVG
 */
add_filter( 'upload_mimes', function ( $mimes ) {
    $mimes['svg'] = 'image/svg+xml';

    return $mimes;
} );

/**
 * Animation option
 */
add_filter( 'register_block_type_args', function ( $args, $name ) {
    $allowed_blocks = [ 'core/heading', 'core/group', 'core/columns', 'core/image' ];
    if ( in_array( $name, $allowed_blocks ) ) {
        $args['attributes']['animateOnScroll'] = [
            'type'    => 'boolean',
            'default' => false,
        ];
    }

    return $args;
}, 10, 2 );
