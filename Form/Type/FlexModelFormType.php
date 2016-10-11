<?php

namespace FlexModel\FlexModelBundle\Form\Type;

use FlexModel\FlexModel;
use ReflectionClass;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use FlexModel\FlexModelBundle\Form\DataTransformer\HTMLPurifierTransformer;

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

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (isset($options['data_class']) && isset($options['form_name'])) {
            $reflectionClass = new ReflectionClass($options['data_class']);
            $objectName = $reflectionClass->getShortName();

            $formConfiguration = $this->flexModel->getFormConfiguration($objectName, $options['form_name']);
            if (is_array($formConfiguration)) {
                foreach ($formConfiguration['fields'] as $formFieldConfiguration) {
                    $fieldConfiguration = $this->flexModel->getField($objectName, $formFieldConfiguration['name']);

                    $fieldType = $this->getFieldType($formFieldConfiguration, $fieldConfiguration);
                    $fieldOptions = $this->getFieldOptions($formFieldConfiguration, $fieldConfiguration);
                    $fieldName = $fieldConfiguration['name'];
                    if ($fieldType === FileType::class) {
                        $fieldName .= '_upload';
                    }

                    $builder->add($fieldName, $fieldType, $fieldOptions);
                    if ($fieldConfiguration['datatype'] === 'HTML') {
                        $builder->get($fieldName)->addModelTransformer(new HTMLPurifierTransformer());
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('form_name', null);
    }

    /**
     * Returns the field type for a field.
     *
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     *
     * @return array
     */
    private function getFieldType(array $formFieldConfiguration, array $fieldConfiguration)
    {
        if (isset($formFieldConfiguration['fieldtype'])) {
            return $formFieldConfiguration['fieldtype'];
        }

        $fieldType = null;
        switch ($fieldConfiguration['datatype']) {
            case 'BOOLEAN':
                $fieldType = CheckboxType::class;
                break;
            case 'DATE':
                $fieldType = DateType::class;
                break;
            case 'DATEINTERVAL':
                $fieldType = TextType::class;
                break;
            case 'DATETIME':
                $fieldType = DateTimeType::class;
                break;
            case 'DECIMAL':
                $fieldType = NumberType::class;
                break;
            case 'FILE':
                $fieldType = FileType::class;
                break;
            case 'FLOAT':
                $fieldType = NumberType::class;
                break;
            case 'INTEGER':
                $fieldType = IntegerType::class;
                break;
            case 'SET':
                $fieldType = ChoiceType::class;
                break;
            case 'TEXT':
            case 'HTML':
            case 'JSON':
                $fieldType = TextareaType::class;
                break;
            case 'VARCHAR':
                $fieldType = TextType::class;
                break;
        }

        if (isset($formFieldConfiguration['options']) || isset($fieldConfiguration['options'])) {
            $fieldType = ChoiceType::class;
        }

        return $fieldType;
    }

    /**
     * Returns the field options for a field.
     *
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     *
     * @return array
     */
    protected function getFieldOptions(array $formFieldConfiguration, array $fieldConfiguration)
    {
        $options = array(
            'label' => $fieldConfiguration['label'],
            'required' => false,
            'constraints' => array(),
        );
        if (isset($fieldConfiguration['required'])) {
            $options['required'] = $fieldConfiguration['required'];
        }
        if (isset($formFieldConfiguration['widget'])) {
            $options['widget'] = $formFieldConfiguration['widget'];
        }
        if (isset($formFieldConfiguration['format'])) {
            $options['format'] = $formFieldConfiguration['format'];
        }

        $this->addFieldPlaceholder($options, $formFieldConfiguration, $fieldConfiguration);
        $this->addFieldOptionsByDatatype($options, $fieldConfiguration);
        $this->addFieldChoiceOptions($options, $formFieldConfiguration, $fieldConfiguration);
        $this->addFieldConstraintOptions($options, $formFieldConfiguration);

        return $options;
    }

    /**
     * Adds the placeholder for a field based on the type.
     *
     * @param array $options
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     */
    public function addFieldPlaceholder(array &$options, array $formFieldConfiguration, array $fieldConfiguration)
    {
        $fieldType = $this->getFieldType($formFieldConfiguration, $fieldConfiguration);

        if (isset($formFieldConfiguration['notices']['placeholder'])) {
            if (in_array($fieldType, array(ChoiceType::class, DateType::class, BirthdayType::class, DateTimeType::class, CountryType::class))) {
                $options['placeholder'] = $formFieldConfiguration['notices']['placeholder'];
            } else {
                $options['attr']['placeholder'] = $formFieldConfiguration['notices']['placeholder'];
            }
        }
    }

    /**
     * Adds field options based on the datatype of a field.
     *
     * @param array $options
     * @param array $fieldConfiguration
     */
    private function addFieldOptionsByDatatype(array &$options, array $fieldConfiguration)
    {
        switch ($fieldConfiguration['datatype']) {
            case 'SET':
                $options['multiple'] = true;
                break;
        }
    }

    /**
     * Adds the choices option to the field options.
     *
     * @param array $options
     * @param array $formFieldConfiguration
     * @param array $fieldConfiguration
     */
    private function addFieldChoiceOptions(array &$options, array $formFieldConfiguration, array $fieldConfiguration)
    {
        if (isset($formFieldConfiguration['options'])) {
            $fieldConfiguration['options'] = $formFieldConfiguration['options'];
        }

        if (isset($fieldConfiguration['options'])) {
            $options['choices'] = array();
            foreach ($fieldConfiguration['options'] as $option) {
                $options['choices'][$option['label']] = $option['value'];
            }
        }
    }

    /**
     * Adds the constraints option to the field options.
     *
     * @param array $options
     * @param array $formFieldConfiguration
     */
    private function addFieldConstraintOptions(array &$options, array $formFieldConfiguration)
    {
        if ($options['required'] === true) {
            $options['constraints'][] = new NotBlank();
        }

        if (isset($formFieldConfiguration['validators'])) {
            $options['constraints'] = array();
            foreach ($formFieldConfiguration['validators'] as $validatorClass => $validatorOptions) {
                $options['constraints'][] = new $validatorClass($validatorOptions);
            }
        }
    }
}
