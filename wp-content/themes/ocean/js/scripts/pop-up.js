document.addEventListener('DOMContentLoaded', function() {
    const sectionsWithPopUp = document.querySelectorAll('.section-with-pop-up')
    const body = document.querySelector('body')

    if(sectionsWithPopUp) {
        sectionsWithPopUp.forEach((sectionWithPopUp) => {
            let buttonsPopUp = sectionWithPopUp.querySelectorAll('.button-link-pop-up')
            let buttonsClosePopUp = document.querySelectorAll('.pop-up-close')

            buttonsPopUp.forEach((buttonPopUp) => {
                buttonPopUp.addEventListener('click', () => {
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
})