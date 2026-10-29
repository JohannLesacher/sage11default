<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command
{
    protected $signature = 'make:block {name}
                            {--native : Bloc natif (block.json + éditeur JS, InnerBlocks, sans classe PHP) au lieu de MB Blocks}
                            {--js : Script front (resources/js/blocks)}
                            {--vc : View Composer}
                            {--force : Écrase les fichiers existants}';

    protected $description = 'Crée un bloc Gutenberg (MB Blocks ou natif), auto-enregistré par BlockServiceProvider';

    private string $className;

    private string $slug;

    private string $title;

    public function handle(): int
    {
        $name = Str::ascii($this->argument('name'));
        $this->className = Str::studly($name);
        $this->slug = Str::kebab($this->className);
        $this->title = Str::ucfirst(str_replace('-', ' ', $this->slug));

        $files = $this->files();
        $existing = array_filter(array_keys($files), fn ($path) => File::exists($path));

        if ($existing && ! $this->option('force')) {
            $this->error('Fichiers déjà existants (utiliser --force pour écraser) :');
            foreach ($existing as $path) {
                $this->line("  {$path}");
            }

            return self::FAILURE;
        }

        foreach ($files as $path => $content) {
            File::ensureDirectoryExists(dirname($path));
            File::put($path, $content);
        }

        if ($this->option('native')) {
            $this->registerEditorScript();
        }

        $this->info("Bloc {$this->className} généré (auto-enregistré via BlockServiceProvider).");
        $this->warn('Relancer `npm run dev` ou `npm run build` : les nouveaux assets doivent être connus de Vite avant le rendu du bloc.');

        return self::SUCCESS;
    }

    private function files(): array
    {
        $native = $this->option('native');

        $files = [
            resource_path("views/blocks/{$this->slug}.blade.php") => $native ? $this->nativeView() : $this->metaBoxView(),
            resource_path("css/blocks/{$this->slug}.scss") => ".block-{$this->slug} {\n}\n",
        ];

        if (! $native) {
            $files[app_path("Blocks/{$this->className}.php")] = $this->metaBoxClass();
        }

        if ($native) {
            $files[resource_path("blocks/{$this->slug}/block.json")] = $this->blockJson();
            $files[resource_path("js/admin/blocks/{$this->slug}-editor.js")] = $this->editorScript();
        }

        if ($this->option('js')) {
            $files[resource_path("js/blocks/{$this->slug}.js")] = $this->frontScript();
        }

        if ($this->option('vc')) {
            $files[app_path("View/Composers/Blocks/{$this->className}.php")] = $this->composer();
        }

        return $files;
    }

    private function metaBoxClass(): string
    {
        return <<<PHP
<?php

namespace App\Blocks;

use App\Blocks\Base\MetaBoxBlock;

class {$this->className} extends MetaBoxBlock
{
    protected static function definition(): array
    {
        return [
            'title' => '{$this->title}',
            'icon' => 'admin-generic',
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
                'customClassName' => true,
            ],
            'fields' => [
                //
            ],
        ];
    }
}

PHP;
    }

    private function metaBoxView(): string
    {
        return "<section {!! \$wrapperAttributes('block-{$this->slug}') !!}>\n    <InnerBlocks />\n</section>\n";
    }

    private function nativeView(): string
    {
        return "<section {!! \$wrapperAttributes('block-{$this->slug}') !!}>\n    {!! \$content !!}\n</section>\n";
    }

    private function blockJson(): string
    {
        return json_encode([
            '$schema' => 'https://schemas.wp.org/trunk/block.json',
            'apiVersion' => 3,
            'name' => "sur-mesure/{$this->slug}",
            'title' => $this->title,
            'category' => 'sur-mesure',
            'icon' => 'admin-generic',
            'attributes' => (object) [],
            'supports' => [
                'align' => ['wide', 'full'],
                'anchor' => true,
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)."\n";
    }

    private function editorScript(): string
    {
        return <<<JS
import { registerBlockType } from '@wordpress/blocks';
import metadata from '@blocks/{$this->slug}/block.json';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType(metadata, {
  edit: () => el('div', useBlockProps(), el(InnerBlocks)),

  save: () => el(InnerBlocks.Content),
});

JS;
    }

    private function frontScript(): string
    {
        return "document.querySelectorAll('.block-{$this->slug}').forEach((block) => {\n  //\n});\n";
    }

    private function composer(): string
    {
        return <<<PHP
<?php

namespace App\View\Composers\Blocks;

use App\View\BlockComposer;

class {$this->className} extends BlockComposer
{
    protected static \$views = ['blocks.{$this->slug}'];
}

PHP;
    }

    private function registerEditorScript(): void
    {
        $path = resource_path('js/editor.js');
        $import = "import '@scripts/admin/blocks/{$this->slug}-editor';";

        if (! str_contains(File::get($path), $import)) {
            File::append($path, $import."\n");
        }
    }
}
