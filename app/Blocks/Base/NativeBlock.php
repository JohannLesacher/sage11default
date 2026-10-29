<?php

namespace App\Blocks\Base;

use WP_Block;

use function Roots\view;

/**
 * Bloc natif dynamique, découvert automatiquement depuis resources/blocks/{name}/block.json
 * (source unique PHP + éditeur JS), rendu par la vue blocks.{name}.
 *
 * Aucune classe à créer. Pour personnaliser (hooks, rendu), créer App\Blocks\{Name} qui étend
 * cette classe : elle est alors utilisée à la place.
 */
class NativeBlock
{
    use LoadsAssets;

    protected string $blockName;

    public function __construct(protected string $name)
    {
        $this->blockName = json_decode(file_get_contents($this->path().'/block.json'), true)['name'];
    }

    public function boot(): void
    {
        register_block_type($this->path(), ['render_callback' => [$this, 'render']]);

        add_action('enqueue_block_assets', fn () => static::loadAssets($this->name, $this->blockName, true));
    }

    public function render(array $attributes, string $content, WP_Block $block): string
    {
        static::loadAssets($this->name, $this->blockName, false);

        return view("blocks.{$this->name}", compact('attributes', 'content', 'block'))->render();
    }

    protected function path(): string
    {
        return resource_path("blocks/{$this->name}");
    }
}
