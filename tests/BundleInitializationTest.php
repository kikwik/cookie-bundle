<?php

namespace Kikwik\CookieBundle\Tests;

use Kikwik\CookieBundle\EventSubscriber\CookieEventSubscriber;
use Kikwik\CookieBundle\KikwikCookieBundle;
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
        $kernel->addTestBundle(KikwikCookieBundle::class);
        $kernel->addTestBundle(TwigBundle::class);
        $kernel->handleOptions($options);
        $kernel->addTestRoutingFile(__DIR__.'/../src/Resources/config/routes.xml');

        return $kernel;
    }

    public function testInitBundle(): void
    {
        // Boot the kernel.
        $kernel = self::bootKernel();

        // Get the container
        $container = $kernel->getContainer();

        // Or for FrameworkBundle@^5.3.6 to access private services without the PublicCompilerPass
        // $container = self::getContainer();

        // Test if your services exists
        $this->assertTrue($container->has('kikwik_cookie.event_subscriber.cookie_event_subscriber'));
        $service = $container->get('kikwik_cookie.event_subscriber.cookie_event_subscriber');
        $this->assertInstanceOf(CookieEventSubscriber::class, $service);
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
}