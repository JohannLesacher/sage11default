<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeBlockCommand extends Command {
    protected $signature = 'make:block {name} {--js} {--vc}';
    protected $description = 'Crée un bloc MB Blocks (Sage 11)';

    public function handle(): void {
        $name      = Str::ascii( $this->argument( 'name' ) );
        $className = Str::studly( $name );
        $slug      = Str::kebab( $name );
        $hasJs     = $this->option( 'js' );
        $hasVC     = $this->option( 'vc' );

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
        $path = app_path( "Blocks/{$className}.php" );

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
    public static function register_block(\$meta_boxes)
    {
        \$meta_boxes[] = [
            'title'           => '{$className}',
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
            "<article class=\"block-{$slug} {{ \$attributes['className'] ?? '' }}\">\n    <InnerBlocks />\n</article>" );
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

    protected function registerInServiceProvider( $className ): void {
        $path = app_path( 'Providers/BlockServiceProvider.php' );
        if ( ! File::exists( $path ) ) {
            return;
        }

        $content = File::get( $path );

        // Ajout de l'import use
        if ( ! str_contains( $content, "use App\Blocks\\{$className};" ) ) {
            $content = str_replace( "namespace App\Providers;",
                "namespace App\Providers;\nuse App\Blocks\\{$className};", $content );
        }

        // Ajout au filter
        $search = "add_filter( 'rwmb_meta_boxes', [";
        if ( str_contains( $content, $search ) ) {
            $content = str_replace( $search,
                "add_filter( 'rwmb_meta_boxes', [ {$className}::class, 'register_block' ] );\n        add_filter( 'rwmb_meta_boxes', [",
                $content );
        }

        File::put( $path, $content );
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

class {$className} extends Composer
{
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static \$views = [
        'blocks.{$slug}',
    ];

    /**
     * Data to be passed to view before rendering.
     *
     * @return array
     */
    public function with()
    {
        return [
            // 'items' => get_field('items'),
        ];
    }
}
PHP;

        File::put( $path, $stub );
    }
}
