document.addEventListener('DOMContentLoaded', function() {
    let controller = new ScrollMagic.Controller()
    const sliderAchievements = document.querySelector('.swiper-achievements')
    
    if (sliderAchievements) {
        let sliderAchievementsSwiper = new Swiper('.swiper-achievements', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            spaceBetween: 100,
            allowTouchMove: false,
            speed: 10000,
            autoplay: {
                delay: 1,
            },
        });

        sliderAchievementsSwiper.autoplay.stop()

        new ScrollMagic.Scene({triggerElement: '.section-slider', duration: '100%', triggerHook: 'onLeave'})
            // .addIndicators({name: 'section-slider'})
            .addTo(controller)
            .on('start end', (e) => {
                if (e.state === 'DURING' && e.type === 'start') {
                    sliderAchievementsSwiper.autoplay.start()
                }

                if (e.state === 'BEFORE' && e.type === 'start') {
                    sliderAchievementsSwiper.autoplay.stop()
                }

                if (e.state === 'AFTER' && e.type === 'end') {
                    sliderAchievementsSwiper.autoplay.stop()
                }

                if (e.state === 'DURING' && e.type === 'end') {
                    sliderAchievementsSwiper.autoplay.start()
                }
            })
            .reverse(true);
    }
})
