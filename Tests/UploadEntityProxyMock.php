<?php

namespace FlexModel\FlexModelBundle\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Mock class mimicking the behavior of a Doctrine proxy class.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class UploadEntityProxyMock extends UploadEntityMock
{
    /**
     * Overloaded trait method alias.
     *
     * @param UploadedFile $file
     */
    public function setImageUpload(UploadedFile $file = null)
    {
        parent::setImageUpload($file);
    }
}
