<?php

namespace FlexModel\FlexModelBundle\DataCollector;

use Exception;
use FlexModel\FlexModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * FlexModelDataCollector.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FlexModelDataCollector extends DataCollector
{
    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * Constructs a new FlexModelDataCollector.
     *
     * @param FlexModel $flexModel
     */
    public function __construct(FlexModel $flexModel)
    {
        $this->flexModel = $flexModel;
    }

    /**
     * Collect the specific FlexModel data for the Symfony Profiler.
     *
     * @param Request   $request
     * @param Response  $response
     * @param Exception $exception
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $objects = array();
        $objectNames = $this->flexModel->getObjectNames();
        foreach ($objectNames as $objectName) {
            $fieldNames = $this->flexModel->getFieldNames($objectName);

            $objects[] = array(
                'objectName' => $objectName,
                'fieldNames' => $fieldNames,
            );
        }

        $this->data = array(
            'objects' => $objects,
        );
    }

    /**
     * Returns information about the objects loaded from the FlexModel.
     *
     * @return array
     */
    public function getObjects()
    {
        return $this->data['objects'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'flexmodel';
    }
}
