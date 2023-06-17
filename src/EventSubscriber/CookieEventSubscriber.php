<?php

namespace Kikwik\CookieBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class CookieEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var string
     */
    private $cookieName;
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var null|string
     */
    private $cookieValue = null;

    public function __construct(Environment $twig, string $cookieName)
    {
        $this->cookieName = $cookieName;
        $this->twig = $twig;
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
        $this->cookieValue = $requestEvent->getRequest()->cookies->get($this->cookieName);
    }

    public function onKernelResponse(ResponseEvent $responseEvent)
    {
        if($responseEvent->isMainRequest() && is_null($this->cookieValue))
        {
            $cookieBanner = sprintf('<div class="kwc-banner">%s</div>',$this->twig->render('@KikwikCookie/_cookieBanner.html.twig'));

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