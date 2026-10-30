import { registerBlockType } from '@wordpress/blocks';
import {
  InspectorControls,
  RichText,
  useBlockProps,
  getColorObjectByColorValue,
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import metadata from './block.json';

/**
 * Couleur du chiffre, dans le panneau Couleur natif à côté de Texte / Arrière-plan.
 */
function ValueColorControl({ attributes, setAttributes, clientId }) {
  const colorSettings = useMultipleOriginColorsAndGradients();
  const palette = colorSettings.colors.flatMap((origin) => origin.colors);

  const colorValue =
    palette.find((color) => color.slug === attributes.valueColor)?.color ??
    attributes.customValueColor;

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

/**
 * Édition directe dans le bloc : le JSX reprend le balisage de la vue Blade (à garder en phase).
 */
registerBlockType(metadata, {
  edit: (props) => {
    const { attributes, setAttributes, isSelected } = props;
    const { valeur, libelle, valueColor, customValueColor } = attributes;

    const valueStyle = {
      color: valueColor ? `var(--wp--preset--color--${valueColor})` : customValueColor,
    };

    // Préfixe et unité optionnels : visibles vides seulement quand le bloc est sélectionné
    const part = (key, className, placeholder) =>
      (isSelected || attributes[key]) && (
        <RichText
          tagName="span"
          className={className}
          placeholder={placeholder}
          allowedFormats={[]}
          disableLineBreaks
          value={attributes[key]}
          onChange={(value) => setAttributes({ [key]: value })}
        />
      );

    return (
      <>
        <ValueColorControl {...props} />

        <div {...useBlockProps({ className: 'block-key-figure' })}>
          <p className="block-key-figure__value" style={valueStyle}>
            {part('prefixe', 'block-key-figure__prefix', '+')}
            <RichText
              tagName="span"
              className="block-key-figure__number"
              placeholder="00"
              allowedFormats={[]}
              disableLineBreaks
              value={valeur}
              onChange={(value) => setAttributes({ valeur: value })}
            />
            {part('unite', 'block-key-figure__unit', 'k€')}
          </p>

          <RichText
            tagName="p"
            className="block-key-figure__label"
            placeholder="Libellé"
            allowedFormats={[]}
            value={libelle}
            onChange={(value) => setAttributes({ libelle: value })}
          />
        </div>
      </>
    );
  },
});
