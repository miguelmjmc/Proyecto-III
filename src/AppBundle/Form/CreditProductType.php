<?php

namespace AppBundle\Form;

use AppBundle\Utils\MeasurementUnit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', null, array('choice_label' => 'fullProductName', 'placeholder' => 'Select', 'attr' => array('class' => 'selectpicker', 'data-live-search' => "true")))
            ->add('measurementUnit', ChoiceType::class, array('choices' => MeasurementUnit::units()))
            ->add('amount', null, array('grouping' => true, 'scale' => 2, 'attr' => array('class' => 'money')))
            ->add('quantity', null, array('grouping' => true, 'scale' => 2, 'attr' => array('class' => 'number')))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CreditProduct'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_creditproduct';
    }
}
