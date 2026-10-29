<?php

namespace App\View;

use Roots\Acorn\View\Composer;
use WP_Block_Supports;

/**
 * Base des composers de blocs (MB Blocks et natifs).
 *
 * Dans la vue : <div {!! $wrapperAttributes('block-mon-bloc') !!}>
 * → classes, styles et ancre des supports Gutenberg (align, couleurs, espacements, className, anchor).
 */
abstract class BlockComposer extends Composer
{
    public function wrapperAttributes(string $class = '', array $extra = []): string
    {
        $extra = array_filter(['class' => $class] + $extra, fn ($value) => $value !== '' && $value !== null);

        // Hors rendu de bloc (aperçu particulier), pas de supports : attributs bruts
        if (empty(WP_Block_Supports::$block_to_render)) {
            return implode(' ', array_map(
                fn ($name, $value) => sprintf('%s="%s"', $name, esc_attr($value)),
                array_keys($extra),
                $extra
            ));
        }

        return get_block_wrapper_attributes($extra);
    }

    /**
     * Champ MB Blocks, en chaîne nettoyée.
     */
    protected function field(string $key): string
    {
        return trim((string) ($this->view->getData()['data'][$key] ?? ''));
    }

    protected function attribute(string $key, mixed $default = null): mixed
    {
        return $this->view->getData()['attributes'][$key] ?? $default;
    }
}
