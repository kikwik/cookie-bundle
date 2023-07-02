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
    consent_version:    '1.0'           # consent version (change to invalidate old consents)
    privacy_policy:     'app_privacy'   # privacy policy route or URL (default is null)
    cookie_policy:      'app_cookie'    # cookie policy route or URL (default is null)
    categories:         [ ]             # example: [ 'functional', 'analytics', 'profiling', 'marketing' ]
    banner_classes:
        wrapper: 'position-fixed bottom-0 start-0 end-0 p-1 border-top border-3 bg-white'
        actionWrapper: 'float-md-end text-center'
        btnAccept: 'btn btn-sm btn-success my-1'
        btnDeny: 'btn btn-sm btn-danger my-1'
        btnChoose: 'btn btn-sm btn-warning my-1'
        btnPrivacy: 'm-1'
        btnCookie: 'm-1'    
    
```

Import routes bundle in `config/routes/kikwik_cookie.yaml`:

```yaml
kikwik_cookie_bundle:
    resource: '@KikwikCookieBundle/Resources/config/routes.xml'
    prefix: '/'
```

Copy translations file from `vendor/kikwik/cookie-bundle/src/Resources/translations/KikwikCookieBundle.xx.yaml` into `translations` directory to change banner text

Make a twig global variable in `config/packages/twig.yaml` to use ConsentManager in templates

```yaml
twig:
    globals:
        cookieConsentManager:   '@Kikwik\CookieBundle\Service\ConsentManager'
```

```twig
    {% if cookieConsentManager.categoryAllowed('analytics') %}
        <script>
            ...
        </script>
    {% endif %}
``` 