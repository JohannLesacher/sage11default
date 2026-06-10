<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class UpcomingEvent extends Composer
{
    protected static $views = [
        'blocks.upcoming-event',
    ];

    private function data(): array
    {
        return $this->view->getData()['data'] ?? [];
    }

    public function imageId(): ?int
    {
        $image = $this->data()['image'] ?? null;

        if (is_array($image)) {
            $image = $image[0] ?? null;
        }

        return $image ? (int) $image : null;
    }

    public function title(): string
    {
        return $this->data()['title'] ?? '';
    }

    public function dateLabel(): string
    {
        $date = $this->data()['date'] ?? '';

        if (! $date) {
            return '';
        }

        $timestamp = strtotime($date);

        return $timestamp ? date_i18n('M j', $timestamp) : $date;
    }

    public function time(): string
    {
        return $this->data()['time'] ?? '';
    }

    public function price(): string
    {
        return $this->data()['price'] ?? '';
    }

    public function cta(): array
    {
        return [
            'link' => $this->data()['cta_url'] ?? '',
            'text' => $this->data()['cta_text'] ?? '',
        ];
    }
}
