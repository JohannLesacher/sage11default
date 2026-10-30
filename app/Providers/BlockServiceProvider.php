<?php

namespace App\Providers;

use App\Blocks\Base\BlockExtension;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use WP_Block;

/**
 * Blocs : chaque resources/blocks/{name}/block.json est enregistré automatiquement.
 * - resources/blocks/{name}/ : block.json, editor.jsx (importé par resources/js/editor.js),
 *   style.scss (front + éditeur) et script.js (module, front), optionnels
 * - rendu : vue blocks.{name}, composer App\View\Composers\Blocks\{Name} s'il y a de la logique
 *
 * Classes de app/Blocks/*.php (extensions de blocs core…) : boot() statique appelé sur init.
 */
class BlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        add_action('init', [$this, 'bootBlocks']);
    }

    public function bootBlocks(): void
    {
        foreach (glob(resource_path('blocks') . '/*/block.json') ?: [] as $file) {
            $this->registerBlock(basename(dirname($file)));
        }

        foreach ($this->blockClasses() as $className) {
            $className::boot();
        }
    }

    private function registerBlock(string $name): void
    {
        $type = register_block_type(resource_path("blocks/{$name}"), [
            'render_callback' => fn (array $attributes, string $content, WP_Block $block) => $this->render(
                $name,
                $attributes,
                $content,
                $block,
            ),
        ]);

        if (! $type) {
            return;
        }

        // Front : assets dans le <head> si le contenu contient le bloc, sinon au rendu (pattern, widget…)
        add_action('enqueue_block_assets', function () use ($name, $type) {
            if (is_admin() || has_block($type->name)) {
                $this->loadAssets($name);
            }
        });
    }

    /**
     * @param  array<mixed>  $attributes
     */
    private function render(string $name, array $attributes, string $content, WP_Block $block): string
    {
        $this->loadAssets($name);

        return View::make("blocks.{$name}", [
            'attributes' => $attributes,
            'content' => $content,
            'block' => $block,
            // Aperçu dans l'éditeur (ServerSideRender passe par l'API REST)
            'is_preview' => wp_is_serving_rest_request(),
            // Classes, styles et ancre des supports Gutenberg : <div {!! $wrapperAttributes('block-x') !!}>
            'wrapperAttributes' => $this->wrapperAttributes(...),
        ])->render();
    }

    /**
     * @param  array<string, string|null>  $extra
     */
    private function wrapperAttributes(string $class = '', array $extra = []): string
    {
        return get_block_wrapper_attributes(array_filter(
            ['class' => $class] + $extra,
            fn (?string $value) => $value !== null && $value !== '',
        ));
    }

    private function loadAssets(string $name): void
    {
        if (file_exists(resource_path("blocks/{$name}/style.scss"))) {
            wp_enqueue_style("block-{$name}", Vite::asset("resources/blocks/{$name}/style.scss"), [], null);
        }

        if (! is_admin() && file_exists(resource_path("blocks/{$name}/script.js"))) {
            wp_enqueue_script_module("block-{$name}", Vite::asset("resources/blocks/{$name}/script.js"), [], null);
        }
    }

    /**
     * @return list<class-string<BlockExtension>>
     */
    private function blockClasses(): array
    {
        $classes = [];

        foreach (glob(app_path('Blocks') . '/*.php') ?: [] as $file) {
            $className = 'App\\Blocks\\' . pathinfo($file, PATHINFO_FILENAME);

            if (
                is_subclass_of($className, BlockExtension::class) && ! (new ReflectionClass($className))->isAbstract()
            ) {
                $classes[] = $className;
            }
        }

        return $classes;
    }
}
