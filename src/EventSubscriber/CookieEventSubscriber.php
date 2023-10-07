<?php

namespace Kikwik\CookieBundle\EventSubscriber;

use Kikwik\CookieBundle\Service\ConsentManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class CookieEventSubscriber implements EventSubscriberInterface
{


    /**
     * @var ConsentManager
     */
    private $consentManager;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var string|null
     */
    private $privacyPolicy;
    /**
     * @var string|null
     */
    private $cookiePolicy;
    /**
     * @var array
     */
    private $bannerClasses;
    /**
     * @var array
     */
    private $categories;

    public function __construct(ConsentManager $consentManager, Environment $twig, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, ?string $privacyPolicy, ?string $cookiePolicy, array $bannerClasses)
    {
        $this->consentManager = $consentManager;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->privacyPolicy = $privacyPolicy;
        $this->cookiePolicy = $cookiePolicy;
        $this->bannerClasses = $bannerClasses;
        $this->categories = $consentManager->getAvailableCategories();
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelResponse(ResponseEvent $responseEvent)
    {
        if($responseEvent->isMainRequest())
        {
            $bannerDisplayStyle = $this->consentManager->getUserHasChoosen()
                ? ' style="display: none;" aria-hidden="true"'
                : ' aria-hidden="false"';

            // inject the consent banner
            $cookieBanner = sprintf('
                            <a href="#" class="js-kwc-toggle-banner" aria-label="%s">%s</a>
                            <div class="kwc-banner" role="dialog" aria-modal="false" aria-label="Cookie Banner" %s>%s</div>
                            <script src="/bundles/kikwikcookie/cookie.js?v=%s"></script>
                            <link type="text/css" rel="stylesheet" href="/bundles/kikwikcookie/cookie.css?v=%s">',
                $this->translator->trans('banner.toggler.text',[],'KikwikCookieBundle'),
                $this->translator->trans('banner.toggler.icon',[],'KikwikCookieBundle'),
                $bannerDisplayStyle,
                $this->twig->render('@KikwikCookie/_cookieBanner.html.twig',[
                    'privacy_url' => $this->generateUrl($this->privacyPolicy),
                    'cookie_url' => $this->generateUrl($this->cookiePolicy),
                    'bannerClasses' => $this->bannerClasses,
                    'categories' => $this->categories,
                ]),
                filemtime(__DIR__.'/../Resources/public/cookie.js'),
                filemtime(__DIR__.'/../Resources/public/cookie.css')
            );

            $response = $responseEvent->getResponse();

            $content = $response->getContent();
            if(strpos($content, '</body>') !== false)
            {
                // new code: inject cookie banner just after the opening <body> tag
                $matches = preg_split('/(<body.*?>)/i', $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $injectedHTML = $matches[0] . $matches[1] . $cookieBanner . $matches[2];
                $response->setContent($injectedHTML);

                // old code: inject cookie banner just before the closing </body> tag
                // $content = str_replace('</body>',$cookieBanner.'</body>', $content);
                // $response->setContent($content);
            }
        }
    }

    /**
     * Transform $routeOrUrl into the url if the route is defined
     * otherwise return the plain $routeOrUrl value
     *
     * @return string|null
     */
    private function generateUrl($routeOrUrl): ?string
    {
        if($routeOrUrl)
        {
            try{
                return $this->urlGenerator->generate($routeOrUrl);
            }
            catch (RouteNotFoundException $e)
            {
                return $routeOrUrl;
            }
        }
        else
        {
            return null;
        }
    }
}