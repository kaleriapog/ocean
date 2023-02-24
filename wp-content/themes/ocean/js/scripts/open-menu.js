document.addEventListener('DOMContentLoaded', function() {
    const openMenu = document.querySelector('.header .icon-menu')
    const closeMenu = document.querySelector('.header .icon-menu-close')
    const headerNavigation = document.querySelector('.header .header__navigation')
    const body = document.querySelector('body')

    // open mobile menu
    if (openMenu) {
        openMenu.addEventListener('click', () => {
            headerNavigation.style.right = '0'
            headerNavigation.style.left = 'auto'

            setTimeout(() => {
                headerNavigation.classList.add('open')
                body.classList.add('open-menu')
            }, 10)
        })

        if (closeMenu) {
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
})