import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType('sur-mesure/slide', {
  apiVersion: 3,
  title: 'Slide',
  description: 'Une diapositive du slider, contenu libre.',
  category: 'sur-mesure',
  icon: 'media-interactive',
  parent: ['sur-mesure/slider'],
  supports: {
    color: { background: true, text: true },
    spacing: { padding: true },
  },

  edit: () => {
    const blockProps = useBlockProps({ className: 'editor-slide' });

    return el(
      'div',
      blockProps,
      el(InnerBlocks, {
        template: [['core/paragraph', { placeholder: 'Contenu de la slide…' }]],
      }),
    );
  },

  save: () => el(InnerBlocks.Content, null),
});
