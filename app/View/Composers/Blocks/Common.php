<?php

namespace App\View\Composers\Blocks;

use Roots\Acorn\View\Composer;

class Common extends Composer {
    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'blocks.*',
    ];

    private function getParsedAttributes(): string {
        $attributes = $this->view->getData()['attributes'] ?? [];

        $attributes['layout'] = null; // sinon ça génère une erreur dans get_block_wrapper_attributes

        if ( isset( $attributes['style'] ) && is_array( $attributes['style'] ) ) {
            $style_json = json_encode( $attributes['style'] );

            $converted_json = preg_replace_callback(
                '/var:preset\|([a-zA-Z0-9_-]+)\|([a-zA-Z0-9_-]+)/',
                function ( $matches ) {
                    return "var(--wp--preset--{$matches[1]}--{$matches[2]})";
                },
                $style_json
            );

            $style_array         = json_decode( $converted_json, true );
            $styles              = wp_style_engine_get_styles( $style_array );
            $attributes['style'] = $styles['css'] ?? '';
        }

        return get_block_wrapper_attributes( $attributes );
    }

    /**
     * Retrieve the block class names
     */
    public function blockClass(): string {
        $attributesParsed = $this->getParsedAttributes();
        $classNames       = "";

        if ( preg_match( '/class="([^"]+)"/', $attributesParsed, $matches ) ) {
            $classNames = $matches[1];
        }

        if ( preg_match( '/align="([^"]+)"/', $attributesParsed, $matches ) ) {
            $classNames .= ' align' . $matches[1];
        }

        return $classNames;
    }

    /**
     * Retrieve the block style
     */
    public function blockStyle(): ?string {
        $attributesParsed = $this->getParsedAttributes();
        $style            = "";

        if ( preg_match( '/style="([^"]+)"/', $attributesParsed, $matches ) ) {
            $style = $matches[1];
            // Filter out "Array " that can appear sometimes
            $style = str_replace( 'Array', '', $style );
        }

        return ! empty( $style ) ? $style : null;
    }

    public function blockGap(): ?string {
        $attributes = $this->view->getData()['attributes'] ?? [];
        $styles     = $attributes['style'] ?? null;

        if ( ! $styles ) {
            return null;
        }

        $json = json_encode( $styles );

        $json = preg_replace(
            '/var:preset\|([a-zA-Z0-9_-]+)\|([a-zA-Z0-9_-]+)/',
            'var(--wp--preset--$1--$2)',
            $json
        );

        $parsedArray = json_decode( $json, true );

        return $parsedArray['spacing']['blockGap'] ?? null;
    }
}
