<?php

namespace App\View\Composers;

use App\Services\ThemeSettings;
use Log1x\Navi\Facades\Navi;
use Roots\Acorn\View\Composer;

class Footer extends Composer
{
    protected static $views = ['sections.footer'];

    private const SOCIAL = [
        'x' => 'X (Twitter)',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
    ];

    public function with(): array
    {
        $settings = app(ThemeSettings::class);

        return [
            'footerNav' => $this->buildNav('footer_navigation'),
            'legalNav' => $this->buildNav('legal_footer_navigation'),
            'contact' => $this->contact($settings),
            'social' => $this->social($settings),
            'copyright' => $this->copyright($settings),
        ];
    }

    private function contact(ThemeSettings $settings): array
    {
        $phone = $settings->translated('footer_phone');

        return array_filter([
            'address' => $settings->translated('footer_address'),
            'phone' => $phone,
            'phoneHref' => $phone !== '' ? 'tel:'.preg_replace('/[^\d+]/', '', $phone) : '',
            'email' => $settings->translated('footer_email'),
        ]);
    }

    private function social(ThemeSettings $settings): array
    {
        $links = [];

        foreach (self::SOCIAL as $network => $label) {
            if ($url = $settings->get("footer_social_{$network}")) {
                $links[] = ['network' => $network, 'label' => $label, 'url' => $url];
            }
        }

        return $links;
    }

    private function copyright(ThemeSettings $settings): string
    {
        $default = get_bloginfo('name').'. Tous droits réservés.';

        return '© '.date('Y').' '.$settings->translated('footer_copyright_text', $default);
    }

    private function buildNav(string $location): array
    {
        $nav = Navi::build($location);

        return $nav->isEmpty() ? [] : $nav->all();
    }
}
