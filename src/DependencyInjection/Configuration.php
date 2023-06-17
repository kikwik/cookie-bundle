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
                ->scalarNode('cookie_name')->defaultValue('kwc_consent')->cannotBeEmpty()->end()
                ->integerNode('cookie_lifetime')->defaultValue(60)->info('number days after cookie expiration')->end()
                ->scalarNode('privacy_route')->defaultNull()->end()
            ->end()
        ;

        return $treeBuilder;
    }

}