<?php

namespace Kikwik\CookieBundle\Service;

use Doctrine\Persistence\ManagerRegistry;
use Kikwik\CookieBundle\Entity\CookieConsentLog;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class ConsentManager
{
    /**
     * @var ManagerRegistry
     */
    private $doctrine;
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
    /**
     * @var bool
     */
    private $enableConsentLog;


    public function __construct(RequestStack $requestStack, ManagerRegistry $doctrine, string $cookiePrefix, int $cookieLifetime, string $consentVersion, array $categories, bool $enableConsentLog)
    {
        $this->doctrine = $doctrine;
        $this->cookiePrefix = $cookiePrefix;
        $this->cookieLifetime = $cookieLifetime;
        $this->consentVersion = $consentVersion;
        $this->categories = $categories;
        $this->enableConsentLog = $enableConsentLog;

        if($requestStack->getCurrentRequest())
        {
            $this->init($requestStack->getCurrentRequest());
        }
    }

    private $userHasChoosen = null;
    private $consentKey = null;
    private $consentValue = null;

    private $clientIp = null;
    private $userAgent = null;

    public function init(Request $request)
    {
        $userConsentVersion = $request->cookies->get($this->cookiePrefix.'_version', false);
        $this->userHasChoosen = $userConsentVersion == $this->consentVersion;
        $this->consentKey = $request->cookies->get($this->cookiePrefix.'_key', $this->uuidv4());
        $this->consentValue = json_decode($request->cookies->get($this->cookiePrefix.'_value','[]'),true);
        $this->clientIp = $request->getClientIp();
        $this->userAgent = $request->headers->get('User-Agent');
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
            $this->consentKey,
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );
        $response->headers->setCookie(Cookie::create(
            $this->cookiePrefix.'_value',
            json_encode($this->consentValue),
            strtotime(sprintf('+%d days',$this->cookieLifetime)))
        );

        if($this->enableConsentLog)
        {
            $consentLog = new CookieConsentLog();
            $consentLog->setConsentKey($this->consentKey);
            $consentLog->setConsentValue($this->consentValue);
            $consentLog->setConsentVersion($this->consentVersion);
            $consentLog->setConsentAt(new \DateTimeImmutable());
            $consentLog->setConsentFromIp($this->clientIp);
            $consentLog->setUserAgent($this->userAgent);

            $em = $this->doctrine->getManager();
            $em->persist($consentLog);
            $em->flush();
        }
    }

    /**
     * @return bool
     */
    public function getUserHasChoosen(): bool
    {
        if(is_null($this->userHasChoosen)) throw new \Exception('ConsentManager not initialized');
        return $this->userHasChoosen;
    }





    private function uuidv4()
    {
        $data =random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}