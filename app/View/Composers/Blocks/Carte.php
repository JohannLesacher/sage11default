<?php

namespace App\View\Composers\Blocks;

use App\Services\ThemeSettings;
use App\View\BlockComposer;

class Carte extends BlockComposer
{
    protected static $views = ['blocks.carte'];

    private const ZOOM_MIN = 10;

    private const ZOOM_MAX = 18;

    private const ZOOM_DEFAUT = 15;

    public function estComplet(): bool
    {
        return is_numeric($this->field('latitude')) && is_numeric($this->field('longitude'));
    }

    public function titre(): string
    {
        $adresse = $this->field('adresse');

        return $adresse !== '' ? "Carte : {$adresse}" : 'Carte';
    }

    public function embedUrl(): string
    {
        return $this->estGoogle() ? $this->embedGoogle() : $this->embedOsm();
    }

    private function embedOsm(): string
    {
        $lat = $this->latitude();
        $lon = $this->longitude();
        $deltaLon = 0.01 * (2 ** (self::ZOOM_DEFAUT - $this->zoom()));
        $deltaLat = $deltaLon / 2;

        $bbox = implode(',', array_map([$this, 'format'], [
            $lon - $deltaLon,
            $lat - $deltaLat,
            $lon + $deltaLon,
            $lat + $deltaLat,
        ]));

        return sprintf(
            'https://www.openstreetmap.org/export/embed.html?bbox=%s&layer=mapnik&marker=%s,%s',
            $bbox,
            $this->format($lat),
            $this->format($lon),
        );
    }

    private function embedGoogle(): string
    {
        return sprintf(
            'https://maps.google.com/maps?q=%s,%s&z=%d&hl=fr&output=embed',
            $this->format($this->latitude()),
            $this->format($this->longitude()),
            $this->zoom(),
        );
    }

    private function estGoogle(): bool
    {
        return app(ThemeSettings::class)->get('carte_fournisseur', 'osm') === 'google';
    }

    private function latitude(): float
    {
        return (float) $this->field('latitude');
    }

    private function longitude(): float
    {
        return (float) $this->field('longitude');
    }

    private function zoom(): int
    {
        $zoom = $this->field('zoom');

        if (! is_numeric($zoom)) {
            return self::ZOOM_DEFAUT;
        }

        return max(self::ZOOM_MIN, min(self::ZOOM_MAX, (int) $zoom));
    }

    private function format(float $nombre): string
    {
        return sprintf('%.6F', $nombre);
    }
}
