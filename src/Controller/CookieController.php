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

    public function acceptAll(Request $request)
    {
        // accept all categories
        $this->consentManager->allowCategory('technical');
        foreach($this->consentManager->getAvailableCategories() as $category)
        {
            $this->consentManager->allowCategory($category);
        }

        $response = new Response();
        $this->consentManager->setCookie($response);
        return $response;
    }

    public function denyAll(Request $request)
    {
        // accept only technical categories
        $this->consentManager->allowCategory('technical');
        foreach($this->consentManager->getAvailableCategories() as $category)
        {
            $this->consentManager->denyCategory($category);
        }

        $response = new Response();
        $this->consentManager->setCookie($response);
        return $response;
    }
}