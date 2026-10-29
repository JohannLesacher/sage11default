<?php

namespace App\View\Composers\Blocks;

class Slider extends Common
{
    protected static $views = ['blocks.slider'];

    private const BREAKPOINT_TABLET = 1024;

    private const BREAKPOINT_MOBILE = 640;

    public function label(): string
    {
        return esc_attr($this->attribute('label') ?: __('Carrousel', 'sage'));
    }

    public function splideOptions(): string
    {
        $perPage = max(1, (int) $this->attribute('perPage', 3));

        $options = [
            'type' => $this->attribute('loop') ? 'loop' : 'slide',
            'perPage' => $perPage,
            'gap' => $this->blockGap() ?? '1.5rem',
            'arrows' => (bool) $this->attribute('arrows', true),
            'pagination' => (bool) $this->attribute('pagination'),
            'autoplay' => (bool) $this->attribute('autoplay'),
            'interval' => max(1, (int) $this->attribute('interval', 5)) * 1000,
            'breakpoints' => [
                self::BREAKPOINT_TABLET => [
                    'perPage' => (int) ($this->attribute('perPageTablet') ?? max(1, $perPage - 1)),
                ],
                self::BREAKPOINT_MOBILE => [
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

        return esc_attr(wp_json_encode($options));
    }

    private function i18n(): array
    {
        return [
            'prev' => __('Diapositive précédente', 'sage'),
            'next' => __('Diapositive suivante', 'sage'),
            'first' => __('Aller à la première diapositive', 'sage'),
            'last' => __('Aller à la dernière diapositive', 'sage'),
            'slideX' => __('Aller à la diapositive %s', 'sage'),
            'pageX' => __('Aller à la page %s', 'sage'),
            'play' => __('Lancer la lecture automatique', 'sage'),
            'pause' => __('Mettre en pause la lecture automatique', 'sage'),
            'carousel' => __('carrousel', 'sage'),
            'select' => __('Sélectionner une diapositive', 'sage'),
            'slide' => __('diapositive', 'sage'),
            'slideLabel' => __('%s sur %s', 'sage'),
        ];
    }

    private function attribute(string $key, mixed $default = null): mixed
    {
        return $this->view->getData()['attributes'][$key] ?? $default;
    }
}
