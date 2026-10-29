<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class Slide extends BlockComposer
{
    protected static $views = ['blocks.slide'];

    public function classes(): string
    {
        return 'splide__slide block-slide'.($this->animated() ? ' is-animated is-animated--slide' : '');
    }

    public function animation(): ?string
    {
        return $this->animated() ? $this->attribute('animationType', 'fade-up') : null;
    }

    private function animated(): bool
    {
        return (bool) $this->attribute('animateOnScroll', false);
    }
}
