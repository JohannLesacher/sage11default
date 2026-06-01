<?php

namespace App\Services;

class ThemeSettings {

    public const string OPTION_NAME = 'theme_settings';

    public function registerSettingsPage(): void {
        add_filter( 'mb_settings_pages', function ( array $pages ): array {
            $tabs = [];
            foreach ( $this->languages() as $lang ) {
                $tabs[ $lang ] = strtoupper( $lang );
            }
            $tabs['global'] = 'Global';

            $pages[] = [
                'id'          => 'theme-settings',
                'option_name' => self::OPTION_NAME,
                'menu_title'  => 'Réglages du thème',
                'icon_url'    => 'dashicons-admin-appearance',
                'position'    => 60,
                'tabs'        => $tabs,
                'tab_style'   => 'left',
            ];

            return $pages;
        } );
    }

    public function registerFields(): void {
        add_filter( 'rwmb_meta_boxes', function ( array $meta_boxes ): array {
            foreach ( $this->languages() as $lang ) {
                $meta_boxes[] = [
                    'title'          => 'Footer',
                    'id'             => 'theme-settings-footer-' . $lang,
                    'settings_pages' => 'theme-settings',
                    'tab'            => $lang,
                    'fields'         => $this->footerFields( $lang ),
                ];
            }

            $meta_boxes[] = [
                'id'             => 'theme-settings-logos',
                'title'          => 'Logos',
                'settings_pages' => 'theme-settings',
                'tab'            => 'global',
                'fields'         => [
                    [
                        'id'         => 'logo_header',
                        'name'       => 'Logo header',
                        'type'       => 'single_image',
                        'image_size' => 'medium',
                    ],
                    [
                        'id'         => 'logo_footer',
                        'name'       => 'Logo footer',
                        'type'       => 'single_image',
                        'image_size' => 'medium',
                    ],
                ],
            ];

            $meta_boxes[] = [
                'title'          => 'Réseaux sociaux',
                'id'             => 'theme-settings-social',
                'settings_pages' => 'theme-settings',
                'tab'            => 'global',
                'fields'         => [
                    [
                        'id'   => 'footer_social_x',
                        'name' => 'X (Twitter) URL',
                        'type' => 'url',
                    ],
                    [
                        'id'   => 'footer_social_linkedin',
                        'name' => 'LinkedIn URL',
                        'type' => 'url',
                    ],
                    [
                        'id'   => 'footer_social_youtube',
                        'name' => 'YouTube URL',
                        'type' => 'url',
                    ],
                ],
            ];

            return $meta_boxes;
        } );
    }

    private function languages(): array {
        return function_exists( 'pll_languages_list' )
            ? pll_languages_list( [ 'fields' => 'slug' ] )
            : [ 'en' ];
    }

    private function footerFields( string $lang ): array {
        return [
            [
                'id'   => "footer_address_{$lang}",
                'name' => 'Adresse',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'id'   => "footer_phone_{$lang}",
                'name' => 'Téléphone',
                'type' => 'text',
            ],
            [
                'id'   => "footer_email_{$lang}",
                'name' => 'Email',
                'type' => 'text',
            ],
            [
                'id'          => "footer_copyright_text_{$lang}",
                'name'        => 'Texte copyright',
                'type'        => 'text',
                'placeholder' => 'All rights reserved.',
                'desc'        => "L'année courante est ajoutée automatiquement : © " . date( 'Y' ) . ' {votre texte}',
            ],
        ];
    }

    public static function get( string $key, mixed $default = null ): mixed {
        $options = get_option( self::OPTION_NAME );
        $value   = $options[ $key ] ?? $default;

        return $value !== '' && $value !== null && $value !== false ? $value : $default;
    }
}
