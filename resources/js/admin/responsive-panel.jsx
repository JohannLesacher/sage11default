import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { InspectorControls } from '@wordpress/block-editor';
import { Fragment } from '@wordpress/element';
import {
  __experimentalToolsPanel as ToolsPanel,
  __experimentalToolsPanelItem as ToolsPanelItem,
  ToggleControl,
} from '@wordpress/components';

const registry = {};

export function registerResponsiveOption(blockName, option) {
  if (!registry[blockName]) registry[blockName] = [];
  if (registry[blockName].some((o) => o.name === option.name)) return;
  registry[blockName].push(option);
}

const classOf = (name) => `is-responsive-${name}`;

const hasClass = (className, cls) =>
  new RegExp(`(^|\\s)${cls}(\\s|$)`).test(className || '');

const addClass = (className, cls) =>
  hasClass(className, cls) ? className : `${className || ''} ${cls}`.trim();

const removeClass = (className, cls) =>
  (className || '')
    .replace(new RegExp(`(^|\\s)${cls}(?=\\s|$)`, 'g'), ' ')
    .replace(/\s+/g, ' ')
    .trim();

const withResponsivePanel = createHigherOrderComponent(
  (BlockEdit) => (props) => {
    const options = registry[props.name];
    if (!options || options.length === 0) return <BlockEdit {...props} />;

    const className = props.attributes.className || '';

    const toggle = (optName) => {
      const cls = classOf(optName);
      const next = hasClass(className, cls)
        ? removeClass(className, cls)
        : addClass(className, cls);
      props.setAttributes({ className: next || undefined });
    };

    const resetAll = () => {
      let next = className;
      options.forEach((o) => {
        next = removeClass(next, classOf(o.name));
      });
      props.setAttributes({ className: next || undefined });
    };

    return (
      <Fragment>
        <BlockEdit {...props} />
        <InspectorControls group="styles">
          <ToolsPanel label="Responsive" resetAll={resetAll}>
            {options.map((opt) => (
              <ToolsPanelItem
                key={opt.name}
                label={opt.label}
                isShownByDefault
                hasValue={() => hasClass(className, classOf(opt.name))}
                onDeselect={() => {
                  const next = removeClass(className, classOf(opt.name));
                  props.setAttributes({ className: next || undefined });
                }}
                panelId={props.clientId}
              >
                <ToggleControl
                  label={opt.label}
                  checked={hasClass(className, classOf(opt.name))}
                  onChange={() => toggle(opt.name)}
                  __nextHasNoMarginBottom
                />
              </ToolsPanelItem>
            ))}
          </ToolsPanel>
        </InspectorControls>
      </Fragment>
    );
  },
  'withResponsivePanel',
);

addFilter('editor.BlockEdit', 'theme/responsive-panel', withResponsivePanel);
