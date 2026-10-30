<?php

namespace App\Blocks\Base;

/**
 * Classe de app/Blocks : boot() est appelé sur init par BlockServiceProvider.
 */
interface BlockExtension
{
    public static function boot(): void;
}
