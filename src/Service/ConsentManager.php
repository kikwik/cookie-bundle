<?php

namespace Kikwik\CookieBundle\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
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
    /**
     * @var string
     */
    private $consentVersion;
    /**
     * @var array
     */
    private $categories;


    public function __construct(RequestStack $requestStack, string $cookiePrefix, int $cookieLifetime, string $consentVersion, array $categories)
    {
        $this->cookiePrefix = $cookiePrefix;
        $this->cookieLifetime = $cookieLifetime;
        $this->consentVersion = $consentVersion;
        $this->categories = $categories;
        if($requestStack->getCurrentRequest())
        {
            $this->init($requestStack->getCurrentRequest());
        }
    }

    private $userHasChoosen = null;
    private $consentKey = null;
    private $consentValue = null;

    public function init(Request $request)
    {
        $userConsentVersion = $request->cookies->get($this->cookiePrefix.'_version', false);
        $this->userHasChoosen = $userConsentVersion == $this->consentVersion;
        $this->consentKey = $request->cookies->get($this->cookiePrefix.'_key', $this->uuidv4());
        $this->consentValue = json_decode($request->cookies->get($this->cookiePrefix.'_value','[]'),true);
    }

    /**
     * @return array
     */
    public function getAvailableCategories(): array
    {
        return $this->categories;
    }

    public function allowCategory(string $category)
    {
        $this->consentValue[$category] = true;
    }

    public function denyCategory(string $category)
    {
        $this->consentValue[$category] = false;
    }

    public function isCategoryAllowed(string $category): bool
    {
        return $this->userHasChoosen && $this->consentValue[$category] ?? false;
    }

    public function setCookie(Response $response)
    {
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_version',
            $this->consentVersion,
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_key',
            $this->getConsentKey(),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_value',
            json_encode($this->consentValue),
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
        return $this->consentKey;
    }




    private function uuidv4()
    {
        $data =random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}