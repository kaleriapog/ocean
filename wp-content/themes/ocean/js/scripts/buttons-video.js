document.addEventListener('DOMContentLoaded', function() {
    const buttonsVideo = document.querySelectorAll('.button-video')

    //new buttons in video player
    if(buttonsVideo) {
        buttonsVideo.forEach((buttonVideo) => {
            let video = buttonVideo.closest('.button-video').previousElementSibling

            buttonVideo.addEventListener('click', (elem) => {
                let button = elem.target.closest('.button-video')
                let video = button.previousElementSibling

                video.addEventListener('play', function() {
                    video.play();
                }, false);

                if (video.paused) {
                    const playPromise = video.play()

                    if (playPromise !== null){
                        playPromise.catch(() => { video.play()})
                    }

                    button.classList.add('play')

                    setTimeout(() => {
                        button.classList.add('opacity')
                    }, 2000)

                } else {
                    video.pause();
                    button.classList.remove('play')
                    button.classList.remove('opacity')
                }
                return false;
            })

            video.addEventListener('ended', (elem) => {
                let buttonPlay = elem.target.nextElementSibling
                buttonPlay.classList.remove('play')
                buttonPlay.classList.remove('opacity')
            })
        })
    }
})