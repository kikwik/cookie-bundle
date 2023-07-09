<?php

namespace Kikwik\CookieBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Kikwik\CookieBundle\Controller\CookieController;
use Kikwik\CookieBundle\EventSubscriber\CookieEventSubscriber;
use Kikwik\CookieBundle\KikwikCookieBundle;
use Kikwik\CookieBundle\Service\ConsentManager;
use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\HttpKernel\KernelInterface;

class BundleInitializationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);

        // bundle to test
        $kernel->addTestBundle(KikwikCookieBundle::class);

        // Add some other bundles we depend on
        $kernel->addTestBundle(DoctrineBundle::class);
        $kernel->addTestBundle(TwigBundle::class);

        $kernel->handleOptions($options);

        // Add some configuration
        $kernel->addTestRoutingFile(__DIR__.'/../src/Resources/config/routes.xml');
        $kernel->addTestConfig(__DIR__.'/config.yaml');

        return $kernel;
    }

    public function testInitBundle(): void
    {
        // Boot the kernel.
        $kernel = self::bootKernel();

        // Get the container
        $container = $kernel->getContainer();

        // Test if your services exists
        $this->assertTrue($container->has('kikwik_cookie.event_subscriber.cookie_event_subscriber'),'service CookieEventSubscriber should exists');
        $service = $container->get('kikwik_cookie.event_subscriber.cookie_event_subscriber');
        $this->assertInstanceOf(CookieEventSubscriber::class, $service);

        $this->assertTrue($container->has('kikwik_cookie.service.consent_manager'),'service ConsentManager should exists');
        $service = $container->get('kikwik_cookie.service.consent_manager');
        $this->assertInstanceOf(ConsentManager::class, $service);

        $this->assertTrue($container->has('kikwik_cookie.controller.cookie_controller'),'service CookieController should exists');
        $service = $container->get('kikwik_cookie.controller.cookie_controller');
        $this->assertInstanceOf(CookieController::class, $service);
    }

    public function testCookieBanner()
    {
        $kernel = self::bootKernel();

        $client = new KernelBrowser($kernel);
        $client->request('GET','/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<div class="kwc-banner">',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-accept',$client->getResponse()->getContent());
        $this->assertStringNotContainsString('js-kwc-btn-policy-privacy',$client->getResponse()->getContent());
        $this->assertStringNotContainsString('js-kwc-btn-policy-cookie',$client->getResponse()->getContent());
    }

    public function testCookieBannerWithPrivacyPolicyUrl()
    {
        // Boot the kernel with a config closure, the handleOptions call in createKernel is important for that to work
        $kernel = self::bootKernel(['config' => static function(TestKernel $kernel){
            // Add some configuration
            $kernel->addTestConfig(__DIR__.'/kikwik_cookie_with-privacy-url.yaml');
        }]);

        $client = new KernelBrowser($kernel);
        $client->request('GET','/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<div class="kwc-banner">',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-accept',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-policy-privacy',$client->getResponse()->getContent());
        $this->assertStringContainsString('/privacy-policy-url',$client->getResponse()->getContent());
    }

    public function testCookieBannerWithCookiePolicyUrl()
    {
        // Boot the kernel with a config closure, the handleOptions call in createKernel is important for that to work
        $kernel = self::bootKernel(['config' => static function(TestKernel $kernel){
            // Add some configuration
            $kernel->addTestConfig(__DIR__.'/kikwik_cookie_with-cookie-url.yaml');
        }]);

        $client = new KernelBrowser($kernel);
        $client->request('GET','/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<div class="kwc-banner">',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-accept',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-policy-cookie',$client->getResponse()->getContent());
        $this->assertStringContainsString('/cookie-policy-url',$client->getResponse()->getContent());
    }

    public function testCookieBannerWithCookieAndPrivacyUrl()
    {
        // Boot the kernel with a config closure, the handleOptions call in createKernel is important for that to work
        $kernel = self::bootKernel(['config' => static function(TestKernel $kernel){
            // Add some configuration
            $kernel->addTestConfig(__DIR__.'/kikwik_cookie_with-cookie-and-privacy-url.yaml');
        }]);

        $client = new KernelBrowser($kernel);
        $client->request('GET','/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<div class="kwc-banner">',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-accept',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-policy-cookie',$client->getResponse()->getContent());
        $this->assertStringContainsString('/cookie-policy-url',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-policy-privacy',$client->getResponse()->getContent());
        $this->assertStringContainsString('/privacy-policy-url',$client->getResponse()->getContent());
    }

    public function testCookieBannerWithCategories()
    {
        // Boot the kernel with a config closure, the handleOptions call in createKernel is important for that to work
        $kernel = self::bootKernel(['config' => static function(TestKernel $kernel){
            // Add some configuration
            $kernel->addTestConfig(__DIR__.'/kikwik_cookie_with-categories.yaml');
        }]);

        $client = new KernelBrowser($kernel);
        $client->request('GET','/');
        $this->assertSame(404, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('<div class="kwc-banner">',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-accept',$client->getResponse()->getContent());
        $this->assertStringContainsString('js-kwc-btn-deny',$client->getResponse()->getContent());
    }
}