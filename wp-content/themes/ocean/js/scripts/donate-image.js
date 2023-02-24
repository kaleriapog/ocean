document.addEventListener('DOMContentLoaded', function() {
    let controller = new ScrollMagic.Controller()
    const mediaLaptop = (window.innerWidth < 1025)
    const body = document.querySelector('body')
    const sectionHeroDonateImage = document.querySelector('.section-hero-donate__image')

    //animation image in section donate
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
})