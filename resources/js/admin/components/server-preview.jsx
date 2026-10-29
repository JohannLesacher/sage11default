import { useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Aperçu du bloc rendu par sa vue Blade (source unique du HTML).
 * Les supports (couleurs, espacements, align…) sont appliqués par le wrapper de l'éditeur,
 * d'où skipBlockSupportAttributes pour ne pas les doubler dans l'aperçu.
 */
export default function ServerPreview({ name, attributes }) {
  return (
    <div {...useBlockProps()}>
      <ServerSideRender block={name} attributes={attributes} skipBlockSupportAttributes />
    </div>
  );
}

/**
 * Raccourci pour lier un contrôle à un attribut : <TextControl {...bind('titre')} />
 */
export const binder = (attributes, setAttributes) => (key) => ({
  value: attributes[key],
  onChange: (value) => setAttributes({ [key]: value }),
});
