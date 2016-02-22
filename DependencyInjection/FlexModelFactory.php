<?php

namespace FlexModel\FlexModelBundle\DependencyInjection;

use DOMDocument;
use FlexModel\FlexModel;

/**
 * FlexModelFactory.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FlexModelFactory
{
    /**
     * Creates a new FlexModel instance.
     *
     * @param string $identifier
     * @param string $resource
     * @param string $cachePath
     *
     * @return FlexModel
     */
    public static function createFlexModel($identifier, $resource, $cachePath)
    {
        $domDocument = new DOMDocument('1.0', 'UTF-8');
        $domDocument->load($resource);

        $flexModel = new FlexModel($identifier);
        $flexModel->load($domDocument, $cachePath);

        return $flexModel;
    }
}
