import {createHigherOrderComponent} from '@wordpress/compose';
import {InspectorControls} from '@wordpress/block-editor';
import {PanelBody, SelectControl, ToggleControl} from '@wordpress/components';
import {Fragment} from '@wordpress/element';
import {addFilter} from '@wordpress/hooks';

const ANIMATION_OPTIONS = [
  {label: 'Fondu', value: 'fade'},
  {label: 'Fondu vers le haut', value: 'fade-up'},
  {label: 'Fondu depuis la gauche', value: 'fade-left'},
  {label: 'Fondu depuis la droite', value: 'fade-right'},
  {label: 'Zoom', value: 'scale'},
  {label: 'Texte par caractères', value: 'text-chars'},
];

const ALLOWED_BLOCKS = [
  'core/heading',
  'core/group',
  'core/columns',
  'core/column',
  'core/image',
  'core/button',
  'core/cover',
  'core/accordion-item',
];

const addAnimationControl = createHigherOrderComponent((BlockEdit) => {
  return (props) => {
    const {attributes, setAttributes, name} = props;

    if (!ALLOWED_BLOCKS.includes(name)) return <BlockEdit {...props} />;

    const {animateOnScroll, animationType} = attributes;

    return (
      <Fragment>
        <BlockEdit {...props} />
        <InspectorControls>
          <PanelBody title="Animations">
            <ToggleControl
              label="Animer l'apparition"
              checked={!!animateOnScroll}
              onChange={(val) =>
                setAttributes({
                  animateOnScroll: val,
                  animationType: val ? animationType || 'fade-up' : undefined,
                })
              }
            />
            {animateOnScroll && (
              <SelectControl
                label="Type d'animation"
                value={animationType || 'fade-up'}
                options={ANIMATION_OPTIONS}
                onChange={(val) => setAttributes({animationType: val})}
              />
            )}
          </PanelBody>
        </InspectorControls>
      </Fragment>
    );
  };
}, 'addAnimationControl');

addFilter('editor.BlockEdit', 'heat/animation-control', addAnimationControl);

function addAnimationProps(extraProps, blockType, attributes) {
  if (!attributes.animateOnScroll) return extraProps;

  extraProps.className = `${extraProps.className || ''} is-animated`.trim();
  extraProps['data-animation'] = attributes.animationType || 'fade-up';

  return extraProps;
}

addFilter('blocks.getSaveContent.extraProps', 'heat/add-class', addAnimationProps);

addFilter('blocks.registerBlockType', 'heat/attributes', (settings, name) => {
  if (!ALLOWED_BLOCKS.includes(name)) return settings;

  settings.attributes = {
    ...settings.attributes,
    animateOnScroll: {type: 'boolean', default: false},
    animationType: {type: 'string', default: 'fade-up'},
  };

  return settings;
});
