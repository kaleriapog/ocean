document.addEventListener('DOMContentLoaded', function() {
    const sharePostCopy = document.querySelector('.share-post-copy')

    //share post copy
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
})