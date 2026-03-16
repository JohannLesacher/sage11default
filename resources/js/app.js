import {initHeader} from "./components/header.js";

import.meta.glob([
  '../images/**',
  '../fonts/**',
]);

document.addEventListener('DOMContentLoaded', () => {
  initHeader();
})
