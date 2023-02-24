document.addEventListener('DOMContentLoaded', function() {
    const teamSlider = document.querySelector('.team-slider')
    const mediaMobile = (window.innerWidth < 768)

    //slider with team
    if(teamSlider) {
        setTimeout(() => {
            let teamSliderLeft = new Swiper('.team-slider-left', {
                direction: 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 40,
                loop: !mediaMobile,
                speed: 7000,
                autoplay: {
                    delay: 1,
                },
            });

            let teamSliderRight = new Swiper('.team-slider-right', {
                direction: 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 40,
                speed: 7000,
                loop: !mediaMobile,
                autoplay: {
                    delay: 1,
                    reverseDirection: true,
                },

            });

            if(mediaMobile) {
                teamSliderLeft.disable()
                teamSliderRight.disable()
            }
        }, 50)
    }
})