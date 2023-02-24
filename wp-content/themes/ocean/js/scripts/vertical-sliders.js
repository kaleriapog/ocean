document.addEventListener('DOMContentLoaded', function() {
    const verticalSliders = document.querySelector('.section-vertical-sliders__sliders')

    if(verticalSliders) {
        setTimeout(() => {
            let verticalSliderLeft = new Swiper('.vertical-slider-left', {
                direction: 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 40,
                loop: true,
                speed: 7000,
                autoplay: {
                    delay: 1,
                },
            });

            let verticalSliderRight = new Swiper('.vertical-slider-right', {
                direction: 'vertical',
                slidesPerView: 'auto',
                spaceBetween: 40,
                loop: true,
                speed: 7000,
                autoplay: {
                    delay: 1,
                    reverseDirection: true,
                },
            });
        }, 500)
    }
})

