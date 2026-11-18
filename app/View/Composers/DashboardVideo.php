<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;

class DashboardVideo extends Composer
{
    /** @var string[] */
    protected static $views = ['admin.dashboard-video'];

    public function videoUrl(): string
    {
        return config()->string('cereal-one.video.url');
    }

    /**
     * @return list<array{time: int, timecode: string, label: string}>
     */
    public function chapters(): array
    {
        /** @var list<array{time: int, label: string}> $chapters */
        $chapters = config()->array('cereal-one.video.chapters');

        return array_map(fn (array $chapter): array => [
            'time' => $chapter['time'],
            'timecode' => sprintf('%d:%02d', intdiv($chapter['time'], 60), $chapter['time'] % 60),
            'label' => $chapter['label'],
        ], $chapters);
    }

    public function supportEmail(): string
    {
        return config()->string('cereal-one.support_email');
    }
}
