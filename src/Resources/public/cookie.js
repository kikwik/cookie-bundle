document.addEventListener('click', function (event) {

    if (event.target.matches('.js-kwc-accept')) {
        event.preventDefault();
    }

    if (event.target.matches('.js-kwc-deny')) {
        event.preventDefault();
    }

    if (event.target.matches('.js-kwc-choose')) {
        event.preventDefault();
    }

}, false);