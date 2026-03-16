<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeHeaderCommand extends Command {
    protected $signature = 'make:header
                            {--fixed : Positionnement fixed (overlay)}
                            {--align=right : Alignement du menu (left, center, right)}
                            {--lang : Inclure la logique Polylang}
                            {--cta : Inclure la zone CTA}';

    protected $description = 'Scaffold a header system with custom options';

    public function handle(): void {
        $options = [
            'fixed' => $this->option('fixed'),
            'align' => $this->option('align'),
            'lang'  => $this->option('lang'),
            'cta'   => $this->option('cta'),
        ];

        $this->generateComposer($options);

        $this->info('🚀 Header Composer generated in app/View/Composers/Header.php');
    }

    protected function generateComposer(array $options): void {
        $path = app_path('View/Composers/Header.php');
        $directory = dirname($path);

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $isFixed = $options['fixed'] ? 'true' : 'false';
        $hasCta  = $options['cta'] ? 'true' : 'false';
        $align   = $options['align'];

        // Polylang logic
        $hasLang      = $options['lang'] ? 'true' : 'false';
        $langDataLine = $options['lang'] ? "'languages' => \$this->getLanguages()," : "'languages' => [],";
        $langMethod   = $options['lang'] ? $this->getLangMethodStub() : "";

        // Body Class logic (uniquement si --fixed)
        $bodyClassFilter = "";
        if ($options['fixed']) {
            $bodyClassFilter = <<<PHP

/**
 * Add "has-fixed-header" class to body when fixed option is active.
 */
add_filter('body_class', function (array \$classes) {
    \$classes[] = 'has-fixed-header';
    return \$classes;
});
PHP;
        }

        $stub = <<<PHP
<?php

namespace App\View\Composers;

use Roots\Acorn\View\Composer;
use Log1x\Navi\Facades\Navi;

class Header extends Composer
{
    protected static \$views = ['sections.header'];

    public function with(): array
    {
        return [
            'navigation' => \$this->getNavigation(),
            {$langDataLine}
            'logo'       => get_custom_logo(),
            'is_fixed'   => {$isFixed},
            'has_lang'   => {$hasLang},
            'has_cta'    => {$hasCta},
            'align'      => '{$align}',
        ];
    }

    private function getNavigation(): array
    {
        return Navi::build('primary_navigation')->isEmpty()
            ? []
            : Navi::build('primary_navigation')->all();
    }
{$langMethod}
}
{$bodyClassFilter}
PHP;

        File::put($path, $stub);
    }

    private function getLangMethodStub(): string {
        return <<<PHP

    private function getLanguages(): array
    {
        return function_exists('pll_the_languages')
            ? (pll_the_languages(['raw' => 1]) ?: [])
            : [];
    }
PHP;
    }
}
