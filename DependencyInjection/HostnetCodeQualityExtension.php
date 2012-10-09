<?php

namespace Hostnet\HostnetCodeQualityBundle\DependencyInjection;

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
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if(isset($config['scm'])) {
          $container->setParameter('hostnet_code_quality.scm', $config['scm']);
        } else {
          throw new \InvalidArgumentException("The 'scm' setting must be set in the main paramateres.yml config file.");
        }

        if(isset($config['raw_file_url_mask'])) {
          $container->setParameter('hostnet_code_quality.raw_file_url_mask', $config['raw_file_url_mask']);
        } else {
          throw new \InvalidArgumentException("The 'raw_file_url_mask' setting must be set in the main paramateres.yml config file.");
        }
    }
}
