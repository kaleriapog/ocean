document.addEventListener("DOMContentLoaded", () => {
  const buttonsFullscreen = document.querySelectorAll(".button-fullscreen");
  if (!buttonsFullscreen || !buttonsFullscreen.length) return;
  buttonsFullscreen.forEach((button) => {
    const parent = button.parentNode;
    const video = parent.querySelector("video");
    if (!video) return;

    video.addEventListener("play", () => {
      if (video.played) {
        button.classList.remove("hidden");
      }
    });

    video.addEventListener("pause", () => {
      if (video.paused) {
        button.classList.add("hidden");
      }
    });

    button.addEventListener("click", () => {
      if (video.requestFullscreen) video.requestFullscreen();
      else if (video.webkitRequestFullscreen) video.webkitRequestFullscreen();
      else if (video.mozRequestFullScreen) video.mozRequestFullScreen();
      else if (video.msRequestFullscreen) video.msRequestFullscreen();
      else if (video.webkitEnterFullscreen) video.webkitEnterFullscreen();
    });
  });
});
