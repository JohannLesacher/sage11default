import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';
import ServerPreview, { binder } from '@scripts/admin/components/server-preview';
import metadata from './block.json';

const VITESSES = [
  { label: 'Lent', value: 'lent' },
  { label: 'Normal', value: 'normal' },
  { label: 'Rapide', value: 'rapide' },
];

const DIRECTIONS = [
  { label: 'Gauche', value: 'gauche' },
  { label: 'Droite', value: 'droite' },
];

const TAGS = ['div', 'p', 'h1', 'h2', 'h3', 'span'].map((tag) => ({ label: tag, value: tag }));

registerBlockType(metadata, {
  edit: ({ name, attributes, setAttributes }) => {
    const bind = binder(attributes, setAttributes);

    return (
      <>
        <InspectorControls>
          <PanelBody title="Texte">
            <TextControl label="Texte" placeholder="Texte défilant…" {...bind('texte')} />
            <TextControl
              label="Séparateur"
              placeholder="•"
              help="Affiché entre chaque répétition du texte."
              {...bind('separateur')}
            />
            <SelectControl label="Élément HTML" options={TAGS} {...bind('tag')} />
          </PanelBody>

          <PanelBody title="Défilement">
            <SelectControl label="Vitesse" options={VITESSES} {...bind('vitesse')} />
            <SelectControl label="Direction" options={DIRECTIONS} {...bind('direction')} />
            <ToggleControl
              label="Pause au survol"
              checked={attributes.pause}
              onChange={(value) => setAttributes({ pause: value })}
            />
          </PanelBody>
        </InspectorControls>

        <ServerPreview name={name} attributes={attributes} />
      </>
    );
  },
});
