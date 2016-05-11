<?php

namespace FlexModel\FlexModelBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreFlushEventArgs;
use Doctrine\ORM\Events;
use FlexModel\FlexModel;
use FlexModel\FlexModelBundle\Model\UploadObjectInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;

/**
 * ObjectUploadSubscriber.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class ObjectUploadSubscriber implements EventSubscriber
{
    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * The base path for file uploads.
     *
     * @var string
     */
    private $fileUploadPath;

    /**
     * The files scheduled for deletion.
     *
     * @var array
     */
    private $filesScheduledForDeletion = array();

    /**
     * Constructs a new ObjectUploadSubscriber.
     *
     * @param FlexModel $flexModel
     * @param string    $fileUploadPath
     */
    public function __construct(FlexModel $flexModel, $fileUploadPath)
    {
        $this->flexModel = $flexModel;
        $this->fileUploadPath = $fileUploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::preFlush,
            Events::postPersist,
            Events::postUpdate,
            Events::postFlush,
        );
    }

    /**
     * Prepares upload file references for all objects implementing the UploadObjectInterface.
     *
     * @param PreFlushEventArgs $args
     */
    public function preFlush(PreFlushEventArgs $args)
    {
        $objectManager = $args->getEntityManager();
        $unitOfWork = $objectManager->getUnitOfWork();

        $entityMap = $unitOfWork->getIdentityMap();
        foreach ($entityMap as $objectClass => $objects) {
            if (in_array(UploadObjectInterface::class, class_implements($objectClass))) {
                foreach ($objects as $object) {
                    $this->prepareUploadFileReferences($object);
                }
            }
        }
    }

    /**
     * Stores the file uploads.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UploadObjectInterface) {
            $this->storeFileUploads($object);
        }
    }

    /**
     * Stores the file uploads.
     *
     * @param LifecycleEventArgs $args
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->postPersist($args);
    }

    /**
     * Removes files scheduled for deletion.
     */
    public function postFlush()
    {
        $fileSystem = new Filesystem();
        $fileSystem->remove($this->filesScheduledForDeletion);
    }

    /**
     * Sets the new file references to the uploaded files on the object and schedules the previous file reference for deletion.
     *
     * @param UploadObjectInterface $object
     */
    private function prepareUploadFileReferences(UploadObjectInterface $object)
    {
        $object->setFileUploadPath($this->fileUploadPath);

        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();

        $fileUploads = $object->getFileUploads();
        $fileFieldConfigurations = $this->flexModel->getFieldsByDatatype($objectName, 'FILE');
        foreach ($fileFieldConfigurations as $fileFieldConfiguration) {
            $camelizedFieldName = Container::camelize($fileFieldConfiguration['name']);
            $fileFieldProperty = lcfirst($camelizedFieldName);
            if (isset($fileUploads[$fileFieldProperty])) {
                $getter = 'get'.$camelizedFieldName;
                $setter = 'set'.$camelizedFieldName;

                $previousFileReference = $object->$getter();
                if (empty($previousFileReference) === false) {
                    $this->filesScheduledForDeletion[] = sprintf('%s/%s', $this->getFilePath($objectName, $fileFieldConfiguration['name']), $previousFileReference);
                }

                $fileName = md5(uniqid()).'.'.$fileUploads[$fileFieldProperty]->guessExtension();

                $object->$setter($fileName);
            }
        }
    }

    /**
     * Stores the uploaded files to the specified file system location.
     *
     * @param UploadObjectInterface $object
     */
    private function storeFileUploads(UploadObjectInterface $object)
    {
        $reflectionClass = new ReflectionClass($object);
        $objectName = $reflectionClass->getShortName();

        $fileUploads = $object->getFileUploads();
        $fileFieldConfigurations = $this->flexModel->getFieldsByDatatype($objectName, 'FILE');
        foreach ($fileFieldConfigurations as $fileFieldConfiguration) {
            $camelizedFieldName = Container::camelize($fileFieldConfiguration['name']);
            $fileFieldProperty = lcfirst($camelizedFieldName);
            if (isset($fileUploads[$fileFieldProperty])) {
                $getter = 'get'.$camelizedFieldName;
                $setter = 'set'.$camelizedFieldName.'Upload';

                $fileUploads[$fileFieldProperty]->move($this->getFilePath($objectName, $fileFieldConfiguration['name']), $object->$getter());

                $object->$setter(null);
            }
        }
    }

    /**
     * Returns the file path for a field name of an object.
     *
     * @param string $objectName
     * @param string $fieldName
     *
     * @return string
     */
    private function getFilePath($objectName, $fieldName)
    {
        return sprintf('%s/%s/%s', $this->fileUploadPath, strtolower($objectName), $fieldName);
    }
}
