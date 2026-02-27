<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class BlockServiceProvider extends ServiceProvider {
    public function register(): void {
        add_filter( 'rwmb_meta_boxes', [ $this, 'loadAllBlocks' ] );
    }

    public function loadAllBlocks( $meta_boxes ) {
        $blocksPath = app_path( 'Blocks' );

        if ( ! File::isDirectory( $blocksPath ) ) {
            return $meta_boxes;
        }

        foreach ( File::allFiles( $blocksPath ) as $file ) {
            $className = 'App\\Blocks\\' . $file->getFilenameWithoutExtension();

            if ( method_exists( $className, 'register_block' ) ) {
                $meta_boxes = $className::register_block( $meta_boxes );
            }
        }

        return $meta_boxes;
    }
}
