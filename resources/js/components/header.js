import { onScroll } from '../global/scroll.js';

export const initHeader = () => {
  const header = document.querySelector('.banner');
  const burger = document.querySelector('.burger');
  if (!header) return;

  const CONFIG = {
    smart: false,
    threshold: 100,
  };

  let lastScroll = 0;

  const update = (currentScroll) => {
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

  onScroll(update);

  const setMenuOpen = (open) => {
    header.classList.toggle('is-menu-open', open);
    burger?.setAttribute('aria-expanded', String(open));

    if (open) {
      const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
      document.body.style.paddingRight = `${scrollbarWidth}px`;
      document.body.classList.add('overflow-hidden');
      document.body.addEventListener('touchmove', preventDefault, { passive: false });
    } else {
      document.body.style.paddingRight = '';
      document.body.classList.remove('overflow-hidden');
      document.body.removeEventListener('touchmove', preventDefault);
    }
  };

  const isMenuOpen = () => header.classList.contains('is-menu-open');

  burger?.addEventListener('click', () => setMenuOpen(!isMenuOpen()));

  // Échap ferme le menu et rend le focus au burger
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && isMenuOpen()) {
      setMenuOpen(false);
      burger?.focus();
    }
  });

  // Un clic sur un lien (ancre comprise) ferme le menu mobile
  header.querySelector('.banner__nav')?.addEventListener('click', (e) => {
    if (e.target.closest('a') && isMenuOpen()) setMenuOpen(false);
  });

  function preventDefault(e) {
    if (!e.target.closest('.banner__nav')) {
      e.preventDefault();
    }
  }
};
