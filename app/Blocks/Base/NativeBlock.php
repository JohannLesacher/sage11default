<?php

namespace App\Blocks\Base;

use Illuminate\Support\Facades\Vite;
use WP_Block;

use function Roots\view;

/**
 * Bloc natif dynamique, découvert automatiquement depuis resources/blocks/{name}/block.json
 * (source unique PHP + éditeur JSX), rendu par la vue blocks.{name}.
 *
 * Aucune classe à créer. Pour personnaliser (hooks, rendu), créer App\Blocks\{Name} qui étend
 * cette classe : elle est alors utilisée à la place.
 *
 * Assets, s'ils existent : resources/css/blocks/{name}.scss (front + éditeur)
 * et resources/js/blocks/{name}.js (module, front uniquement). Front : chargés dans le <head>
 * si le contenu contient le bloc, sinon au rendu (bloc dans un pattern, un widget…).
 */
class NativeBlock
{
    protected string $blockName;

    public function __construct(protected string $name)
    {
        $this->blockName = json_decode(file_get_contents($this->path().'/block.json'), true)['name'];
    }

    public function boot(): void
    {
        register_block_type($this->path(), ['render_callback' => [$this, 'render']]);

        add_action('enqueue_block_assets', fn () => $this->loadAssets(onlyIfPresent: true));
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        $this->loadAssets(onlyIfPresent: false);

        // Aperçu dans l'éditeur (ServerSideRender passe par l'API REST)
        $is_preview = wp_is_serving_rest_request();

        return view("blocks.{$this->name}", compact('attributes', 'content', 'block', 'is_preview'))->render();
    }

    protected function loadAssets(bool $onlyIfPresent): void
    {
        if ($onlyIfPresent && ! is_admin() && ! has_block($this->blockName)) {
            return;
        }

        if (file_exists(resource_path("css/blocks/{$this->name}.scss"))) {
            wp_enqueue_style("block-{$this->name}", Vite::asset("resources/css/blocks/{$this->name}.scss"), [], null);
        }

        if (! is_admin() && file_exists(resource_path("js/blocks/{$this->name}.js"))) {
            wp_enqueue_script_module("block-{$this->name}", Vite::asset("resources/js/blocks/{$this->name}.js"), [], null);
        }
    }

    protected function path(): string
    {
        return resource_path("blocks/{$this->name}");
    }
}
