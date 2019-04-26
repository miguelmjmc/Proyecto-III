<?php

namespace AppBundle\Form;

use AppBundle\Entity\Credit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', DateType::class, array('widget' => 'single_text', 'html5' => false, 'format' => 'yyyy/MM/dd', 'attr' => array('class' => 'datepicker', 'readonly' => true)))
            ->add('deadlineChoice', ChoiceType::class, array('mapped' => false, 'label' => 'Deadline', 'choices' => array('1 Semana' => 1, '15 Dias' => 2, '1 Mes' => 3)))
            ->add('comment')
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Credit $data */
            $data = $event->getData();

            if ($form->isValid()) {
                $date = new \DateTime($data->getDate()->format('Y-m-d'));

                switch ($form['deadlineChoice']->getData()) {
                    case 1:
                        $date->modify('+7 days');
                        break;
                    case 2:
                        $date->modify('+15 days');
                        break;
                    case 3:
                        $date->modify('+1 month');
                        break;
                }

                $data->setDeadline($date);
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Credit'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_credit';
    }
}
