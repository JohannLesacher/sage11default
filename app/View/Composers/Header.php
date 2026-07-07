<?php

namespace App\View\Composers;

use App\Services\MenuPdf;
use App\Services\ThemeSettings;
use Illuminate\Support\Facades\Vite;
use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Header extends Composer
{
    protected static $views = ['sections.header'];

    public function with(): array
    {
        return [
            'navigation' => $this->getNavigation(),
            'headerLinks' => $this->getHeaderLinks(),
            'languages' => [],
            'logo_url' => $this->getLogoUrl(),
            'is_fixed' => true,
            'has_lang' => false,
            'has_cta' => true,
            'align' => 'right',
        ];
    }

    private function getNavigation(): array
    {
        return Navi::build('primary_navigation')->isEmpty()
            ? []
            : Navi::build('primary_navigation')->all();
    }

    private function getLogoUrl(): string
    {
        $id = (int) ThemeSettings::get('logo_header');

        if ($id) {
            $url = wp_get_attachment_image_url($id, 'medium');
            if ($url) {
                return $url;
            }
        }

        return Vite::asset('resources/images/logo.svg');
    }

    private function getHeaderLinks(): array
    {
        $wineAndSakeUrl = app(MenuPdf::class)->url('wine')
            ?? ThemeSettings::get('header_wine_sake_list_url_en');
        $reservationUrl = ThemeSettings::get('header_reservation_url_en');

        return [
            'wine_and_sake_url' => $wineAndSakeUrl,
            'reservation_url' => $reservationUrl,
        ];
    }
}

/**
 * Add "has-fixed-header" class to body when fixed option is active.
 */
add_filter('body_class', function (array $classes) {
    $classes[] = 'has-fixed-header';

    return $classes;
});
