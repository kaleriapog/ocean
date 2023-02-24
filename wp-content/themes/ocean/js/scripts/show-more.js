document.addEventListener('DOMContentLoaded', function() {
    const showMore = document.querySelector('.show-more')

    // for button to show more
    if (showMore) {
        let text = showMore.parentNode.querySelector('.content-for-show-more')
        let showMoreName = showMore.querySelector('.show-more__name')

        showMore.addEventListener('click', () => {
            showMoreName.classList.toggle('open')
            text.classList.toggle('open')
        })
    }
})