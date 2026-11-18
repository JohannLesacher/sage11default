const widget = document.querySelector('.cereal-one-video');
const video = widget?.querySelector('.cereal-one-video__player');

widget?.addEventListener('click', (event) => {
  const chapter = event.target.closest('.cereal-one-video__chapter');

  if (!chapter || !video) return;

  video.currentTime = Number(chapter.dataset.time);
  video.play();
  video.focus();
});
