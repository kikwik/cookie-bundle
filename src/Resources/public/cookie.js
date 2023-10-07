document.addEventListener('click', function (event) {

    if (event.target.closest('.js-kwc-btn-accept') || event.target.closest('.js-kwc-btn-deny')) {
        event.preventDefault();
        // make a POST request to consent url
        const url = event.target.href;
        fetch(url, {
            method: 'POST'
        }).then(function (response){
            if(response.ok)
            {
                // hide the banner
                const banner = document.querySelector('.kwc-banner');
                if(banner)
                {
                    banner.style.display = 'none';
                    banner.ariaHidden = 'true';
                }

                if(event.target.matches('.js-kwc-btn-accept'))
                {
                    // reload the page
                    fetch(window.location.href)
                        .then(function (response){
                            // The call was successful!
                            return response.text();
                        }).then(function (html) {
                        // This is the HTML from our response as a text string
                        document.open('text/html');
                        document.write(html);
                        document.close();
                    });
                }
            }
        })
    }

    if (event.target.closest('.js-kwc-toggle-banner')) {
        event.preventDefault();

        // show the banner
        const banner = document.querySelector('.kwc-banner');
        if(banner)
        {
            if(banner.style.display == 'none')
            {
                banner.style.display = '';
                banner.ariaHidden = 'false';
            }
            else
            {
                banner.style.display = 'none';
                banner.ariaHidden = 'true';
            }
        }
    }


});

