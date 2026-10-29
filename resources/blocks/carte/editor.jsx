import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RangeControl, TextControl } from '@wordpress/components';
import ServerPreview, { binder } from '@scripts/admin/components/server-preview';
import metadata from './block.json';

registerBlockType(metadata, {
  edit: ({ name, attributes, setAttributes }) => {
    const bind = binder(attributes, setAttributes);

    return (
      <>
        <InspectorControls>
          <PanelBody title="Lieu">
            <TextControl
              label="Nom du lieu"
              help="Non affiché : sert de titre à la carte pour les lecteurs d’écran."
              {...bind('adresse')}
            />
            <TextControl
              label="Latitude"
              type="number"
              step="any"
              help="Sur openstreetmap.org, clic droit sur le lieu > Afficher l’adresse : les coordonnées s’affichent à gauche."
              {...bind('latitude')}
            />
            <TextControl label="Longitude" type="number" step="any" {...bind('longitude')} />
            <RangeControl label="Zoom" min={10} max={18} {...bind('zoom')} />
          </PanelBody>
        </InspectorControls>

        <ServerPreview name={name} attributes={attributes} />
      </>
    );
  },
});
