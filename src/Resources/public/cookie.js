document.addEventListener('click', function (event) {

    if (event.target.matches('.js-kwc-btn-accept')) {
        event.preventDefault();
        const url = event.target.href;
        fetch(url, {
            method: 'POST'
        }).then(function (response){
            if(response.ok)
            {
                const banner = document.querySelector('.kwc-banner');
                if(banner)
                {
                    banner.style.display = 'none';
                }
            }
        })
    }



});

