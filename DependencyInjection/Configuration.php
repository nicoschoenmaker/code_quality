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
   * @see \Symfony\Component\Config\Definition\ConfigurationInterface::getConfigTreeBuilder()
   */
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('hostnet_code_quality');

    $rootNode
      ->children()
        ->scalarNode('temp_cq_dir_name')
          ->defaultValue(sys_get_temp_dir() . 'codequality')
            ->end()
        ->scalarNode('scm')
          ->isRequired()
            ->end()
        ->scalarNode('raw_file_url_mask_1')
          ->end()
        ->scalarNode('raw_file_url_mask_2')
          ->end()
        ->scalarNode('domain')
          ->end()
        ->scalarNode('review_board_username')
          ->end()
        ->scalarNode('review_board_password')
          ->end()
        ->scalarNode('review_board_auto_shipit')
          ->defaultValue(false)
            ->end()
      ->end();

    return $treeBuilder;
  }
}
