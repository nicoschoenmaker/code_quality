<?php

namespace Hostnet\Bundle\HostnetCodeQualityBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder,
    Symfony\Component\Config\FileLocator,
    Symfony\Component\HttpKernel\DependencyInjection\Extension,
    Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HostnetCodeQualityExtension extends Extension
{
  /**
   * @see \Symfony\Component\DependencyInjection\Extension\ExtensionInterface::load()
   */
  public function load(array $configs, ContainerBuilder $container)
  {
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('services.yml');

    $container->setParameter('hostnet_code_quality.temp_cq_dir_name',
      $config['temp_cq_dir_name']);
    $container->setParameter('hostnet_code_quality.scm',
      $config['scm']);
    $container->setParameter('hostnet_code_quality.raw_file_url_mask_1',
      $config['raw_file_url_mask_1']);
    $container->setParameter('hostnet_code_quality.raw_file_url_mask_2',
      $config['raw_file_url_mask_2']);
    $container->setParameter('hostnet_code_quality.review_board_base_url',
      $config['review_board_base_url']);
    $container->setParameter('hostnet_code_quality.review_board_username',
      $config['review_board_username']);
    $container->setParameter('hostnet_code_quality.review_board_password',
      $config['review_board_password']);
    $container->setParameter('hostnet_code_quality.review_board_auto_shipit',
      $config['review_board_auto_shipit']);
    $container->setParameter('hostnet_code_quality.review_board_previous_process_date_file',
      $config['review_board_previous_process_date_file']);
  }
}
