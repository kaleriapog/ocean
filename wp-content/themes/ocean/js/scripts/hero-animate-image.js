document.addEventListener('DOMContentLoaded', function() {
    let controller = new ScrollMagic.Controller()
    const heroAnimateImage = document.querySelector('.section-hero-animate-image__image')
    const mediaHeight600 = (window.innerHeight < 600)
    const mediaMobile = (window.innerWidth < 768)
    const mediaLaptop = (window.innerWidth < 1025)

    //animation image in hero section
    if(heroAnimateImage && !mediaHeight600) {
        let timelineSectionInteractive = new TimelineMax();

        timelineSectionInteractive
            .fromTo(['.section-hero-animate-image__image-inner'], {clipPath: mediaMobile ? 'circle(46% at 50% 50%)' : mediaLaptop ? 'circle(34% at 50% 50%)' : 'circle(25.1% at 50% 50%)'}, {clipPath: mediaMobile ? 'circle(74% at 50% 50%)' : 'circle(61% at 50% 50%)', ease: Circ.easeNone})

        new ScrollMagic.Scene({triggerElement: mediaMobile ? '.section-hero-animate-image__image' : mediaLaptop ? '.section-hero-animate-image' : '.section-hero-animate-image__image', duration: '100%', triggerHook: 'onLeave' })
            .setPin( mediaMobile ? '.section-hero-animate-image__image' : mediaLaptop ? '.section-hero-animate-image' : '.section-hero-animate-image__image')
            .setTween(timelineSectionInteractive)
            // .addIndicators({name: "section-interactive"})
            .addTo(controller)
            .reverse(true);
    }
})