<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ci', null, array('attr' => array('class' => 'ci')))
            ->add('name')
            ->add('lastName')
            ->add('creditLimit', null, array('grouping' => true, 'scale' => 2, 'attr' => array('class' => 'money')))
            ->add('email')
            ->add('phone', null, array('attr' => array('class' => 'phone')))
            ->add('address', TextareaType::class)
            ->add('comment')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Client'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_client';
    }
}
