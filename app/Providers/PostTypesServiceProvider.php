<?php

namespace App\Providers;

use App\PostTypes\HasMetaBoxes;
use Illuminate\Support\ServiceProvider;

class PostTypesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->postTypeClasses() as $class) {
            $this->app->singleton($class);
        }
    }

    public function boot(): void
    {
        add_action(
            'init',
            function (): void {
                /** @var array<string, array<string, mixed>> $postTypes */
                $postTypes = config()->array('post-types.post_types', []);

                foreach ($postTypes as $postType => $args) {
                    /** @var array{plural?: string, singular?: string, slug?: string} $names */
                    $names = $args['names'] ?? [];
                    unset($args['names']);

                    register_extended_post_type($postType, $args, $names);
                }
            },
            100,
        );

        add_action(
            'init',
            function (): void {
                /** @var array<string, array<string, mixed>> $taxonomies */
                $taxonomies = config()->array('post-types.taxonomies', []);

                foreach ($taxonomies as $taxonomy => $args) {
                    /** @var string|list<string> $postTypes */
                    $postTypes = $args['post_types'] ?? [];
                    /** @var array{plural?: string, singular?: string, slug?: string} $names */
                    $names = $args['names'] ?? [];
                    unset($args['post_types'], $args['names']);

                    register_extended_taxonomy($taxonomy, $postTypes, $args, $names);
                }
            },
            100,
        );

        add_filter('rwmb_meta_boxes', [$this, 'loadPostTypesMetas']);
    }

    /**
     * @param  array<array<string, mixed>>  $meta_boxes
     * @return array<array<string, mixed>>
     */
    public function loadPostTypesMetas(array $meta_boxes): array
    {
        foreach ($this->postTypeClasses() as $class) {
            $instance = $this->app->make($class);

            if ($instance instanceof HasMetaBoxes) {
                $meta_boxes = $instance->addMetas($meta_boxes);
            }
        }

        return $meta_boxes;
    }

    /**
     * @return list<class-string>
     */
    private function postTypeClasses(): array
    {
        $classes = [];

        foreach (glob(app_path('PostTypes') . '/*.php') ?: [] as $file) {
            $class = 'App\\PostTypes\\' . pathinfo($file, PATHINFO_FILENAME);

            if (class_exists($class)) {
                $classes[] = $class;
            }
        }

        return $classes;
    }
}
