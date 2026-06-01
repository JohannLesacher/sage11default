<?php

namespace App\View\Composers;

use App\Services\ThemeSettings;
use Illuminate\Support\Facades\Vite;
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
            'logo_url'   => $this->getLogoUrl(),
            'is_fixed'   => true,
            'has_lang'   => false,
            'has_cta'    => true,
            'align'      => 'right',
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
}

/**
 * Add "has-fixed-header" class to body when fixed option is active.
 */
add_filter('body_class', function (array $classes) {
    $classes[] = 'has-fixed-header';
    return $classes;
});
