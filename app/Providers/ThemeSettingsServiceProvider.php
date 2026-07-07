<?php

namespace App\Providers;

use App\Services\MenuPdf;
use App\Services\ThemeSettings;
use Illuminate\Support\ServiceProvider;

class ThemeSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeSettings::class);
        $this->app->singleton(MenuPdf::class);
    }

    public function boot(ThemeSettings $settings, MenuPdf $menuPdf): void
    {
        $settings->registerSettingsPage();
        $settings->registerFields();

        add_action('init', [$menuPdf, 'registerRewrite']);
        add_filter('query_vars', [$menuPdf, 'registerQueryVar']);
        add_filter('redirect_canonical', [$menuPdf, 'disableCanonicalRedirect']);
        add_action('template_redirect', [$menuPdf, 'serve']);
    }
}
