<?php

namespace FlexModel\FlexModelBundle\Form;

use FlexModel\FlexModel;

/**
 * FlexModelFormType.
 *
 * @author Niels Nijens <niels@connectholland.nl>
 */
class FlexModelFormType extends AbstractType
{
    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * Constructs a new FlexModelFormType instance.
     *
     * @param FlexModel $flexModel
     */
    public function __construct(FlexModel $flexModel)
    {
        $this->flexModel = $flexModel;
    }
}
