document.addEventListener('DOMContentLoaded', function() {
    const loadMoreEvents = document.querySelector('.section-full-events .load-more')

    //open more events im mobile
    if (loadMoreEvents) {
        loadMoreEvents.addEventListener('click', (elem) => {
            elem.target.closest('.section-full-events__workshops').classList.add('open')
        })
    }
})