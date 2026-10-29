import { registerBlockType } from '@wordpress/blocks';
import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';
import metadata from '@blocks/slide/block.json';

const TEMPLATE = [['core/paragraph', { placeholder: 'Contenu de la slide…' }]];

registerBlockType(metadata, {
  edit: () => (
    <div {...useBlockProps({ className: 'editor-slide' })}>
      <InnerBlocks template={TEMPLATE} />
    </div>
  ),

  save: () => <InnerBlocks.Content />,
});
