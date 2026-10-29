<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class TexteDefilant extends BlockComposer
{
    protected static $views = ['blocks.texte-defilant'];

    private const TAGS = ['div', 'p', 'h1', 'h2', 'h3', 'span'];

    private const VITESSES = ['lent', 'normal', 'rapide'];

    private const DIRECTIONS = ['gauche', 'droite'];

    public function classes(): string
    {
        return 'block-texte-defilant'.($this->pause() ? ' has-pause' : '');
    }

    public function texte(): string
    {
        return esc_html($this->field('texte'));
    }

    public function separateur(): string
    {
        return esc_html($this->field('separateur'));
    }

    public function ariaLabel(): string
    {
        return $this->field('texte');
    }

    public function tag(): string
    {
        return $this->whitelist('tag', self::TAGS, 'div');
    }

    public function vitesse(): string
    {
        return $this->whitelist('vitesse', self::VITESSES, 'normal');
    }

    public function direction(): string
    {
        return $this->whitelist('direction', self::DIRECTIONS, 'gauche');
    }

    public function pause(): bool
    {
        return (bool) $this->field('pause');
    }

    private function whitelist(string $key, array $allowed, string $default): string
    {
        $value = $this->field($key);

        return in_array($value, $allowed, true) ? $value : $default;
    }
}
