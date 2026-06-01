import {animate, splitText, stagger, svg} from 'animejs';

const ANIMATIONS = {
  'fade': {
    opacity: [0, 1],
  },
  'fade-up': {
    opacity: [0, 1],
    translateY: [24, 0],
  },
  'fade-left': {
    opacity: [0, 1],
    translateX: [-24, 0],
  },
  'fade-right': {
    opacity: [0, 1],
    translateX: [24, 0],
  },
  'scale': {
    opacity: [0, 1],
    scale: [0.95, 1],
  },
};

const animateTextChars = (els) => {
  els.forEach((el, i) => {
    const {chars} = splitText(el, {chars: true});
    animate(chars, {
      opacity: [0.3, 1],
      duration: 700,
      delay: stagger(30, {start: i * 120}),
      ease: 'out(3)',
    });
  });
};

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.addEventListener('DOMContentLoaded', () => {
  const elements = document.querySelectorAll('.is-animated');
  if (!elements.length) return;

  if (prefersReducedMotion) {
    elements.forEach(el => el.classList.add('is-visible'));
    return;
  }

  let pendingBatch = [];
  let flushTimer = null;

  const flush = () => {
    if (!pendingBatch.length) return;

    const batch = pendingBatch;
    pendingBatch = [];
    flushTimer = null;

    const groups = batch.reduce((acc, el) => {
      const type = el.dataset.animation || 'fade-up';
      (acc[type] ||= []).push(el);
      return acc;
    }, {});

    Object.entries(groups).forEach(([type, els]) => {
      if (type === 'text-chars') {
        animateTextChars(els);
        return;
      }
      const params = ANIMATIONS[type] || ANIMATIONS['fade-up'];
      animate(els, {
        ...params,
        duration: 700,
        delay: stagger(120),
        ease: 'out(3)',
      });
    });
  };

  const queue = (target, obs) => {
    pendingBatch.push(target);
    obs.unobserve(target);
    clearTimeout(flushTimer);
    flushTimer = setTimeout(flush, 50);
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      queue(entry.target, observer);
    });
  }, {
    rootMargin: '0px 0px -50px 0px',
    threshold: 0.1,
  });

  // Slider slides: clipped horizontally by overflow container.
  // Threshold 0.1 d'area échoue quand seul 1px H visible. Threshold 0 + rootMargin H étendu
  // = vertical garde comportement initial, horizontal accepte 1px.
  const sliderObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      queue(entry.target, sliderObserver);
    });
  }, {
    rootMargin: '0px 9999px -50px 9999px',
    threshold: 0.1,
  });

  elements.forEach(el => {
    if (el.classList.contains('is-animated--slide')) {
      sliderObserver.observe(el);
    } else {
      observer.observe(el);
    }
  });
});

/* Button Icon Animation */
const buttonIconShapeAnimation = () => {
  const buttons = document.querySelectorAll('.wp-block-button.is-style-with-icon, .wp-block-button.is-style-border-with-icon');
  if (buttons.length === 0) return;

  buttons.forEach((button, index) => {
    const path = button.querySelector('.wp-block-button__link svg path');
    if (!path) return;

    // Create hidden SVG holding the original shape for morphTo to reference
    const originalId = `morph-original-${index}`;
    const hiddenSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    hiddenSvg.style.display = 'none';
    const hiddenPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    hiddenPath.setAttribute('id', originalId);
    hiddenPath.setAttribute('d', path.getAttribute('d'));
    hiddenSvg.appendChild(hiddenPath);
    document.body.appendChild(hiddenSvg);

    button.addEventListener('mouseenter', () => {
      animate(path, {
        d: svg.morphTo('#morphButtonIconShape path', 3),
        duration: 300,
        ease: 'inOutCirc',
      });
    });

    button.addEventListener('mouseleave', () => {
      animate(path, {
        d: svg.morphTo(`#${originalId}`, 3),
        duration: 300,
        ease: 'inOutCirc',
      });
    });
  });
};

document.addEventListener('DOMContentLoaded', buttonIconShapeAnimation);
