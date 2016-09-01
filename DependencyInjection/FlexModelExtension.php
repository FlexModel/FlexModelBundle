<?php

namespace FlexModel\FlexModelBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * FlexModelExtension loads and manages the bundle configuration.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FlexModelExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('flex_model.resource', $config['resource']);
        $container->setParameter('flex_model.bundle_name', $config['bundle_name']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['file_upload_path'])) {
            $container->setParameter('flex_model.file_upload_path', $config['file_upload_path']);

            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('upload_services.xml');
        }
    }
}
