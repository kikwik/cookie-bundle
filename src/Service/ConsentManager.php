<?php

namespace Kikwik\CookieBundle\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConsentManager
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

    private $userHasChoosen = null;
    private $consentKey = null;
    private $consentValue = null;

    public function init(Request $request)
    {
        $this->userHasChoosen = $request->cookies->get($this->cookiePrefix.'_has_chosen', false);
        $this->consentKey = $request->cookies->get($this->cookiePrefix.'_key', $this->uuidv4());
        $this->consentValue = json_decode($request->cookies->get($this->cookiePrefix.'_value',''),true);
    }

    public function choose(array $value)
    {
        $this->userHasChoosen = true;
        $this->consentValue = $value;
    }

    public function setCookie(Response $response)
    {
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_has_chosen',
            $this->getUserHasChoosen(),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_key',
            $this->getConsentKey(),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_value',
            json_encode($this->getConsentValue()),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
    }

    /**
     * @return bool
     */
    public function getUserHasChoosen(): bool
    {
        if(is_null($this->userHasChoosen)) throw new \Exception('ConsentManager not initialized');
        return $this->userHasChoosen;
    }

    /**
     * @return string
     */
    public function getConsentKey(): string
    {
        if(is_null($this->consentKey)) throw new \Exception('ConsentManager not initialized');
        return $this->consentKey;
    }

    /**
     * @return array
     */
    public function getConsentValue(): array
    {
        if(is_null($this->consentValue)) throw new \Exception('ConsentManager not initialized');
        return $this->consentValue;
    }



    private function uuidv4()
    {
        $data =random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}