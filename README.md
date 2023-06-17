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
    cookie_prefix:      'kwc_consent'
    cookie_lifetime:    180             # number days after cookie expiration (default is 6 months)
    privacy_route:      'app_privacy'   # privacy route or URL (default is null)
```

Import routes bundle in `config/routes/kikwik_cookie.yaml`:

```yaml
kikwik_cookie_bundle:
    resource: '@KikwikCookieBundle/Resources/config/routes.xml'
    prefix: '/'
```