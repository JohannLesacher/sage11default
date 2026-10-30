import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';
import { createElement as el, Fragment } from '@wordpress/element';
import { addFilter } from '@wordpress/hooks';

const ANIMATION_OPTIONS = [
  { label: 'Fondu', value: 'fade' },
  { label: 'Fondu vers le haut', value: 'fade-up' },
  { label: 'Fondu depuis la gauche', value: 'fade-left' },
  { label: 'Fondu depuis la droite', value: 'fade-right' },
  { label: 'Zoom', value: 'scale' },
  { label: 'Texte par caractères', value: 'text-chars' },
];

// Source unique : config/theme.php (animation.blocks), injectée par app/setup.php
const ALLOWED_BLOCKS = window.theme?.animationBlocks ?? [];

const addAnimationControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    const { attributes, setAttributes, name } = props;

    if (!ALLOWED_BLOCKS.includes(name)) return el(BlockEdit, props);

    const { animateOnScroll, animationType } = attributes;

    return el(
      Fragment,
      null,
      el(BlockEdit, props),
      el(
        InspectorControls,
        null,
        el(
          PanelBody,
          { title: 'Animations' },
          el(ToggleControl, {
            label: "Animer l'apparition",
            checked: !!animateOnScroll,
            onChange: (val) =>
              setAttributes({
                animateOnScroll: val,
                animationType: val ? animationType || 'fade-up' : undefined,
              }),
          }),
          animateOnScroll &&
            el(SelectControl, {
              label: "Type d'animation",
              value: animationType || 'fade-up',
              options: ANIMATION_OPTIONS,
              onChange: (val) => setAttributes({ animationType: val }),
            }),
        ),
      ),
    );
  };
}, 'addAnimationControl');

addFilter('editor.BlockEdit', 'theme/animation-control', addAnimationControl);

function addAnimationProps(extraProps, blockType, attributes) {
  if (!attributes.animateOnScroll) return extraProps;

  extraProps.className = `${extraProps.className || ''} is-animated`.trim();
  extraProps['data-animation'] = attributes.animationType || 'fade-up';

  return extraProps;
}

addFilter('blocks.getSaveContent.extraProps', 'theme/add-class', addAnimationProps);

addFilter('blocks.registerBlockType', 'theme/attributes', (settings, name) => {
  if (!ALLOWED_BLOCKS.includes(name)) return settings;

  settings.attributes = {
    ...settings.attributes,
    animateOnScroll: { type: 'boolean', default: false },
    animationType: { type: 'string', default: 'fade-up' },
  };

  return settings;
});
