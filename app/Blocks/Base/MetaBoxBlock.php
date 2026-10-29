<?php

namespace App\Blocks\Base;

use Illuminate\Support\Str;

use function Roots\view;

/**
 * Bloc MB Blocks : champs MetaBox, rendu par la vue blocks.{id}.
 *
 * id = nom de la classe en kebab-case (KeyFigure → key-figure → meta-box/key-figure).
 * Assets : voir LoadsAssets.
 */
abstract class MetaBoxBlock
{
    use LoadsAssets;

    /**
     * Réglages MB Blocks (title, icon, supports, fields…). id, type, rendu et assets sont gérés ici.
     */
    abstract protected static function definition(): array;

    public static function id(): string
    {
        return Str::kebab(class_basename(static::class));
    }

    public static function register(array $meta_boxes): array
    {
        $meta_boxes[] = array_merge([
            'category' => 'sur-mesure',
            'context' => 'side',
            'icon' => 'admin-generic',
        ], static::definition(), [
            'id' => static::id(),
            'type' => 'block',
            'render_callback' => [static::class, 'render'],
            'enqueue_assets' => fn () => static::loadAssets(static::id(), static::blockName(), true),
        ]);

        return $meta_boxes;
    }

    /**
     * Appelé par MB Blocks, qui injecte les paramètres par leur nom.
     */
    public static function render(array $attributes, bool $is_preview = false): void
    {
        static::loadAssets(static::id(), static::blockName(), false);

        echo view('blocks.'.static::id(), [
            'data' => $attributes['data'] ?? [],
            'attributes' => $attributes,
            'is_preview' => $is_preview,
        ])->render();
    }

    protected static function blockName(): string
    {
        return 'meta-box/'.static::id();
    }
}
