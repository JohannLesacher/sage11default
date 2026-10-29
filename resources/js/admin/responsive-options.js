import { registerResponsiveOption } from './responsive-panel';

// Options par instance absentes de core. Pour masquer un bloc selon l'écran,
// utiliser la visibilité native de WordPress (barre d'outils du bloc).
// Chaque option ajoute la classe `is-responsive-{name}`, stylée dans resources/css/base/responsive.scss.
const OPTIONS = [
  {
    blocks: ['core/columns'],
    name: 'reverse-stacked',
    label: "Inverser l'ordre une fois empilées",
  },
  {
    blocks: ['core/group', 'core/heading', 'core/paragraph'],
    name: 'center-mobile',
    label: 'Centrer sur mobile',
  },
  {
    blocks: ['core/buttons'],
    name: 'full-mobile',
    label: 'Pleine largeur sur mobile',
  },
];

OPTIONS.forEach(({ blocks, ...option }) => {
  blocks.forEach((block) => registerResponsiveOption(block, option));
});
