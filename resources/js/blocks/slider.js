import Splide from '@splidejs/splide';

document.querySelectorAll('.block-slider').forEach((el) => {
  new Splide(el).mount();
});
