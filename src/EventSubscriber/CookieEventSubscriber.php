<?php

namespace Kikwik\CookieBundle\EventSubscriber;

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
     * @var string
     */
    private $cookiePrefix;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var null|string
     */
    private $cookieValue = null;
    /**
     * @var null|string
     */
    private $privacyRoute;
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(Environment $twig, UrlGeneratorInterface $urlGenerator, string $cookiePrefix, ?string $privacyRoute)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->cookiePrefix = $cookiePrefix;
        $this->privacyRoute = $privacyRoute;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }

    public function onKernelRequest(RequestEvent $requestEvent)
    {
        $this->cookieValue = $requestEvent->getRequest()->cookies->get($this->cookiePrefix.'_banner', null);
    }

    public function onKernelResponse(ResponseEvent $responseEvent)
    {
        if($responseEvent->isMainRequest() && is_null($this->cookieValue))
        {
            $privacy_url = $this->privacyRoute;
            if($this->privacyRoute)
            {
                try{
                    $privacy_url = $this->urlGenerator->generate($this->privacyRoute);
                }
                catch (RouteNotFoundException $e){}
            }

            $cookieBanner = sprintf('<div class="kwc-banner">%s</div><script src="/bundles/kikwikcookie/cookie.js?v=%s"></script>',
                $this->twig->render('@KikwikCookie/_cookieBanner.html.twig',[
                    'privacy_url' => $privacy_url,
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