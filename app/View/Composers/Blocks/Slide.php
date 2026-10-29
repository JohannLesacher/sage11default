<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Slide extends Composer
{
    protected static $views = ['blocks.slide'];

    public function animated(): bool
    {
        return (bool) ($this->view->getData()['attributes']['animateOnScroll'] ?? false);
    }

    public function animationType(): string
    {
        return esc_attr($this->view->getData()['attributes']['animationType'] ?? 'fade-up');
    }
}
