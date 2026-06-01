<?php

namespace App\Providers;

use App\Services\ThemeSettings;
use Illuminate\Support\ServiceProvider;

class ThemeSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeSettings::class);
    }

    public function boot(ThemeSettings $settings): void
    {
        $settings->registerSettingsPage();
        $settings->registerFields();
    }
}
