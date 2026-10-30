import { initHeader } from './components/header.js';

import.meta.glob(['../images/**', '../fonts/**']);

// Modules chargés à la demande : seulement si la page contient leur sélecteur
const modules = {
  '.is-animated': () => import('./global/animation.js'),
  '.sync-element-heights': () => import('./global/sync-heights.js'),
  '[js-horizontal-scroll]': () => import('./global/horizontal-scroll.js'),
};

document.addEventListener('DOMContentLoaded', () => {
  initHeader();

  for (const [selector, load] of Object.entries(modules)) {
    if (document.querySelector(selector)) load().then(({ init }) => init());
  }
});
