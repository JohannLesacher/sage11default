<?php

namespace App\Blocks;

use App\Blocks\Base\BlockExtension;
use App\Blocks\Base\ExtendsCoreBlock;

/**
 * Extension de core/gallery : style de bloc « Logos » (resources/blocks/core-gallery/style.scss).
 */
class CoreGallery implements BlockExtension
{
    use ExtendsCoreBlock;

    public static function boot(): void
    {
        static::blockStyle('core/gallery', 'logos', 'Logos', 'core-gallery');
    }
}
