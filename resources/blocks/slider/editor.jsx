import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl, ToggleControl } from '@wordpress/components';
import { breakpoints } from '@scripts/config/breakpoints';
import metadata from './block.json';

const TEMPLATE = [['sur-mesure/slide'], ['sur-mesure/slide'], ['sur-mesure/slide']];

registerBlockType(metadata, {
  edit: ({ attributes, setAttributes }) => {
    const {
      perPage,
      perPageTablet,
      perPageMobile,
      arrows,
      pagination,
      loop,
      autoplay,
      interval,
      label,
    } = attributes;

    return (
      <>
        <InspectorControls>
          <PanelBody title="Disposition">
            <RangeControl
              label="Slides par page (desktop)"
              value={perPage}
              onChange={(value) => setAttributes({ perPage: value })}
              min={1}
              max={6}
            />
            <RangeControl
              label={`Slides par page (tablette ≤ ${breakpoints.tablet}px)`}
              value={perPageTablet ?? Math.max(1, perPage - 1)}
              onChange={(value) => setAttributes({ perPageTablet: value })}
              min={1}
              max={6}
            />
            <RangeControl
              label={`Slides par page (mobile ≤ ${breakpoints.mobile}px)`}
              value={perPageMobile ?? 1}
              onChange={(value) => setAttributes({ perPageMobile: value })}
              min={1}
              max={6}
            />
          </PanelBody>

          <PanelBody title="Navigation">
            <ToggleControl
              label="Flèches"
              checked={arrows}
              onChange={(value) => setAttributes({ arrows: value })}
            />
            <ToggleControl
              label="Pagination (points)"
              checked={pagination}
              onChange={(value) => setAttributes({ pagination: value })}
            />
            <ToggleControl
              label="Boucle infinie"
              checked={loop}
              onChange={(value) => setAttributes({ loop: value })}
            />
            <ToggleControl
              label="Lecture automatique"
              checked={autoplay}
              onChange={(value) => setAttributes({ autoplay: value })}
            />
            {autoplay && (
              <RangeControl
                label="Intervalle (secondes)"
                value={interval}
                onChange={(value) => setAttributes({ interval: value })}
                min={1}
                max={20}
              />
            )}
          </PanelBody>

          <PanelBody title="Accessibilité" initialOpen={false}>
            <TextControl
              label="Libellé du carrousel"
              help="Annoncé par les lecteurs d’écran. Par défaut : « Carrousel »."
              value={label}
              onChange={(value) => setAttributes({ label: value })}
            />
          </PanelBody>
        </InspectorControls>

        <div {...useBlockProps({ className: 'editor-slider' })}>
          <InnerBlocks allowedBlocks={['sur-mesure/slide']} template={TEMPLATE} />
        </div>
      </>
    );
  },

  save: () => <InnerBlocks.Content />,
});
