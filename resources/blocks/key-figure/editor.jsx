import { registerBlockType } from '@wordpress/blocks';
import {
  InspectorControls,
  getColorObjectByColorValue,
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl } from '@wordpress/components';
import ServerPreview, { binder } from '@scripts/admin/components/server-preview';
import metadata from './block.json';

/**
 * Couleur du chiffre, dans le panneau Couleur natif à côté de Texte / Arrière-plan.
 */
function ValueColorControl({ attributes, setAttributes, clientId }) {
  const colorSettings = useMultipleOriginColorsAndGradients();
  const palette = colorSettings.colors.flatMap((origin) => origin.colors);

  const colorValue =
    palette.find((color) => color.slug === attributes.valueColor)?.color ?? attributes.customValueColor;

  const onColorChange = (value) => {
    const colorObject = getColorObjectByColorValue(palette, value);

    setAttributes({
      valueColor: colorObject?.slug,
      customValueColor: colorObject ? undefined : value,
    });
  };

  return (
    <InspectorControls group="color">
      <ColorGradientSettingsDropdown
        __experimentalIsRenderedInSidebar
        panelId={clientId}
        settings={[
          {
            label: 'Chiffre',
            colorValue,
            onColorChange,
            resetAllFilter: () => ({ valueColor: undefined, customValueColor: undefined }),
            isShownByDefault: true,
          },
        ]}
        {...colorSettings}
      />
    </InspectorControls>
  );
}

registerBlockType(metadata, {
  edit: (props) => {
    const { name, attributes, setAttributes } = props;
    const bind = binder(attributes, setAttributes);

    return (
      <>
        <InspectorControls>
          <PanelBody title="Chiffre">
            <TextControl label="Préfixe" placeholder="+" {...bind('prefixe')} />
            <TextControl label="Valeur" placeholder="120" {...bind('valeur')} />
            <TextControl label="Unité" placeholder="k€" {...bind('unite')} />
            <TextareaControl label="Libellé" rows={2} {...bind('libelle')} />
          </PanelBody>
        </InspectorControls>

        <ValueColorControl {...props} />

        <ServerPreview name={name} attributes={attributes} />
      </>
    );
  },
});
