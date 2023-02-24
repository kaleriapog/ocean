document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('select')
    const mediaLaptop = (window.innerWidth < 1025)

    if(selects) {
        if(!mediaLaptop) {
            jQuery(document).ready(function() {
                jQuery('select').select2();
            });
        }
    }
})