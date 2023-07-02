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

    public function __construct(ConsentManager $consentManager, Environment $twig, UrlGeneratorInterface $urlGenerator, ?string $privacyPolicy, ?string $cookiePolicy, array $bannerClasses)
    {
        $this->consentManager = $consentManager;
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
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
            if($this->consentManager->getUserHasChoosen())
            {
                // inject the review icon
            }
            else
            {
                // inject the consent banner
                $cookieBanner = sprintf('<div class="kwc-banner">%s</div><script src="/bundles/kikwikcookie/cookie.js?v=%s"></script>',
                    $this->twig->render('@KikwikCookie/_cookieBanner.html.twig',[
                        'privacy_url' => $this->generateUrl($this->privacyPolicy),
                        'cookie_url' => $this->generateUrl($this->cookiePolicy),
                        'bannerClasses' => $this->bannerClasses,
                        'categories' => $this->categories,
                    ]),
                    filemtime(__DIR__.'/../Resources/public/cookie.js')
                );

                $response = $responseEvent->getResponse();

                $content = $response->getContent();
                if(strpos($content, '</body>') !== false)
                {
                    $content = str_replace('</body>',$cookieBanner.'</body>', $content);
                    $response->setContent($content);
                }
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