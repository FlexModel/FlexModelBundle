<?php

namespace FlexModel\FlexModelBundle\Tests\Model;

use FlexModel\FlexModelBundle\Tests\UploadEntityMock;
use FlexModel\FlexModelBundle\Tests\UploadEntityProxyMock;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * UploadTraitTest.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class UploadTraitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests if the UploadEntityMock::setImageUpload (alias of UploadTrait::setFileUpload)
     * sets the expected file uploads property.
     */
    public function testSetFileUpload()
    {
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $entityMock = new UploadEntityMock();
        $entityMock->setImageUpload($uploadedFileMock);

        $this->assertAttributeSame(
            array(
                'image' => $uploadedFileMock,
            ),
            'fileUploads',
            $entityMock
        );
    }

    /**
     * Tests if the UploadEntityProxyMock::setImageUpload (alias of UploadTrait::setFileUpload)
     * sets the expected file uploads property.
     *
     * This tests the scenario of a Doctrine entity being a parent class of
     * a proxy class with all the method overloaded as this changes the
     * PHP stack to determine the caller method.
     */
    public function testSetFileUploadFromProxy()
    {
        $uploadedFileMock = $this->getMockBuilder(UploadedFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uploadEntityProxyMock = new UploadEntityProxyMock();
        $uploadEntityProxyMock->setImageUpload($uploadedFileMock);

        $this->assertAttributeSame(
            array(
                'image' => $uploadedFileMock,
            ),
            'fileUploads',
            $uploadEntityProxyMock
        );
    }
}
