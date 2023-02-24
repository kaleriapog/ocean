document.addEventListener('DOMContentLoaded', function() {
    const body = document.querySelector('body')
    const header = document.querySelector('.header')
    const sectionHeroAnimate = document.querySelector('.section-hero-animate')

    //transparent header if we have section-hero-animate
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
})