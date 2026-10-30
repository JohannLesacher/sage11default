<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class Slider extends BlockComposer
{
    protected static $views = ['blocks.slider'];

    public function label(): string
    {
        return $this->string('label') ?: __('Carrousel', 'sage');
    }

    public function splideOptions(): string
    {
        $perPage = max(1, $this->integer('perPage', 3));

        $options = [
            'type' => $this->boolean('loop') ? 'loop' : 'slide',
            'perPage' => $perPage,
            'gap' => $this->gap(),
            'arrows' => $this->boolean('arrows', true),
            'pagination' => $this->boolean('pagination'),
            'autoplay' => $this->boolean('autoplay'),
            'interval' => max(1, $this->integer('interval', 5)) * 1000,
            // Clés = largeur max (Splide) : points de rupture tablet et mobile de theme.json (settings.viewport)
            'breakpoints' => [
                $this->breakpoint('tablet', 782) => [
                    'perPage' => $this->integer('perPageTablet', max(1, $perPage - 1)),
                ],
                $this->breakpoint('mobile', 480) => [
                    'perPage' => $this->integer('perPageMobile', 1),
                ],
            ],
            'i18n' => $this->i18n(),
        ];

        if ($this->string('align') === 'full') {
            $options['padding'] = [
                'left' => 'var(--slider-padding-inline)',
                'right' => 'var(--slider-padding-inline)',
            ];
        }

        return wp_json_encode($options) ?: '{}';
    }

    private function breakpoint(string $name, int $default): int
    {
        $value = wp_get_global_settings(['viewport', $name]);

        return is_numeric($value) ? (int) $value : $default;
    }

    private function gap(): string
    {
        $gap = data_get($this->attributes(), 'style.spacing.blockGap');
        $gap = is_array($gap) ? $gap['left'] ?? $gap['top'] ?? null : $gap;

        if (! is_string($gap) || $gap === '') {
            return '1.5rem';
        }

        return preg_replace('/^var:preset\|([\w-]+)\|([\w-]+)$/', 'var(--wp--preset--$1--$2)', $gap) ?? $gap;
    }

    /**
     * @return array<string, string>
     */
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
