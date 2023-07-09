<?php

namespace Kikwik\CookieBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kikwik_cookie');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('cookie_prefix')->defaultValue('kwc_consent')->cannotBeEmpty()->info('the prefix for the cookie name')->end()
                ->integerNode('cookie_lifetime')->defaultValue(180)->info('number days after cookie expiration')->end()
                ->scalarNode('consent_version')->defaultValue('1.0')->info('consent version (change to invalidate old consents)')->end()
                ->scalarNode('privacy_policy')->defaultNull()->info('route or url for privacy policy')->end()
                ->scalarNode('cookie_policy')->defaultNull()->info('route or url for cookie policy')->end()
                ->arrayNode('categories')->scalarPrototype()->end()->info('list of available categories, example: [ \'functional\', \'analytics\', \'profiling\', \'marketing\' ]')->end()
                ->booleanNode('enable_consent_log')->defaultFalse()->info('save user consent in database')->end()
                ->booleanNode('enable_admin')->defaultTrue()->end()
                ->arrayNode('banner_classes')->info('banner classes for buttons and links')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('btnAccept')->defaultValue('btn btn-sm btn-success my-1')->end()
                        ->scalarNode('btnDeny')->defaultValue('btn btn-sm btn-danger my-1')->end()
                        ->scalarNode('btnChoose')->defaultValue('btn btn-sm btn-warning my-1')->end()
                        ->scalarNode('btnPrivacy')->defaultValue('m-1')->end()
                        ->scalarNode('btnCookie')->defaultValue('m-1')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

}