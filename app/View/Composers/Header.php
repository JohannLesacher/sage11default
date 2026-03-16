<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Log1x\Navi\Facades\Navi;

class Header extends Composer
{
    protected static $views = ['sections.header'];

    public function with(): array
    {
        return [
            'navigation' => $this->getNavigation(),
            'languages' => [],
            'logo'       => get_custom_logo(),
            'is_fixed'   => true,
            'has_lang'   => false,
            'has_cta'    => false,
            'align'      => 'left',
        ];
    }

    private function getNavigation(): array
    {
        return Navi::build('primary_navigation')->isEmpty()
            ? []
            : Navi::build('primary_navigation')->all();
    }

}

/**
 * Add "has-fixed-header" class to body when fixed option is active.
 */
add_filter('body_class', function (array $classes) {
    $classes[] = 'has-fixed-header';
    return $classes;
});