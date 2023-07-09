<?php

namespace Kikwik\CookieBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class KikwikCookieExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);


        $consentManager = $container->getDefinition('kikwik_cookie.service.consent_manager');
        $consentManager->setArgument('$cookiePrefix', $config['cookie_prefix']);
        $consentManager->setArgument('$cookieLifetime', $config['cookie_lifetime']);
        $consentManager->setArgument('$consentVersion', $config['consent_version']);
        $consentManager->setArgument('$categories', $config['categories']);
        $consentManager->setArgument('$enableConsentLog', $config['enable_consent_log']);


        $cookieEventSubscriber = $container->getDefinition('kikwik_cookie.event_subscriber.cookie_event_subscriber');
        $cookieEventSubscriber->setArgument('$privacyPolicy', $config['privacy_policy']);
        $cookieEventSubscriber->setArgument('$cookiePolicy', $config['cookie_policy']);
        $cookieEventSubscriber->setArgument('$bannerClasses', $config['banner_classes']);



    }

}