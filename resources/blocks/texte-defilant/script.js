const SPEEDS = {
  lent: 40,
  normal: 80,
  rapide: 160,
};

const fill = (block) => {
  const track = block.querySelector('.block-texte-defilant__track');
  const group = track?.querySelector('.block-texte-defilant__group');
  if (!group) return;

  const groupWidth = group.offsetWidth;
  if (!groupWidth) return;

  const perHalf = Math.max(1, Math.ceil(block.offsetWidth / groupWidth));
  const total = perHalf * 2;

  while (track.children.length > total) track.lastElementChild.remove();
  while (track.children.length < total) track.append(group.cloneNode(true));

  const speed = SPEEDS[block.dataset.vitesse] ?? SPEEDS.normal;
  block.style.setProperty('--marquee-duration', `${(groupWidth * perHalf) / speed}s`);
};

const blocks = document.querySelectorAll('.block-texte-defilant');

if (blocks.length) {
  const observer = new ResizeObserver((entries) => {
    entries.forEach(({ target }) => fill(target));
  });

  blocks.forEach((block) => observer.observe(block));
  document.fonts?.ready.then(() => blocks.forEach(fill));
}
