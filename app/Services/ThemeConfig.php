<?php

namespace App\Services;

/**
 * Accès typé à config/theme.php : les formes décrites ici sont celles attendues dans le fichier.
 */
class ThemeConfig
{
    /**
     * @return array{fixed: bool, align: string, lang: bool, cta: false|array{label: string, url: string}}
     */
    public static function header(): array
    {
        /** @var array{fixed: bool, align: string, lang: bool, cta: false|array{label: string, url: string}} */
        return config()->array('theme.header');
    }

    /**
     * @return array<string, string> emplacement => libellé
     */
    public static function menus(): array
    {
        /** @var array<string, string> */
        return config()->array('theme.menus');
    }

    /**
     * @return array<string, array{width: int<0, max>, height: int<0, max>, crop?: bool, label?: string}>
     */
    public static function imageSizes(): array
    {
        /** @var array<string, array{width: int<0, max>, height: int<0, max>, crop?: bool, label?: string}> */
        return config()->array('theme.image_sizes');
    }

    /**
     * @return list<string> noms des blocs (core/heading…)
     */
    public static function animationBlocks(): array
    {
        /** @var list<string> */
        return config()->array('theme.animation.blocks');
    }
}
