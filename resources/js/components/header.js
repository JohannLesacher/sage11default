export const initHeader = () => {
  const header = document.querySelector('.banner');
  const burger = document.querySelector('.burger');
  if (!header) return;

  const CONFIG = {
    smart: false,
    threshold: 100,
  };

  let lastScroll = 0;

  const onScroll = () => {
    const currentScroll = window.pageYOffset;
    header.classList.toggle('is-scrolled', currentScroll > 50);

    if (CONFIG.smart) {
      if (currentScroll > CONFIG.threshold) {
        if (currentScroll > lastScroll && !header.classList.contains('is-hidden')) {
          header.classList.add('is-hidden');
        } else if (currentScroll < lastScroll && header.classList.contains('is-hidden')) {
          header.classList.remove('is-hidden');
        }
      } else {
        header.classList.remove('is-hidden');
      }
      lastScroll = currentScroll;
    }
  };

  let raf;
  window.addEventListener('scroll', () => {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(onScroll);
  }, { passive: true });

  burger?.addEventListener('click', () => {
    const isOpening = !header.classList.contains('is-menu-open');

    header.classList.toggle('is-menu-open');

    if (isOpening) {
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.paddingRight = `${scrollbarWidth}px`;
      document.body.classList.add('overflow-hidden');

      document.body.addEventListener('touchmove', preventDefault, { passive: false });
    } else {
      document.body.style.paddingRight = '';
      document.body.classList.remove('overflow-hidden');
      document.body.removeEventListener('touchmove', preventDefault);
    }
  });

  function preventDefault(e) {
    if (!e.target.closest('.banner__nav')) {
      e.preventDefault();
    }
  }
};
