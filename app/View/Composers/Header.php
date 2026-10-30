<?php

namespace App\View\Composers;

use App\Services\ThemeConfig;
use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Header extends Composer
{
    protected static $views = ['sections.header'];

    /**
     * @return array<string, mixed>
     */
    public function with(): array
    {
        $header = ThemeConfig::header();

        return [
            'navigation' => $this->getNavigation(),
            'languages' => $this->getLanguages(),
            'is_fixed' => $header['fixed'],
            'has_lang' => $header['lang'],
            'cta' => $header['cta'],
            'align' => $header['align'],
        ];
    }

    /**
     * @return array<mixed>
     */
    private function getNavigation(): array
    {
        $nav = Navi::build('primary_navigation');

        return $nav->isEmpty() ? [] : $nav->all();
    }

    /**
     * @return array<mixed>
     */
    private function getLanguages(): array
    {
        $languages = function_exists('pll_the_languages') ? pll_the_languages(['raw' => 1]) : null;

        return is_array($languages) ? $languages : [];
    }
}
