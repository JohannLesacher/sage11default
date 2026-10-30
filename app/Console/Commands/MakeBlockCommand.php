<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command
{
    protected $signature = 'make:block {name}
                            {--inline : Édition directe dans le bloc (RichText) au lieu des réglages + aperçu serveur}
                            {--inner : Bloc conteneur (InnerBlocks)}
                            {--js : Script front (script.js)}
                            {--vc : View Composer (logique de la vue)}
                            {--force : Écrase les fichiers existants}';

    protected $description = 'Crée un bloc natif (block.json + editor.jsx + vue Blade), auto-enregistré';

    private string $className;

    private string $slug;

    private string $title;

    public function handle(): int
    {
        $name = $this->argument('name');
        $name = Str::ascii(is_string($name) ? $name : '');
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

        $this->info("Bloc sur-mesure/{$this->slug} généré (auto-enregistré).");
        $this->warn(
            'Relancer `npm run dev` ou `npm run build` : les nouveaux assets doivent être connus de Vite avant le rendu du bloc.',
        );

        return self::SUCCESS;
    }

    /**
     * @return array<string, string> chemin => contenu
     */
    private function files(): array
    {
        $files = [
            resource_path("blocks/{$this->slug}/block.json") => $this->blockJson(),
            resource_path("blocks/{$this->slug}/editor.jsx") => match (true) {
                $this->option('inner') => $this->innerEditor(),
                $this->option('inline') => $this->inlineEditor(),
                default => $this->previewEditor(),
            },
            resource_path("views/blocks/{$this->slug}.blade.php") => $this->view(),
            resource_path("blocks/{$this->slug}/style.scss") => ".block-{$this->slug} {\n}\n",
        ];

        if ($this->option('js')) {
            $files[resource_path("blocks/{$this->slug}/script.js")] = $this->frontScript();
        }

        if ($this->option('vc')) {
            $files[app_path("View/Composers/Blocks/{$this->className}.php")] = $this->composer();
        }

        return $files;
    }

    private function blockJson(): string
    {
        $attributes = $this->option('inner') ? (object) [] : ['titre' => ['type' => 'string', 'default' => '']];

        return json_encode(
            [
                '$schema' => 'https://schemas.wp.org/trunk/block.json',
                'apiVersion' => 3,
                'name' => "sur-mesure/{$this->slug}",
                'title' => $this->title,
                'category' => 'sur-mesure',
                'icon' => 'admin-generic',
                'attributes' => $attributes,
                'supports' => [
                    'align' => ['wide', 'full'],
                    'anchor' => true,
                ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
        ) . "\n";
    }

    private function view(): string
    {
        $body = $this->option('inner')
            ? '{!! $content !!}'
            : "@if(\$attributes['titre'])\n        <h2>{!! esc_html(\$attributes['titre']) !!}</h2>\n    @elseif(\$is_preview)\n        <p>Renseignez le titre.</p>\n    @endif";

        return "<section {!! \$wrapperAttributes('block-{$this->slug}') !!}>\n    {$body}\n</section>\n";
    }

    private function previewEditor(): string
    {
        return <<<'JSX'
            import { registerBlockType } from '@wordpress/blocks';
            import { InspectorControls } from '@wordpress/block-editor';
            import { PanelBody, TextControl } from '@wordpress/components';
            import ServerPreview, { binder } from '@scripts/admin/components/server-preview';
            import metadata from './block.json';

            registerBlockType(metadata, {
              edit: ({ name, attributes, setAttributes }) => {
                const bind = binder(attributes, setAttributes);

                return (
                  <>
                    <InspectorControls>
                      <PanelBody title="Réglages">
                        <TextControl label="Titre" {...bind('titre')} />
                      </PanelBody>
                    </InspectorControls>

                    <ServerPreview name={name} attributes={attributes} />
                  </>
                );
              },
            });

            JSX;
    }

    private function inlineEditor(): string
    {
        return <<<JSX
            import { registerBlockType } from '@wordpress/blocks';
            import { RichText, useBlockProps } from '@wordpress/block-editor';
            import metadata from './block.json';

            /**
             * Édition directe : le JSX reprend le balisage de la vue Blade (à garder en phase).
             */
            registerBlockType(metadata, {
              edit: ({ attributes, setAttributes }) => (
                <section {...useBlockProps({ className: 'block-{$this->slug}' })}>
                  <RichText
                    tagName="h2"
                    placeholder="Titre"
                    allowedFormats={[]}
                    value={attributes.titre}
                    onChange={(titre) => setAttributes({ titre })}
                  />
                </section>
              ),
            });

            JSX;
    }

    private function innerEditor(): string
    {
        return <<<'JSX'
            import { registerBlockType } from '@wordpress/blocks';
            import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
            import metadata from './block.json';

            registerBlockType(metadata, {
              edit: () => (
                <div {...useBlockProps()}>
                  <InnerBlocks />
                </div>
              ),

              save: () => <InnerBlocks.Content />,
            });

            JSX;
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
}
