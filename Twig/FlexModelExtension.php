<?php

namespace FlexModel\FlexModelBundle\Twig;

use FlexModel\FlexModel;
use Twig_Extension;
use Twig_SimpleFilter;

/**
 * FlexModelExtension.
 *
 * @author Deborah van der Vegt <deborah@connectholland.nl>
 */
class FlexModelExtension extends Twig_Extension
{

    /**
     * The FlexModel instance.
     *
     * @var FlexModel
     */
    private $flexModel;

    /**
     * Constructs a new FlexModelExtension instance.
     *
     * @param FlexModel $flexModel
     */
    public function __construct(FlexModel $flexModel)
    {
        $this->flexModel = $flexModel;
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('flexmodel_option_label', array($this, 'optionLabelFilter')),
            new Twig_SimpleFilter('flexmodel_field_label', array($this, 'fieldLabelFilter')),
        );
    }

    /**
     * Gets the option label based on the object and field name.
     *
     * @param string $objectName
     * @param string $fieldName
     *
     * @return string $label
     */
    public function optionLabelFilter($value, $objectName, $fieldName)
    {
        $fieldConfiguration = $this->flexModel->getField($objectName, $fieldName);

        $label = "";
        if (is_array($fieldConfiguration)) {
            if (isset($fieldConfiguration['options'])) {
                if (is_array($value)) {
                    foreach ($value as $i => $valueItem) {
                        $value[$i] = $this->getLabelForValue($fieldConfiguration, $valueItem);
                    }
                    $label = implode(', ', $value);
                } else {
                    $label = $this->getLabelForValue($fieldConfiguration, $value);
                }
            } else {
                $label = $value;
            }
        }

        return $label;
    }

    /**
     * Gets the label for the set value.
     *
     * @param mixed $fieldConfiguration
     * @param string $value
     *
     * @return string $label
     */
    private function getLabelForValue($fieldConfiguration, $value)
    {
        foreach ($fieldConfiguration['options'] as $option) {
            if ($option['value'] == $value) {
                $label = $option['label'];
            }
        }

        return $label;
    }

    /**
     * Gets the field label based on the object and field name.
     *
     * @param string $objectName
     * @param string $fieldName
     *
     * @return string $label
     */
    public function fieldLabelFilter($value, $objectName, $fieldName)
    {
        $fieldConfiguration = $this->flexModel->getField($objectName, $fieldName);

        $label = "";
        if (is_array($fieldConfiguration)) {
            $label = $fieldConfiguration['label'];
        }

        return $label;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName()
    {
        return 'flex_model_extension';
    }

}
