<?php

namespace App\View\Composers\Blocks;

use App\Services\MenuPdf;
use Roots\Acorn\View\Composer;

class Menu extends Composer
{
    protected static $views = [
        'blocks.menu',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function title(): string
    {
        return $this->data()['title'] ?? '';
    }

    public function subtitle(): string
    {
        return $this->data()['subtitle'] ?? '';
    }

    public function iconId(): ?int
    {
        $icon = $this->data()['icon'] ?? null;

        if (is_array($icon)) {
            $icon = $icon[0] ?? null;
        }

        return $icon ? (int) $icon : null;
    }

    public function items(): array
    {
        $items = $this->data()['items'] ?? [];

        return is_array($items) ? $items : [];
    }

    public function cta(): array
    {
        $pdfMenu = $this->data()['pdf_menu'] ?? '';
        $link = $pdfMenu ? app(MenuPdf::class)->url($pdfMenu) : null;

        return [
            'link' => $link ?? $this->data()['cta_url'] ?? '',
            'text' => $this->data()['cta_text'] ?? '',
            'download' => $link !== null,
        ];
    }
}
