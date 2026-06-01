<?php

namespace App\View\Composers;

use App\Services\ThemeSettings;
use Illuminate\Support\Facades\Vite;
use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Footer extends Composer {
    protected static $views = [ 'sections.footer' ];

    protected array $settings;

    protected string $lang;

    public function with(): array {
        $this->settings = get_option( 'theme_settings', [] );
        $this->lang     = function_exists( 'pll_current_language' ) ? pll_current_language() : 'fr';

        return [
            'footerNav' => $this->buildNav( 'footer_navigation' ),
            'legalNav'  => $this->buildNav( 'legal_footer_navigation' ),
            'contact'   => $this->contact(),
            'logo_url'       => $this->getLogoUrl(),
        ];
    }

    protected function contact(): array {
        return [
            'address' => $this->t( 'footer_address' ),
            'phone'   => $this->t( 'footer_phone' ),
            'email'   => $this->t( 'footer_email' ),
        ];
    }

    protected function social(): array {
        return [
            'x'        => $this->settings['footer_social_x'] ?? '',
            'linkedin' => $this->settings['footer_social_linkedin'] ?? '',
            'youtube'  => $this->settings['footer_social_youtube'] ?? '',
        ];
    }

    protected function copyright(): string {
        return '© ' . date( 'Y' ) . ' ' . $this->t( 'footer_copyright_text', 'Sopara. All rights reserved.' );
    }

    protected function t( string $key, string $default = '' ): string {
        return $this->settings["{$key}_{$this->lang}"]
               ?? $this->settings["{$key}_fr"]
                  ?? $default;
    }

    protected function buildNav( string $location ): array {
        $nav = Navi::build( $location );

        return $nav->isEmpty() ? [] : $nav->all();
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
