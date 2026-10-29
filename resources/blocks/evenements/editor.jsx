import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, Spinner, ToggleControl } from '@wordpress/components';
import { store as coreStore } from '@wordpress/core-data';
import { useSelect } from '@wordpress/data';
import { dateI18n, getSettings } from '@wordpress/date';
import { decodeEntities } from '@wordpress/html-entities';
import metadata from './block.json';

/**
 * Bloc de données : aperçu en JSX à partir de l'API REST (instantané, sans aller-retour de rendu),
 * rendu front par la vue Blade + composer Evenements (à garder en phase).
 */
function Apercu({ evenements, afficherImage }) {
  if (!evenements) {
    return <Spinner />;
  }

  if (!evenements.length) {
    return <p>Aucun événement publié.</p>;
  }

  return (
    <ul className="block-evenements__liste">
      {evenements.map((evenement) => {
        const image = evenement._embedded?.['wp:featuredmedia']?.[0];

        return (
          <li key={evenement.id} className="block-evenements__item">
            {afficherImage && image && (
              <img
                className="block-evenements__image"
                src={image.media_details?.sizes?.medium_large?.source_url ?? image.source_url}
                alt={image.alt_text}
              />
            )}
            <h3 className="block-evenements__titre">
              <a href={evenement.link} onClick={(event) => event.preventDefault()}>
                {decodeEntities(evenement.title.rendered)}
              </a>
            </h3>
            <time className="block-evenements__date" dateTime={evenement.date}>
              {dateI18n(getSettings().formats.date, evenement.date)}
            </time>
          </li>
        );
      })}
    </ul>
  );
}

registerBlockType(metadata, {
  edit: ({ attributes, setAttributes }) => {
    const { nombre, afficherImage } = attributes;

    const evenements = useSelect(
      (select) =>
        select(coreStore).getEntityRecords('postType', 'evenement', {
          per_page: nombre,
          _embed: 'wp:featuredmedia',
        }),
      [nombre],
    );

    return (
      <>
        <InspectorControls>
          <PanelBody title="Réglages">
            <RangeControl
              label="Nombre d’événements"
              value={nombre}
              onChange={(value) => setAttributes({ nombre: value })}
              min={1}
              max={12}
            />
            <ToggleControl
              label="Afficher l’image"
              checked={afficherImage}
              onChange={(value) => setAttributes({ afficherImage: value })}
            />
          </PanelBody>
        </InspectorControls>

        <section {...useBlockProps({ className: 'block-evenements' })}>
          <Apercu evenements={evenements} afficherImage={afficherImage} />
        </section>
      </>
    );
  },
});
