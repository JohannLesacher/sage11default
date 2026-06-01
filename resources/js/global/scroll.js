const listeners = new Set();
let raf;
let lastY = -1;

const tick = () => {
  const y = window.scrollY;
  if (y === lastY) return;
  lastY = y;
  listeners.forEach((fn) => fn(y));
};

window.addEventListener(
  'scroll',
  () => {
    cancelAnimationFrame(raf);
    raf = requestAnimationFrame(tick);
  },
  { passive: true },
);

export const onScroll = (fn) => {
  fn(window.scrollY);
  listeners.add(fn);
  return () => listeners.delete(fn);
};
