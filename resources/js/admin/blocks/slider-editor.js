import { registerBlockType } from '@wordpress/blocks';
import {
  InnerBlocks,
  InspectorControls,
  useBlockProps,
} from '@wordpress/block-editor';
import { createElement as el, Fragment } from '@wordpress/element';
import {
  PanelBody,
  RangeControl,
  TextControl,
  ToggleControl,
} from '@wordpress/components';

registerBlockType('sur-mesure/slider', {
  apiVersion: 3,
  title: 'Slider',
  description: 'Carrousel de slides au contenu libre.',
  category: 'sur-mesure',
  icon: 'slides',
  attributes: {
    perPage: { type: 'number', default: 3 },
    perPageTablet: { type: 'number' },
    perPageMobile: { type: 'number' },
    loop: { type: 'boolean', default: false },
    arrows: { type: 'boolean', default: true },
    pagination: { type: 'boolean', default: false },
    autoplay: { type: 'boolean', default: false },
    interval: { type: 'number', default: 5 },
    label: { type: 'string', default: '' },
  },
  supports: {
    align: ['wide', 'full'],
    spacing: { blockGap: true, margin: ['top', 'bottom'] },
  },

  edit: ({ attributes, setAttributes }) => {
    const blockProps = useBlockProps({ className: 'editor-slider' });

    return el(
      Fragment,
      {},
      el(
        InspectorControls,
        {},
        el(
          PanelBody,
          { title: 'Disposition', initialOpen: true },
          el(RangeControl, {
            label: 'Slides par page (desktop)',
            value: attributes.perPage,
            onChange: (val) => setAttributes({ perPage: val }),
            min: 1,
            max: 6,
          }),
          el(RangeControl, {
            label: 'Slides par page (tablette ≤ 1024px)',
            value:
              attributes.perPageTablet ?? Math.max(1, attributes.perPage - 1),
            onChange: (val) => setAttributes({ perPageTablet: val }),
            min: 1,
            max: 6,
          }),
          el(RangeControl, {
            label: 'Slides par page (mobile ≤ 640px)',
            value: attributes.perPageMobile ?? 1,
            onChange: (val) => setAttributes({ perPageMobile: val }),
            min: 1,
            max: 6,
          }),
        ),
        el(
          PanelBody,
          { title: 'Navigation', initialOpen: true },
          el(ToggleControl, {
            label: 'Flèches',
            checked: attributes.arrows,
            onChange: (val) => setAttributes({ arrows: val }),
          }),
          el(ToggleControl, {
            label: 'Pagination (points)',
            checked: attributes.pagination,
            onChange: (val) => setAttributes({ pagination: val }),
          }),
          el(ToggleControl, {
            label: 'Boucle infinie',
            checked: attributes.loop,
            onChange: (val) => setAttributes({ loop: val }),
          }),
          el(ToggleControl, {
            label: 'Lecture automatique',
            checked: attributes.autoplay,
            onChange: (val) => setAttributes({ autoplay: val }),
          }),
          attributes.autoplay &&
            el(RangeControl, {
              label: 'Intervalle (secondes)',
              value: attributes.interval,
              onChange: (val) => setAttributes({ interval: val }),
              min: 1,
              max: 20,
            }),
        ),
        el(
          PanelBody,
          { title: 'Accessibilité', initialOpen: false },
          el(TextControl, {
            label: 'Libellé du carrousel',
            help: 'Annoncé par les lecteurs d’écran. Par défaut : « Carrousel ».',
            value: attributes.label,
            onChange: (val) => setAttributes({ label: val }),
          }),
        ),
      ),
      el(
        'div',
        blockProps,
        el(InnerBlocks, {
          allowedBlocks: ['sur-mesure/slide'],
          template: [
            ['sur-mesure/slide'],
            ['sur-mesure/slide'],
            ['sur-mesure/slide'],
          ],
        }),
      ),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});
