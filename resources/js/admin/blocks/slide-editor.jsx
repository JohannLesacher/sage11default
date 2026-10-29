import { registerBlockType } from '@wordpress/blocks';
import metadata from '@blocks/slide/block.json';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import { createElement as el } from '@wordpress/element';

registerBlockType(metadata, {
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
