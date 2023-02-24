document.addEventListener('DOMContentLoaded', function() {
    const sliderHorizontalItems = document.querySelector('.section-slider-horizontal__items')

    //for slider horizontal
    if(sliderHorizontalItems) {
        let sectionSliderHorizontal = new Swiper('.section-slider-horizontal__items', {
            direction: 'horizontal',
            loop: true,
            slidesPerView: 'auto',
            loopedSlides: 2,
            spaceBetween: 40,
            slidesOffsetBefore: -120,
            breakpoints: {
                320: {
                    spaceBetween: 20,
                    slidesOffsetBefore: -80,
                },
                768: {
                    spaceBetween: 40,
                    slidesOffsetBefore: -120,
                },
            },

        });

        sectionSliderHorizontal.on('slideChange', function () {
            let button = document.querySelector('.section-slider-horizontal__items .button-slider')

            button.style.display = 'none'
        });
    }
})