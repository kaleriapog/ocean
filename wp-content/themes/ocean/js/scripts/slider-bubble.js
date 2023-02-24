document.addEventListener('DOMContentLoaded', function() {
    const swiperBubbleArr = document.querySelectorAll('.swiper-bubble')
    const mediaMobile = (window.innerWidth < 768)

    if(swiperBubbleArr) {
        swiperBubbleArr.forEach((swiperBubble) => {

            setTimeout(() => {
                let swiperBubbles = new Swiper(swiperBubble, {
                    direction: 'horizontal',
                    slidesPerView: 'auto',
                    spaceBetween: 30,
                    loop: !mediaMobile,
                    speed: 10000,
                    autoplay: {
                        delay: 1,
                    },
                });

                if(mediaMobile) {
                    swiperBubbles.disable()
                }

            }, 500)
        })
    }
})