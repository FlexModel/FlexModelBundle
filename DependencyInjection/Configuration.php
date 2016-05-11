<?php

namespace FlexModel\FlexModelBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration validates and merges configuration from the app/config files.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('flex_model');

        $rootNode
            ->children()
                ->scalarNode('resource')->isRequired()->end()
                ->scalarNode('file_upload_path')->end()
            ->end();

        return $treeBuilder;
    }
}
