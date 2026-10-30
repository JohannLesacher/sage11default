<?php

namespace App\View;

use Roots\Acorn\View\Composer;

/**
 * Base des composers de blocs (app/View/Composers/Blocks) : lecture typée des attributs du bloc.
 *
 * $wrapperAttributes() est fourni à toutes les vues de blocs par BlockServiceProvider.
 */
abstract class BlockComposer extends Composer
{
    /**
     * Attribut du bloc, en chaîne nettoyée.
     */
    protected function field(string $key): string
    {
        return trim($this->string($key));
    }

    protected function string(string $key, string $default = ''): string
    {
        $value = $this->attribute($key);

        return is_scalar($value) ? (string) $value : $default;
    }

    protected function integer(string $key, int $default = 0): int
    {
        $value = $this->attribute($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    protected function boolean(string $key, bool $default = false): bool
    {
        $value = $this->attribute($key);

        return $value === null ? $default : (bool) filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    protected function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes()[$key] ?? $default;
    }

    /**
     * @return array<mixed>
     */
    protected function attributes(): array
    {
        $attributes = $this->view->getData()['attributes'] ?? [];

        return is_array($attributes) ? $attributes : [];
    }
}
