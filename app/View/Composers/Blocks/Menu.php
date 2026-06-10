<?php

namespace App\View\Composers\Blocks;

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
        $ctaLink = $this->data()['cta_url'] ?? [];
        $ctaText = $this->data()['cta_text'] ?? '';

        return [
            'link' => $ctaLink,
            'text' => $ctaText,
        ];
    }
}
