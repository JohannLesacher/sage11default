<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class KeyFigure extends BlockComposer
{
    protected static $views = ['blocks.key-figure'];

    public function prefixe(): string
    {
        return $this->field('prefixe');
    }

    public function valeur(): string
    {
        return $this->field('valeur');
    }

    public function unite(): string
    {
        return $this->field('unite');
    }

    public function libelle(): string
    {
        // Saisi en RichText : seuls les retours à la ligne (<br>) sont conservés
        return nl2br(wp_kses($this->field('libelle'), ['br' => []]));
    }

    public function valueStyle(): ?string
    {
        if ($valueColor = $this->string('valueColor')) {
            return 'color: var(--wp--preset--color--' . sanitize_html_class($valueColor) . ')';
        }

        if ($customColor = $this->string('customValueColor')) {
            $color = safecss_filter_attr('color: ' . $customColor);

            return $color ?: null;
        }

        return null;
    }
}
