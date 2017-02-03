<?php

namespace FlexModel\FlexModelBundle\Tests;

use FlexModel\FlexModelBundle\Model\UploadTrait;

/**
 * Mock class for testing the UploadTrait.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class UploadEntityMock
{
    use UploadTrait {
        getFileUpload as getImageUpload;
        setFileUpload as setImageUpload;
    }
}
