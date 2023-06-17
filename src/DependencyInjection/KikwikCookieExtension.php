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

        $cookieEventSubscriber = $container->getDefinition('kikwik_cookie.event_subscriber.cookie_event_subscriber');
        $cookieEventSubscriber->setArgument('$cookiePrefix', $config['cookie_prefix']);
        $cookieEventSubscriber->setArgument('$privacyRoute', $config['privacy_route']);


        $cookieController = $container->getDefinition('kikwik_cookie.controller.cookie_controller');
        $cookieController->setArgument('$cookiePrefix', $config['cookie_prefix']);
        $cookieController->setArgument('$cookieLifetime', $config['cookie_lifetime']);
    }

}