<?php

namespace FlexModel\FlexModelBundle\DependencyInjection;

use DOMDocument;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
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

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $domDocument->load($config['resource']);

        $container->setParameter('flex_model.document', $domDocument);
        $container->setParameter('flex_model.cache_path', $config['cache_path']);
    }
}
