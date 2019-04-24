<?php

namespace AppBundle\Form;

use AppBundle\Utils\MeasurementUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('defaultMeasurementUnit', ChoiceType::class, array('choices' => MeasurementUnit::units(), 'placeholder' => 'Select'))
            ->add('productBrand', null, array('choice_label' => 'name', 'placeholder' => 'Select'))
            ->add('productCategory', null, array('choice_label' => 'name', 'placeholder' => 'Select'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_product';
    }
}
