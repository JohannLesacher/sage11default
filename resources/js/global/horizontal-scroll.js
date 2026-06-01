export class HorizontalScroll {
  static SELECTOR = '[js-horizontal-scroll]';
  static MEDIA_QUERY = '(min-width: 1000px)';

  constructor(element) {
    this.el = element;
    this.view = element.querySelector('[js-horizontal-scroll_view]');
    this.wrapper = element.querySelector('[js-horizontal-scroll_wrapper]');
    this.scroller = element.querySelector('[js-horizontal-scroll_scroller]');

    this.isIntersecting = false;
    this.ticking = false;
    this.lastTranslation = null;

    this.viewWidth = 0;
    this.wrapperWidth = 0;
    this.maxTranslation = 0;

    this.onScroll = this.onScroll.bind(this);
    this.onResize = this.onResize.bind(this);

    this.measure();
    this.update();
  }

  measure() {
    this.viewWidth = this.view.getBoundingClientRect().width;
    this.wrapperWidth = this.wrapper.getBoundingClientRect().width;
    this.maxTranslation = Math.max(0, this.viewWidth - this.wrapperWidth);
    this.el.style.setProperty('--scroller-height', `${this.maxTranslation}px`);
  }

  update() {
    const scrollerRect = this.scroller.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const totalScrollHeight = scrollerRect.height + viewportHeight;

    let scrollbarPercent;
    let translation;

    if (scrollerRect.bottom > totalScrollHeight) {
      translation = 0;
    } else if (scrollerRect.bottom < viewportHeight) {
      translation = -this.maxTranslation;
    } else {
      scrollbarPercent =
        ((scrollerRect.bottom - totalScrollHeight) /
          (viewportHeight - totalScrollHeight)) *
        100;
      translation = (this.maxTranslation * scrollbarPercent) / -100;
    }

    translation = Math.round(translation);

    if (translation !== this.lastTranslation) {
      this.el.style.setProperty('--transformation', `${translation}px`);
      this.lastTranslation = translation;
    }
  }

  onScroll() {
    if (this.ticking) return;
    this.ticking = true;
    requestAnimationFrame(() => {
      this.update();
      this.ticking = false;
    });
  }

  onResize() {
    this.measure();
    this.update();
  }

  attach() {
    if (this.isIntersecting) return;
    this.isIntersecting = true;
    window.addEventListener('scroll', this.onScroll, { passive: true });
    window.addEventListener('resize', this.onResize);
  }

  detach() {
    if (!this.isIntersecting) return;
    this.isIntersecting = false;
    window.removeEventListener('scroll', this.onScroll);
    window.removeEventListener('resize', this.onResize);
  }

  destroy() {
    this.detach();
  }
}

export function initHorizontalScroll() {
  const elements = document.querySelectorAll(HorizontalScroll.SELECTOR);
  if (!elements.length) return;
  if (!window.matchMedia(HorizontalScroll.MEDIA_QUERY).matches) return;

  const instances = new Map();

  const observer = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      const instance = instances.get(entry.target);
      if (!instance) continue;
      entry.isIntersecting ? instance.attach() : instance.detach();
    }
  });

  for (const el of elements) {
    const instance = new HorizontalScroll(el);
    instances.set(el, instance);
    observer.observe(el);
  }

  return () => {
    observer.disconnect();
    for (const instance of instances.values()) instance.destroy();
    instances.clear();
  };
}
