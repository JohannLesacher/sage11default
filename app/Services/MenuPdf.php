<?php

namespace App\Services;

class MenuPdf
{
    public const string QUERY_VAR = 'taste_menu_pdf';

    public const array TYPES = ['wine', 'food'];

    public function registerRewrite(): void
    {
        add_rewrite_rule(
            '^menus/(wine|food)\.pdf$',
            'index.php?'.self::QUERY_VAR.'=$matches[1]',
            'top'
        );
    }

    public function registerQueryVar(array $vars): array
    {
        $vars[] = self::QUERY_VAR;

        return $vars;
    }

    public function disableCanonicalRedirect(string|false $redirect): string|false
    {
        return get_query_var(self::QUERY_VAR) ? false : $redirect;
    }

    public function serve(): void
    {
        $type = get_query_var(self::QUERY_VAR);

        if (! in_array($type, self::TYPES, true)) {
            return;
        }

        $path = $this->path($type);

        if (! $path) {
            global $wp_query;
            $wp_query->set_404();
            status_header(404);

            return;
        }

        nocache_headers();
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="'.$type.'-menu.pdf"');
        header('Content-Length: '.filesize($path));
        readfile($path);
        exit;
    }

    public function url(string $type): ?string
    {
        return $this->attachmentId($type) ? home_url("/menus/{$type}.pdf") : null;
    }

    public function attachmentId(string $type): ?int
    {
        $value = ThemeSettings::get("menu_pdf_{$type}");

        if (is_array($value)) {
            $value = reset($value);
        }

        return $value ? (int) $value : null;
    }

    private function path(string $type): ?string
    {
        $id = $this->attachmentId($type);

        if (! $id) {
            return null;
        }

        $path = get_attached_file($id);

        return $path && file_exists($path) ? $path : null;
    }
}
