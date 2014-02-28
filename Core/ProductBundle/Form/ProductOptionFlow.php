<?php

namespace Core\ProductBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Symfony\Component\Form\FormTypeInterface;

class ProductOptionFlow extends FormFlow
{
    protected $formType;

    public function setFormType(FormTypeInterface $formType)
    {
        $this->formType = $formType;
    }

    public function getName()
    {
        return 'core_productbundle_productoptiontype';
    }

    protected function loadStepsConfig()
    {
        return array(
            array(
                'label' => 'Name',
                'type' => $this->formType,
            ),
            array(
                'label' => 'Value',
                'type' => $this->formType,
            )
        );
    }

    public function getFormOptions($step, array $options = array())
    {
        $options = parent::getFormOptions($step, $options);

        $formData = $this->getFormData();

        if ($step > 1) {
            $options['attributeName'] = $formData->getName()->getId();
        }

        return $options;
    }
}
