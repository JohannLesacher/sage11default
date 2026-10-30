<?php

namespace App\View\Composers;

use App\Services\ThemeSettings;
use Roots\Acorn\View\Composer;

class Logo extends Composer
{
    protected static $views = ['partials.logo'];

    public function logoId(): int
    {
        return app(ThemeSettings::class)->integer('logo');
    }
}
