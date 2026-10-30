<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class Slider extends BlockComposer
{
    protected static $views = ['blocks.slider'];

    public function label(): string
    {
        return $this->attribute('label') ?: __('Carrousel', 'sage');
    }

    public function splideOptions(): string
    {
        $perPage = max(1, (int) $this->attribute('perPage', 3));

        $options = [
            'type' => $this->attribute('loop') ? 'loop' : 'slide',
            'perPage' => $perPage,
            'gap' => $this->gap(),
            'arrows' => (bool) $this->attribute('arrows', true),
            'pagination' => (bool) $this->attribute('pagination'),
            'autoplay' => (bool) $this->attribute('autoplay'),
            'interval' => max(1, (int) $this->attribute('interval', 5)) * 1000,
            // Clés = largeur max (Splide) : points de rupture tablet et mobile de theme.json (settings.viewport)
            'breakpoints' => [
                $this->breakpoint('tablet', 782) => [
                    'perPage' => (int) ($this->attribute('perPageTablet') ?? max(1, $perPage - 1)),
                ],
                $this->breakpoint('mobile', 480) => [
                    'perPage' => (int) ($this->attribute('perPageMobile') ?? 1),
                ],
            ],
            'i18n' => $this->i18n(),
        ];

        if ($this->attribute('align') === 'full') {
            $options['padding'] = [
                'left' => 'var(--slider-padding-inline)',
                'right' => 'var(--slider-padding-inline)',
            ];
        }

        return wp_json_encode($options);
    }

    private function breakpoint(string $name, int $default): int
    {
        return (int) (wp_get_global_settings(['viewport', $name]) ?: $default);
    }

    private function gap(): string
    {
        $gap = $this->attribute('style')['spacing']['blockGap'] ?? null;
        $gap = is_array($gap) ? $gap['left'] ?? $gap['top'] ?? null : $gap;

        if (! is_string($gap) || $gap === '') {
            return '1.5rem';
        }

        return preg_replace('/^var:preset\|([\w-]+)\|([\w-]+)$/', 'var(--wp--preset--$1--$2)', $gap);
    }

    private function i18n(): array
    {
        return [
            'prev' => __('Diapositive précédente', 'sage'),
            'next' => __('Diapositive suivante', 'sage'),
            'first' => __('Aller à la première diapositive', 'sage'),
            'last' => __('Aller à la dernière diapositive', 'sage'),
            // translators: %s: slide number.
            'slideX' => __('Aller à la diapositive %s', 'sage'),
            // translators: %s: page number.
            'pageX' => __('Aller à la page %s', 'sage'),
            'play' => __('Lancer la lecture automatique', 'sage'),
            'pause' => __('Mettre en pause la lecture automatique', 'sage'),
            'carousel' => __('carrousel', 'sage'),
            'select' => __('Sélectionner une diapositive', 'sage'),
            'slide' => __('diapositive', 'sage'),
            // translators: Splide replaces each %s in order: current slide, total slides.
            'slideLabel' => __('%s sur %s', 'sage'),
        ];
    }
}
