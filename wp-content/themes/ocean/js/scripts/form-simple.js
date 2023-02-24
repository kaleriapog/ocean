document.addEventListener('DOMContentLoaded', function() {
    const triggerOpenFormSimple = document.querySelector('.trigger-open-form-simple')

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
})