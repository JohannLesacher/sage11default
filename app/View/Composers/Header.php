<?php

namespace App\View\Composers;

use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Header extends Composer {
    protected static $views = [ 'sections.header' ];

    public function with(): array {
        return [
            'navigation' => $this->getNavigation(),
            'languages'  => $this->getLanguages(),
            'is_fixed'   => config( 'theme.header.fixed' ),
            'has_lang'   => config( 'theme.header.lang' ),
            'cta'        => config( 'theme.header.cta' ),
            'align'      => config( 'theme.header.align' ),
        ];
    }

    private function getNavigation(): array {
        $nav = Navi::build( 'primary_navigation' );

        return $nav->isEmpty() ? [] : $nav->all();
    }

    private function getLanguages(): array {
        return function_exists( 'pll_the_languages' )
            ? ( pll_the_languages( [ 'raw' => 1 ] ) ?: [] )
            : [];
    }
}
