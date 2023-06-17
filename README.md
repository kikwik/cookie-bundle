KikwikCookieBundle
==================

Cookie banner for Symfony 5+


Installation
------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require kikwik/cookie-bundle
```

Configuration
-------------

Create the `config/packages/kikwik_cookie.yaml` config file and clear the cache

```yaml
kikwik_cookie:
    cookie_name:        'kwc_consent'
    cookie_lifetime:    60              # number days after cookie expiration
    privacy_route:      'app_privacy'   # default is null
```
