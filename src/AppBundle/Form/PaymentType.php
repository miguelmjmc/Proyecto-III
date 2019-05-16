<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array('widget' => 'single_text', 'html5' => false, 'format' => 'yyyy/MM/dd', 'attr' => array('class' => 'datepicker', 'readonly' => true)))
            ->add('amount', null, array('grouping' => true, 'scale' => 2, 'attr' => array('class' => 'money')))
            ->add('paymentMethod', null, array('choice_label' => 'name', 'placeholder' => 'Select'))
            ->add('comment')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Payment'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_payment';
    }
}
