import {initHeader} from "./components/header.js";
import { initHorizontalScroll } from './global/horizontal-scroll.js';
import './global/animation.js';
import './global/sync-heights.js';

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
  initHorizontalScroll();
})

window.addEventListener('resize', initHorizontalScroll);
