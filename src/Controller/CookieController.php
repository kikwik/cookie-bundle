<?php

namespace Kikwik\CookieBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CookieController
{
    /**
     * @var string
     */
    private $cookiePrefix;
    /**
     * @var int
     */
    private $cookieLifetime;

    public function __construct(string $cookiePrefix, int $cookieLifetime)
    {
        $this->cookiePrefix = $cookiePrefix;
        $this->cookieLifetime = $cookieLifetime;
    }

    public function save(Request $request)
    {
        $response = new Response();
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_banner',
            date('Y-m-d-His'),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        return $response;
    }

}