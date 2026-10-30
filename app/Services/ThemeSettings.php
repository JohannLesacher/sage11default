<?php

namespace App\Services;

class ThemeSettings
{
    private const OPTION = 'theme_settings';

    public function get(string $key, mixed $default = null): mixed
    {
        $value = $this->options()[$key] ?? null;

        return $value === null || $value === '' ? $default : $value;
    }

    public function string(string $key, string $default = ''): string
    {
        $value = $this->get($key);

        return is_scalar($value) ? (string) $value : $default;
    }

    public function integer(string $key, int $default = 0): int
    {
        $value = $this->get($key);

        return is_numeric($value) ? (int) $value : $default;
    }

    public function translated(string $key, string $default = ''): string
    {
        $lang = function_exists('pll_current_language') ? pll_current_language() : 'fr';
        $lang = is_string($lang) && $lang !== '' ? $lang : 'fr';

        return $this->string("{$key}_{$lang}", $this->string("{$key}_fr", $default));
    }

    public function registerSettingsPage(): void
    {
        add_filter(
            'mb_settings_pages',
            /**
             * @param  array<array<string, mixed>>  $pages
             * @return array<array<string, mixed>>
             */
            function (array $pages): array {
                $tabs = [];
                foreach ($this->languages() as $lang) {
                    $tabs[$lang] = strtoupper($lang);
                }
                $tabs['global'] = 'Global';

                $pages[] = [
                    'id' => 'theme-settings',
                    'option_name' => self::OPTION,
                    'menu_title' => 'Réglages du thème',
                    'icon_url' => 'dashicons-admin-appearance',
                    'position' => 60,
                    'tabs' => $tabs,
                    'tab_style' => 'left',
                ];

                return $pages;
            },
        );
    }

    public function registerFields(): void
    {
        add_filter(
            'rwmb_meta_boxes',
            /**
             * @param  array<array<string, mixed>>  $meta_boxes
             * @return array<array<string, mixed>>
             */
            function (array $meta_boxes): array {
                foreach ($this->languages() as $lang) {
                    $meta_boxes[] = [
                        'title' => 'Footer',
                        'id' => 'theme-settings-footer-' . $lang,
                        'settings_pages' => 'theme-settings',
                        'tab' => $lang,
                        'fields' => $this->footerFields($lang),
                    ];
                }

                $meta_boxes[] = [
                    'title' => 'Identité',
                    'id' => 'theme-settings-identity',
                    'settings_pages' => 'theme-settings',
                    'tab' => 'global',
                    'fields' => [
                        [
                            'id' => 'logo',
                            'name' => 'Logo',
                            'type' => 'single_image',
                            'desc' => 'Remplace le logo SVG du thème dans le header et le footer.',
                        ],
                    ],
                ];

                $meta_boxes[] = [
                    'title' => 'Réseaux sociaux',
                    'id' => 'theme-settings-social',
                    'settings_pages' => 'theme-settings',
                    'tab' => 'global',
                    'fields' => [
                        [
                            'id' => 'footer_social_x',
                            'name' => 'X (Twitter) URL',
                            'type' => 'url',
                        ],
                        [
                            'id' => 'footer_social_linkedin',
                            'name' => 'LinkedIn URL',
                            'type' => 'url',
                        ],
                        [
                            'id' => 'footer_social_youtube',
                            'name' => 'YouTube URL',
                            'type' => 'url',
                        ],
                    ],
                ];

                $meta_boxes[] = [
                    'title' => 'Carte',
                    'id' => 'theme-settings-carte',
                    'settings_pages' => 'theme-settings',
                    'tab' => 'global',
                    'fields' => [
                        [
                            'id' => 'carte_fournisseur',
                            'name' => 'Fournisseur de carte',
                            'type' => 'select',
                            'options' => [
                                'osm' => 'OpenStreetMap',
                                'google' => 'Google Maps',
                            ],
                            'std' => 'osm',
                            'desc' => 'Utilisé par tous les blocs « Carte » du site.',
                        ],
                    ],
                ];

                return $meta_boxes;
            },
        );
    }

    /**
     * @return array<mixed>
     */
    private function options(): array
    {
        $options = get_option(self::OPTION);

        return is_array($options) ? $options : [];
    }

    /**
     * @return list<string>
     */
    private function languages(): array
    {
        $languages = function_exists('pll_languages_list') ? pll_languages_list(['fields' => 'slug']) : null;

        return is_array($languages) ? array_values(array_filter($languages, 'is_string')) : ['fr'];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function footerFields(string $lang): array
    {
        return [
            [
                'id' => "footer_address_{$lang}",
                'name' => 'Adresse',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'id' => "footer_phone_{$lang}",
                'name' => 'Téléphone',
                'type' => 'text',
            ],
            [
                'id' => "footer_email_{$lang}",
                'name' => 'Email',
                'type' => 'text',
            ],
            [
                'id' => "footer_copyright_text_{$lang}",
                'name' => 'Texte copyright',
                'type' => 'text',
                'placeholder' => 'Tous droits réservés.',
                'desc' => "L'année courante est ajoutée automatiquement : © " . date('Y') . ' {votre texte}',
            ],
        ];
    }
}
