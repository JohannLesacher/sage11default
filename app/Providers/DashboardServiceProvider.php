<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

/**
 * Tableau de bord : widget « Prendre en main votre site » (vidéo de formation, config/cereal-one.php)
 * et widgets WordPress par défaut retirés pour les comptes clients.
 */
class DashboardServiceProvider extends ServiceProvider
{
    private const WIDGET = 'cereal_one_video';

    /** Widget => contexte (colonne) */
    private const HIDDEN_WIDGETS = [
        'dashboard_primary' => 'side',
        'dashboard_quick_press' => 'side',
        'dashboard_site_health' => 'normal',
        'dashboard_right_now' => 'normal',
        'dashboard_activity' => 'normal',
    ];

    public function boot(): void
    {
        add_action('wp_dashboard_setup', [$this, 'setupWidgets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAssets']);
    }

    public function setupWidgets(): void
    {
        if (current_user_can('edit_pages')) {
            wp_add_dashboard_widget(self::WIDGET, 'Prendre en main votre site', [$this, 'renderWidget']);
            $this->moveWidgetFirst();
        }

        if (! current_user_can('manage_options')) {
            foreach (self::HIDDEN_WIDGETS as $widget => $context) {
                remove_meta_box($widget, 'dashboard', $context);
            }
        }
    }

    public function renderWidget(): void
    {
        echo View::make('admin.dashboard-video')->render();
    }

    public function enqueueAssets(string $hookSuffix): void
    {
        if ($hookSuffix !== 'index.php' || ! current_user_can('edit_pages')) {
            return;
        }

        wp_enqueue_style('cereal-one-video', Vite::asset('resources/css/admin/dashboard-video.scss'), [], null);
        wp_enqueue_script_module('cereal-one-video', Vite::asset('resources/js/admin/dashboard-video.js'), [], null);
    }

    private function moveWidgetFirst(): void
    {
        /** @var array{dashboard: array{normal: array{core: array<string, mixed>}}} $wp_meta_boxes */
        global $wp_meta_boxes;

        $core = $wp_meta_boxes['dashboard']['normal']['core'];

        if (($core[self::WIDGET] ?? null) === null) {
            return;
        }

        $wp_meta_boxes['dashboard']['normal']['core'] = [self::WIDGET => $core[self::WIDGET]] + $core;
    }
}
