<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command {
    protected $signature = 'make:block {name} {--js} {--vc} {--force : Écrase les fichiers existants}';
    protected $description = 'Crée un bloc MB Blocks (Sage 11)';

    public function handle(): void {
        $name      = Str::ascii( $this->argument( 'name' ) );
        $className = Str::studly( $name );
        $slug      = Str::kebab( $name );
        $hasJs     = $this->option( 'js' );
        $hasVC     = $this->option( 'vc' );

        $existing = array_filter( $this->targetPaths( $className, $slug, $hasJs, $hasVC ), fn ( $path ) => File::exists( $path ) );

        if ( $existing && ! $this->option( 'force' ) ) {
            $this->error( 'Fichiers déjà existants (utiliser --force pour écraser) :' );
            foreach ( $existing as $path ) {
                $this->line( "  {$path}" );
            }

            return;
        }

        $this->generatePhpClass( $className, $slug, $hasJs );
        $this->generateView( $slug );
        $this->generateStyle( $slug );

        if ( $hasJs ) {
            $this->generateScript( $slug );
        }

        if ( $hasVC ) {
            $this->generateViewComposer( $slug );
        }

        $this->info( "Bloc {$className} généré ! (Auto-enregistré via BlockServiceProvider)" );
    }

    protected function generatePhpClass( $className, $slug, $hasJs ): void {
        $path  = app_path( "Blocks/{$className}.php" );
        $title = Str::ucfirst( str_replace( '-', ' ', $slug ) );

        // Construction de la closure enqueue_assets
        $enqueueLogic = "wp_enqueue_style('{$slug}', Vite::asset('resources/css/blocks/{$slug}.scss'), [], null);";

        if ( $hasJs ) {
            $enqueueLogic .= "\n";
            $enqueueLogic .= "\n                if ( ! is_admin() ) {";
            $enqueueLogic .= "\n                    wp_enqueue_script_module('{$slug}', Vite::asset('resources/js/blocks/{$slug}.js'), [], null);";
            $enqueueLogic .= "\n                }";
        }

        $stub = <<<PHP
<?php

namespace App\Blocks;

use App\Services\BlockEngine;
use Illuminate\Support\Facades\Vite;

class {$className} extends BlockEngine
{
    public static function register_block(array \$meta_boxes): array
    {
        \$meta_boxes[] = [
            'title'           => '{$title}',
            'id'              => '{$slug}',
            'type'            => 'block',
            'category'        => 'sur-mesure',
            'context'         => 'normal',
            'icon'            => 'admin-generic',
            'render_callback' => [parent::class, 'renderBlock'],
            'enqueue_assets'  => function () {
                {$enqueueLogic}
            },
            'supports'        => [
                'align'           => true,
                'anchor'          => true,
                'customClassName' => true,
                'reusable'        => true,
            ],
            'fields'          => [
                //
            ]
        ];

        return \$meta_boxes;
    }
}
PHP;

        File::put( $path, $stub );
    }

    protected function generateView( $slug ): void {
        $path = resource_path( "views/blocks/{$slug}.blade.php" );
        File::ensureDirectoryExists( dirname( $path ) );
        File::put( $path,
            "<section class=\"block-{$slug} {{ \$attributes['className'] ?? '' }} align{{ \$attributes['align'] ?? 'normal' }}\">\n    <InnerBlocks />\n</section>\n" );
    }

    protected function generateStyle( $slug ): void {
        $path = resource_path( "css/blocks/{$slug}.scss" );
        File::ensureDirectoryExists( dirname( $path ) );
        File::put( $path, ".block-{$slug} {\n    // Styles\n}" );
    }

    protected function generateScript( $slug ): void {
        $path = resource_path( "js/blocks/{$slug}.js" );
        File::ensureDirectoryExists( dirname( $path ) );
        File::put( $path, "console.log('Block {$slug} initialized');" );
    }

    private function targetPaths( string $className, string $slug, bool $hasJs, bool $hasVC ): array {
        return array_filter( [
            app_path( "Blocks/{$className}.php" ),
            resource_path( "views/blocks/{$slug}.blade.php" ),
            resource_path( "css/blocks/{$slug}.scss" ),
            $hasJs ? resource_path( "js/blocks/{$slug}.js" ) : null,
            $hasVC ? app_path( 'View/Composers/Blocks/' . Str::studly( $slug ) . '.php' ) : null,
        ] );
    }

    private function generateViewComposer( string $slug ): void {
        $className = Str::studly( $slug );
        $path      = app_path( "View/Composers/Blocks/{$className}.php" );

        // Création du dossier s'il n'existe pas
        if ( ! File::isDirectory( dirname( $path ) ) ) {
            File::makeDirectory( dirname( $path ), 0755, true );
        }

        $stub = <<<PHP
<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class {$className} extends Composer
{
    protected static \$views = ['blocks.{$slug}'];

    private function field(string \$key): string
    {
        return trim((string) (\$this->view->getData()['data'][\$key] ?? ''));
    }
}
PHP;

        File::put( $path, $stub );
    }
}
