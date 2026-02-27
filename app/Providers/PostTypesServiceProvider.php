<?php

namespace App\Providers;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class PostTypesServiceProvider extends ServiceProvider {

    public function register(): void {
        $path = app_path( 'PostTypes' );

        if ( ! File::isDirectory( $path ) ) {
            return;
        }

        foreach ( File::allFiles( $path ) as $file ) {
            $class = 'App\\PostTypes\\' . $file->getFilenameWithoutExtension();
            $this->app->singleton( $class );
        }
    }

    public function boot(): void {
        add_action( 'init', function (): void {
            Collection::make( config( 'post-types.post_types' ) )
                      ->each( function ( $args, $post_type ) {
                          register_extended_post_type(
                              $post_type,
                              $args,
                              Arr::pull( $args, 'names' )
                          );
                      } );
        }, 100 );

        add_action( 'init', function (): void {
            Collection::make( config( 'post-types.taxonomies' ) )
                      ->each( function ( $args, $taxonomy ) {
                          register_extended_taxonomy(
                              $taxonomy,
                              Arr::pull( $args, 'post_types' ),
                              $args,
                              Arr::pull( $args, 'names' )
                          );
                      } );
        }, 100 );

        add_filter( 'rwmb_meta_boxes', [ $this, 'loadPostTypesMetas' ] );
    }

    public function loadPostTypesMetas( array $meta_boxes ): array {
        $path = app_path( 'PostTypes' );

        if ( ! File::isDirectory( $path ) ) {
            return $meta_boxes;
        }

        foreach ( File::allFiles( $path ) as $file ) {
            $class = 'App\\PostTypes\\' . $file->getFilenameWithoutExtension();

            if ( class_exists( $class ) ) {
                $instance = $this->app->make( $class );

                if ( method_exists( $instance, 'addMetas' ) ) {
                    $meta_boxes = $instance->addMetas( $meta_boxes );
                }
            }
        }

        return $meta_boxes;
    }
}
