<?php

namespace FlexModel\FlexModelBundle\CacheWarmer;

use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;

/**
 * FlexModelCacheWarmer.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FlexModelCacheWarmer extends CacheWarmer
{
    /**
     * The service container instance.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * The location of cache path.
     *
     * @var string
     */
    private $cachePath;

    /**
     * Creates a new FlexModelCacheWarmer instance.
     *
     * @param ContainerInterface $container
     * @param string             $cachePath
     */
    public function __construct(ContainerInterface $container, $cachePath)
    {
        $this->container = $container;
        $this->cachePath = $cachePath;
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        if (is_dir($this->cachePath) === false) {
            if (@mkdir($this->cachePath, 0770, true) === false) {
                throw new RuntimeException(sprintf('Unable to create the FlexModel directory "%s".', $this->cachePath));
            }
        } elseif (is_writable($this->cachePath) === false) {
            throw new RuntimeException(sprintf('The FlexModel directory "%s" is not writeable for the current system user.', $this->cachePath));
        }

        // Triggers cache creation.
        $this->container->get('flexmodel')->reload();
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }
}
