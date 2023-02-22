document.addEventListener('DOMContentLoaded', function() {
    let controller = new ScrollMagic.Controller()

    let openMenu = document.querySelector('.header .icon-menu')
    let header = document.querySelector('.header')
    let closeMenu = document.querySelector('.header .icon-menu-close')
    let headerNavigation = document.querySelector('.header .header__navigation')
    let buttonSubmit = document.querySelector('.button-submit input[type="submit"]')
    let body = document.querySelector('body')
    let sliderAchievements = document.querySelector('.swiper-achievements')
    let verticalSliders = document.querySelector('.section-vertical-sliders__sliders')
    let showMore = document.querySelector('.show-more')
    let teamSlider = document.querySelector('.team-slider')
    let sectionHeroAnimate = document.querySelector('.section-hero-animate')
    let loadMoreEvents = document.querySelector('.section-full-events .load-more')
    let heroAnimateImage = document.querySelector('.section-hero-animate-image__image')
    let buttonsVideo = document.querySelectorAll('.button-video')
    let sliderHorizontalItems = document.querySelector('.section-slider-horizontal__items')
    let sharePostCopy = document.querySelector('.share-post-copy')
    let formPost = document.querySelector('.posts-filter')
    let postsPanel = document.querySelector('.posts-panel')
    let postsSecondList = document.querySelector('.posts-second-list')
    let sectionsWithPopUp = document.querySelectorAll('.section-with-pop-up')
    let selects = document.querySelectorAll('select')
    let triggerOpenFormSimple = document.querySelector('.trigger-open-form-simple')
    let sectionHeroDonateImage = document.querySelector('.section-hero-donate__image')
    let swiperBubbleArr = document.querySelectorAll('.swiper-bubble')

    // media
    let mediaMobile = (window.innerWidth < 768)
    let mediaLaptop = (window.innerWidth < 1025)
    let mediaHeight600 = (window.innerHeight < 600)

    if(openMenu) {
        openMenu.addEventListener('click', () => {
            headerNavigation.style.right = '0'
            headerNavigation.style.left = 'auto'

            setTimeout(() => {
                headerNavigation.classList.add('open')
                body.classList.add('open-menu')
            }, 10)
        })

        if(closeMenu) {
            closeMenu.addEventListener('click', () => {
                headerNavigation.classList.remove('open')
                body.classList.remove('open-menu')

                setTimeout(() => {
                    headerNavigation.style.right = '-100%'
                    headerNavigation.style.left = '100%'
                }, 800)
            })
        }
    }

    if(buttonSubmit) {
        let div = document.createElement('div');
        div.className = 'fake-button-submit';
        div.innerHTML = '<svg width="29" height="24" viewBox="0 0 29 24" fill="none" xmlns="http://www.w3.org/2000/svg">\n' +
            '<path d="M2 10.5C1.17157 10.5 0.5 11.1716 0.5 12C0.5 12.8284 1.17157 13.5 2 13.5L2 10.5ZM28.0607 13.0607C28.6464 12.4749 28.6464 11.5251 28.0607 10.9393L18.5147 1.3934C17.9289 0.807611 16.9792 0.807611 16.3934 1.3934C15.8076 1.97919 15.8076 2.92893 16.3934 3.51472L24.8787 12L16.3934 20.4853C15.8076 21.0711 15.8076 22.0208 16.3934 22.6066C16.9792 23.1924 17.9289 23.1924 18.5147 22.6066L28.0607 13.0607ZM2 13.5L27 13.5V10.5L2 10.5L2 13.5Z" fill="white"/>\n' +
            '</svg>';
        buttonSubmit.parentNode.prepend(div)
        buttonSubmit.parentNode.style.position = 'relative'
    }

    if(sliderAchievements) {
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

        new ScrollMagic.Scene({triggerElement: '.section-slider', duration: '100%', triggerHook: 'onLeave' })
            // .addIndicators({name: 'section-slider'})
            .addTo(controller)
            .on('start end', (e) => {
                if(e.state === 'DURING' && e.type === 'start') {
                    sliderAchievementsSwiper.autoplay.start()
                }

                if(e.state === 'BEFORE' && e.type === 'start') {
                    sliderAchievementsSwiper.autoplay.stop()
                }

                if(e.state === 'AFTER' && e.type === 'end') {
                    sliderAchievementsSwiper.autoplay.stop()
                }

                if(e.state === 'DURING' && e.type === 'end') {
                    sliderAchievementsSwiper.autoplay.start()
                }
            })
            .reverse(true);
    }

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

    if(showMore) {
        let text = showMore.parentNode.querySelector('.content-for-show-more')
        let showMoreName = showMore.querySelector('.show-more__name')

        showMore.addEventListener('click', () => {
            showMoreName.classList.toggle('open')
            text.classList.toggle('open')
        })
    }

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
        }, 500)
    }

    if(sectionHeroAnimate) {
        let headerHeight = header.offsetHeight
        let getprop = window.getComputedStyle(sectionHeroAnimate, null).getPropertyValue('padding-top').slice(0, -2) * 1;

        header.classList.add('header-transparent')
        sectionHeroAnimate.style.paddingTop = `${getprop + headerHeight}px`

        let headerTransparent = document.querySelector('.header-transparent')

        body.style.paddingTop = '0'

        //for fixed header
        if(headerTransparent) {
            window.addEventListener('scroll', () => {
                let scrollTop = window.pageYOffset ? window.pageYOffset : (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);

                if (scrollTop >= 50) {
                    if (header.classList.contains('header-transparent')) {
                        header.classList.remove('header-transparent')
                    }
                } else {
                    header.classList.add('header-transparent')
                }
            })
        }
    }

    if(loadMoreEvents) {
        loadMoreEvents.addEventListener('click', (elem) => {
            elem.target.closest('.section-full-events__workshops').classList.add('open')
        })
    }

    if(heroAnimateImage && !mediaHeight600) {
        let timelineSectionInteractive = new TimelineMax();

        timelineSectionInteractive
            .fromTo(['.section-hero-animate-image__image-inner'], {clipPath: mediaMobile ? 'circle(46% at 50% 50%)' : mediaLaptop ? 'circle(34% at 50% 50%)' : 'circle(25.1% at 50% 50%)'}, {clipPath: mediaMobile ? 'circle(74% at 50% 50%)' : 'circle(61% at 50% 50%)', ease: Circ.easeNone})

        let ScrollMagicInteractive = new ScrollMagic.Scene({triggerElement: mediaMobile ? '.section-hero-animate-image__image' : mediaLaptop ? '.section-hero-animate-image' : '.section-hero-animate-image__image', duration: '100%', triggerHook: 'onLeave' })
            .setPin( mediaMobile ? '.section-hero-animate-image__image' : mediaLaptop ? '.section-hero-animate-image' : '.section-hero-animate-image__image')
            .setTween(timelineSectionInteractive)
            // .addIndicators({name: "section-interactive"})
            .addTo(controller)
            .reverse(true);
    }

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

    if(sliderHorizontalItems) {
        let sectionSliderHorizontal = new Swiper('.section-slider-horizontal__items', {
            direction: 'horizontal',
            slidesPerView: 'auto',
            observer: true,
            loopedSlides: 5,
            spaceBetween: 40,
            loop: true,
            observeParents: true,
            resizeObserver: true,
            observeSlideChildren: true,
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
            }
        });

        sectionSliderHorizontal.on('slideChange', function () {
            let button = document.querySelector('.section-slider-horizontal__items .button-slider')

            button.style.display = 'none'
        });
    }

    if(sharePostCopy) {
        let urlCurrentPost = location.href

        sharePostCopy.addEventListener('click', (e) => {
            e.preventDefault();
            navigator.clipboard.writeText(urlCurrentPost).then(function() {
                document.querySelector('.copied').style.opacity = 1;

                window.setTimeout(() => {
                    document.querySelector('.copied').style.opacity = 0;
                }, 2000);
            }, function(err) {
            });
        })
    }

    if(formPost) {
        const inputs = formPost.querySelectorAll('input')

        inputs.forEach((el) => {
            el.addEventListener('change', () => {
                formPost.submit();
                return false;
            })
        })

    }

    if(postsPanel && postsSecondList && !mediaLaptop) {
        new ScrollMagic.Scene({triggerElement: '.posts__wrapper', triggerHook: '0.1' })
            .setPin( '.posts-panel__wrapper')
            // .addIndicators({name: "section-interactive"})
            .addTo(controller)
            .reverse(true);
    }

    if(sectionsWithPopUp) {
        sectionsWithPopUp.forEach((sectionWithPopUp) => {
            let buttonsPopUp = sectionWithPopUp.querySelectorAll('.button-link-pop-up')
            let buttonsClosePopUp = document.querySelectorAll('.pop-up-close')

            buttonsPopUp.forEach((buttonPopUp) => {
                buttonPopUp.addEventListener('click', (elem) => {
                    document.querySelector('.section-contact-pop-up').classList.add('open')
                    body.classList.add('open-menu')
                })
            })

            buttonsClosePopUp.forEach((buttonClosePopUp) => {
                buttonClosePopUp.addEventListener('click', (elem) => {
                    elem.target.closest('.section-contact-pop-up').classList.remove('open')
                    body.classList.remove('open-menu')
                })
            })
        })
    }

    if(selects) {
        if(!mediaLaptop) {
            jQuery(document).ready(function() {
                jQuery('select').select2();
            });
        }
    }

    if(triggerOpenFormSimple) {
        triggerOpenFormSimple.addEventListener('click', () => {
            let content = document.querySelector('.section-form-simple__content')
            let button = document.querySelector('.section-form-simple__button')

            content.classList.add('open')

            setTimeout(() => {
                button.style.display = 'none'
            }, 800)
        })
    }

    if(sectionHeroDonateImage && !mediaLaptop) {
        body.style.paddingTop = '0'
        let timelineSectionDonateImage = new TimelineMax();

        timelineSectionDonateImage
            .fromTo(['.section-hero-donate__image-inner'], {}, {maxWidth: '100%', ease: Circ.easeNone})
            .fromTo(['.section-hero-donate__image-mask'], {}, {transform: 'translateX(-50%) scale(5.45)', top: '49%', ease: Circ.easeNone}, '<')
            .fromTo(['.section-hero-donate__title'], {}, {top: '37%', ease: Circ.easeNone}, '<')
            .fromTo(['.section-hero-donate'], {}, {paddingBottom: '0', ease: Circ.easeNone}, '<')

       new ScrollMagic.Scene({triggerElement: '.section-hero-donate' , duration: '100%', triggerHook: '0', pinSpacing: false })
            .setPin( '.section-hero-donate')
            .setTween(timelineSectionDonateImage)
            // .addIndicators({name: "section-interactive"})
            .addTo(controller)
            .reverse(true);
    }

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

