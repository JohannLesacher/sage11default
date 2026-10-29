<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class BlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        add_action('init', [$this, 'bootAllBlocks']);
        add_filter('rwmb_meta_boxes', [$this, 'loadAllBlocks']);
    }

    public function bootAllBlocks(): void
    {
        foreach ($this->blockClasses() as $className) {
            if (method_exists($className, 'boot')) {
                $className::boot();
            }
        }
    }

    public function loadAllBlocks($meta_boxes)
    {
        foreach ($this->blockClasses() as $className) {
            if (method_exists($className, 'register_block')) {
                $meta_boxes = $className::register_block($meta_boxes);
            }
        }

        return $meta_boxes;
    }

    private function blockClasses(): array
    {
        $blocksPath = app_path('Blocks');

        if (! File::isDirectory($blocksPath)) {
            return [];
        }

        return array_map(
            fn ($file) => 'App\\Blocks\\'.$file->getFilenameWithoutExtension(),
            File::allFiles($blocksPath)
        );
    }
}
