<?php

namespace App\Providers;

use App\Blocks\Base\NativeBlock;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

/**
 * - Blocs : chaque resources/blocks/{name}/block.json est enregistré (classe App\Blocks\{Name} optionnelle)
 * - Autres classes de app/Blocks/*.php (racine, non abstraites) : boot() statique appelé sur init
 *   (extensions de blocs core…)
 */
class BlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        add_action('init', [$this, 'bootBlocks']);
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

    private function blockClasses(): array
    {
        return array_values(array_filter(
            array_map(
                fn ($file) => 'App\\Blocks\\'.pathinfo($file, PATHINFO_FILENAME),
                File::glob(app_path('Blocks').'/*.php')
            ),
            fn ($className) => class_exists($className) && ! (new ReflectionClass($className))->isAbstract()
        ));
    }
}
