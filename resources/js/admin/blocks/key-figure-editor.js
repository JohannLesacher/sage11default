import { createHigherOrderComponent } from '@wordpress/compose';
import {
  InspectorControls,
  getColorObjectByColorValue,
  __experimentalColorGradientSettingsDropdown as ColorGradientSettingsDropdown,
  __experimentalUseMultipleOriginColorsAndGradients as useMultipleOriginColorsAndGradients,
} from '@wordpress/block-editor';
import { createElement as el, Fragment } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';

const BLOCK_NAME = 'meta-box/key-figure';

addFilter(
  'blocks.registerBlockType',
  'theme/key-figure-attributes',
  (settings, name) => {
    if (name !== BLOCK_NAME) return settings;

    settings.attributes = {
      ...settings.attributes,
      valueColor: { type: 'string' },
      customValueColor: { type: 'string' },
    };

    return settings;
  },
);

const ValueColorControl = ({ attributes, setAttributes, clientId }) => {
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

  return el(
    InspectorControls,
    { group: 'color' },
    el(ColorGradientSettingsDropdown, {
      __experimentalIsRenderedInSidebar: true,
      panelId: clientId,
      settings: [
        {
          label: 'Chiffre',
          colorValue,
          onColorChange,
          resetAllFilter: () => ({
            valueColor: undefined,
            customValueColor: undefined,
          }),
          isShownByDefault: true,
        },
      ],
      ...colorSettings,
    }),
  );
};

const withValueColorControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    if (props.name !== BLOCK_NAME) return el(BlockEdit, props);

    return el(
      Fragment,
      null,
      el(BlockEdit, props),
      props.isSelected && el(ValueColorControl, props),
    );
  };
}, 'withValueColorControl');

addFilter(
  'editor.BlockEdit',
  'theme/key-figure-value-color',
  withValueColorControl,
);
