<?php

namespace App\View\Composers;

use App\Services\ThemeSettings;
use Roots\Acorn\View\Composer;

class Logo extends Composer
{
    protected static $views = ['partials.logo'];

    public function logoId(): int
    {
        $settings = app(ThemeSettings::class);

        if ($this->data->get('footer')) {
            $footerId = $settings->integer('logo_footer');

            if ($footerId) {
                return $footerId;
            }
        }

        return $settings->integer('logo');
    }
}
