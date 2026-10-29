<?php

namespace App\Providers;

use App\Blocks\Base\MetaBoxBlock;
use App\Blocks\Base\NativeBlock;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * - Blocs natifs : chaque resources/blocks/{name}/block.json est enregistré (classe App\Blocks\{Name} optionnelle)
 * - Classes de app/Blocks/*.php (racine, non abstraites) : boot() statique appelé sur init
 *   (blocs MB Blocks, extensions de blocs core), MetaBoxBlock déclarés à MB Blocks
 */
class BlockServiceProvider extends ServiceProvider
{
    private ?array $classes = null;

    public function register(): void
    {
        add_action('init', [$this, 'bootBlocks']);
        add_filter('rwmb_meta_boxes', [$this, 'registerMetaBoxBlocks']);
    }

    public function bootBlocks(): void
    {
        foreach (File::glob(resource_path('blocks').'/*/block.json') as $file) {
            $name = basename(dirname($file));
            $class = 'App\\Blocks\\'.Str::studly($name);

            $block = is_subclass_of($class, NativeBlock::class) ? new $class($name) : new NativeBlock($name);
            $block->boot();
        }

        foreach ($this->blockClasses() as $className) {
            if (! is_subclass_of($className, NativeBlock::class) && method_exists($className, 'boot')) {
                $className::boot();
            }
        }
    }

    public function registerMetaBoxBlocks(array $meta_boxes): array
    {
        foreach ($this->blockClasses() as $className) {
            if (is_subclass_of($className, MetaBoxBlock::class)) {
                $meta_boxes = $className::register($meta_boxes);
            }
        }

        return $meta_boxes;
    }

    private function blockClasses(): array
    {
        return $this->classes ??= array_values(array_filter(
            array_map(
                fn ($file) => 'App\\Blocks\\'.pathinfo($file, PATHINFO_FILENAME),
                File::glob(app_path('Blocks').'/*.php')
            ),
            fn ($className) => class_exists($className) && ! (new ReflectionClass($className))->isAbstract()
        ));
    }
}
