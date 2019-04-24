<?php

namespace AppBundle\Form;

use AppBundle\Form\EventListener\UploadFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('rif', null, array('attr' => array('class' => 'rif')))
            ->add('email')
            ->add('phone', null, array('attr' => array('class' => 'phone')))
            ->add('address', TextareaType::class)
        ;

        if ('GET' !== $builder->getMethod()) {
            $builder->addEventSubscriber(new UploadFileSubscriber());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\CompanyProfile'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_companyprofile';
    }
}
