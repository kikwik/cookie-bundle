<?php

namespace Kikwik\CookieBundle\Controller;

use Kikwik\CookieBundle\Service\ConsentManager;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieController
{
    /**
     * @var ConsentManager
     */
    private $consentManager;

    public function __construct(ConsentManager $consentManager)
    {
        $this->consentManager = $consentManager;
    }

    public function save(Request $request)
    {
        $this->consentManager->init($request);
        $this->consentManager->choose(['technical'=>true]);

        $response = new Response();
        $this->consentManager->setCookie($response);
        return $response;
    }

}