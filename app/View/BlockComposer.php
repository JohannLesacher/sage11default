<?php

namespace App\View;

use Roots\Acorn\View\Composer;

/**
 * Base des composers de blocs (app/View/Composers/Blocks) : lecture des attributs du bloc.
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
        return trim((string) $this->attribute($key, ''));
    }

    protected function attribute(string $key, mixed $default = null): mixed
    {
        return $this->view->getData()['attributes'][$key] ?? $default;
    }
}
