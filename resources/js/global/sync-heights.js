const BREAKPOINT = 768;
const SELECTOR = '.sync-element-heights';
const EXCLUDE_CLASS = 'exclude-from-sync-element-heights';

const groups = new Map();
let rafId = null;

const NO_DESCEND_TAGS = new Set(['UL', 'OL']);

const canDescend = (members) =>
  members.every((el) => !NO_DESCEND_TAGS.has(el.tagName));

const EXCLUDED_SELECTORS = ['div.wp-block-button'];

const isExcluded = (el) =>
  el.classList.contains(EXCLUDE_CLASS) ||
  EXCLUDED_SELECTORS.some((sel) => el.matches(sel));

// Elements that already carry a min-height (from CSS or markup) before the
// script runs. We never overwrite their min-height — they keep their own.
const locked = new WeakSet();

const hasOwnMinHeight = (el) =>
  parseFloat(window.getComputedStyle(el).minHeight) > 0;

const buildLevel = (rawContainers) => {
  const containers = rawContainers.filter((c) => !isExcluded(c));
  if (containers.length === 0) return [];

  const rowCount = Math.min(...containers.map((c) => c.children.length));
  if (!Number.isFinite(rowCount) || rowCount === 0) return [];

  const rows = [];
  for (let i = 0; i < rowCount; i++) {
    const members = containers
      .map((c) => c.children[i])
      .filter((el) => !isExcluded(el));
    if (members.length === 0) continue;
    members.forEach((el) => {
      if (hasOwnMinHeight(el)) locked.add(el);
    });
    const subRows = canDescend(members) ? buildLevel(members) : [];
    rows.push({ members, subRows });
  }
  return rows;
};

const resetRows = (rows) => {
  rows.forEach((row) => {
    row.members.forEach((el) => {
      if (locked.has(el)) return;
      el.style.minHeight = '';
    });
    resetRows(row.subRows);
  });
};

const applyRows = (rows) => {
  rows.forEach((row) => {
    const max = row.members.reduce(
      (acc, el) => Math.max(acc, el.getBoundingClientRect().height),
      0,
    );
    row.members.forEach((el) => {
      if (locked.has(el)) return;
      el.style.minHeight = `${max}px`;
    });
    applyRows(row.subRows);
  });
};

const sync = (group) => {
  if (!group.alwaysSync && window.innerWidth < BREAKPOINT) {
    resetRows(group.rows);
    return;
  }

  resetRows(group.rows);
  applyRows(group.rows);
};

const syncAll = () => {
  rafId = null;
  groups.forEach(sync);
};

const schedule = () => {
  if (rafId !== null) return;
  rafId = requestAnimationFrame(syncAll);
};

const resolveSyncContainer = (el) =>
  el.classList.contains('splide') ? el.querySelector('.splide__list') : el;

const observe = (rawContainer) => {
  const container = resolveSyncContainer(rawContainer);
  if (!container) return;

  const alwaysSync = rawContainer.classList.contains('splide');

  const rows = buildLevel(Array.from(container.children));
  if (rows.length === 0) return;

  let lastWidth = container.getBoundingClientRect().width;

  const observer = new ResizeObserver((entries) => {
    const width = entries[0].contentRect.width;
    if (width === lastWidth) return;
    lastWidth = width;
    schedule();
  });

  observer.observe(container);

  const group = { rows, observer, alwaysSync };
  groups.set(container, group);
  sync(group);
};

const destroy = (container) => {
  const group = groups.get(container);
  if (!group) return;
  group.observer.disconnect();
  resetRows(group.rows);
  groups.delete(container);
};

const init = () => {
  document.querySelectorAll(SELECTOR).forEach(observe);
};

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', init);
} else {
  init();
}

document.addEventListener('tabChanged', schedule);

export { observe, destroy };
