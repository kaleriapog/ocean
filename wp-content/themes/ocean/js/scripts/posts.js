document.addEventListener('DOMContentLoaded', function() {
    const formPost = document.querySelector('.posts-filter')
    const postsPanel = document.querySelector('.posts-panel')
    const postsSecondList = document.querySelector('.posts-second-list')
    const mediaLaptop = (window.innerWidth < 1025)
    let controller = new ScrollMagic.Controller()


    //switching by category on the posts page
    if(formPost) {
        const inputs = formPost.querySelectorAll('input')

        inputs.forEach((el) => {
            el.addEventListener('change', () => {
                formPost.submit();
                return false;
            })
        })

    }

    //fixing categories on the page when scrolling and when we have posts-second-list
    if(postsPanel && postsSecondList && !mediaLaptop) {
        new ScrollMagic.Scene({triggerElement: '.posts__wrapper', triggerHook: '0.1' })
            .setPin( '.posts-panel__wrapper')
            // .addIndicators({name: "section-interactive"})
            .addTo(controller)
            .reverse(true);
    }
})