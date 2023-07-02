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
                ->scalarNode('cookie_prefix')->defaultValue('kwc_consent')->cannotBeEmpty()->end()
                ->integerNode('cookie_lifetime')->defaultValue(180)->info('number days after cookie expiration')->end()
                ->scalarNode('consent_version')->defaultValue('1.0')->info('consent version (change to invalidate old consents)')->end()
                ->scalarNode('privacy_policy')->defaultNull()->info('route or url')->end()
                ->scalarNode('cookie_policy')->defaultNull()->info('route or url')->end()
                ->arrayNode('categories')->scalarPrototype()->end()->end()
                ->arrayNode('banner_classes')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('wrapper')->defaultValue('position-fixed bottom-0 start-0 end-0 p-1 border-top border-3 bg-white lh-sm fw-light small')->end()
                        ->scalarNode('actionWrapper')->defaultValue('text-md-end text-center')->end()
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