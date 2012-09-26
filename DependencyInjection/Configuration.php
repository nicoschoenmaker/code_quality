<?php

namespace Hostnet\HostnetCodeQualityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder,
    Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('hostnet_code_quality');

        $rootNode
          ->children()
            ->scalarNode('scm')
              ->defaultNull()
                ->end()
            ->scalarNode('raw_file_url_mask')
              ->defaultNull()
                ->end()
          ->end();

        return $treeBuilder;
    }
}
