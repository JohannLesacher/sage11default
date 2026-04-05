<?php

namespace App\View\Composers;

use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Footer extends Composer {
    protected static $views = [ 'sections.footer' ];

    public function with(): array {
        return [
            'footerNav' => $this->getFooterNav(),
            'legalNav'  => $this->getLegalNav(),
        ];
    }

    private function getFooterNav(): array {
        return Navi::build( 'footer_navigation' )->isEmpty()
            ? []
            : Navi::build( 'footer_navigation' )->all();
    }

    private function getLegalNav(): array {
        return Navi::build( 'legal_footer_navigation' )->isEmpty()
            ? []
            : Navi::build( 'legal_footer_navigation' )->all();
    }
}
