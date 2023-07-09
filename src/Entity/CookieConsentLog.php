<?php

namespace Kikwik\CookieBundle\Entity;

class CookieConsentLog
{
    /**************************************/
    /* PROPERTIES                         */
    /**************************************/

    /**
     * Unique id of this document.
     *
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $consentKey;

    /**
     * @var array
     */
    protected $consentValue;

    /**
     * @var string
     */
    protected $consentVersion;

    /**
     * @var \DateTimeImmutable
     */
    protected $consentAt;

    /**
     * @var string
     */
    protected $consentFromIp;

    /**
     * @var string
     */
    protected $userAgent;

    /**************************************/
    /* CUSTOM METHODS                     */
    /**************************************/

    public function __toString()
    {
        return (string)$this->consentKey;
    }


    /**************************************/
    /* GETTERS & SETTERS                  */
    /**************************************/

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getConsentKey(): string
    {
        return $this->consentKey;
    }

    /**
     * @param string $consentKey
     * @return CookieConsentLog
     */
    public function setConsentKey(string $consentKey): CookieConsentLog
    {
        $this->consentKey = $consentKey;
        return $this;
    }

    /**
     * @return array
     */
    public function getConsentValue(): array
    {
        return $this->consentValue;
    }

    /**
     * @param array $consentValue
     * @return CookieConsentLog
     */
    public function setConsentValue(array $consentValue): CookieConsentLog
    {
        $this->consentValue = $consentValue;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsentVersion(): string
    {
        return $this->consentVersion;
    }

    /**
     * @param string $consentVersion
     * @return CookieConsentLog
     */
    public function setConsentVersion(string $consentVersion): CookieConsentLog
    {
        $this->consentVersion = $consentVersion;
        return $this;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getConsentAt(): \DateTimeImmutable
    {
        return $this->consentAt;
    }

    /**
     * @param \DateTimeImmutable $consentAt
     * @return CookieConsentLog
     */
    public function setConsentAt(\DateTimeImmutable $consentAt): CookieConsentLog
    {
        $this->consentAt = $consentAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getConsentFromIp(): string
    {
        return $this->consentFromIp;
    }

    /**
     * @param string $consentFromIp
     * @return CookieConsentLog
     */
    public function setConsentFromIp(string $consentFromIp): CookieConsentLog
    {
        $this->consentFromIp = $consentFromIp;
        return $this;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     * @return CookieConsentLog
     */
    public function setUserAgent(string $userAgent): CookieConsentLog
    {
        $this->userAgent = $userAgent;
        return $this;
    }





}