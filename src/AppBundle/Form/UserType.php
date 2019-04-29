<?php

namespace AppBundle\Form;

use AppBundle\Entity\User;
use AppBundle\Form\EventListener\UploadFileSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $data = $builder->getData();

        $rol = 'ROLE_USER';

        if ($data instanceof User) {
            if ($data->hasRole('ROLE_ADMIN')) {
                $rol = 'ROLE_ADMIN';
            }
        }

        if ('POST' === $builder->getMethod()) {
            $builder
                ->add('name')
                ->add('lastName')
                ->add('username')
                ->add('email')
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array('label' => 'Status', 'choices' => array('Enabled' => true, 'Disabled' => false))
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                )
                ->add(
                    'plain_password',
                    PasswordType::class,
                    array(
                        'constraints' => array(
                            new NotBlank(),
                            new Length(array('min' => 8, 'max' => 20)),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[a-z])/',
                                    'message' => 'Este valor debería tener 1 letra minuscula o más.',
                                )
                            ),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[A-Z])/',
                                    'message' => 'Este valor debería tener 1 letra mayuscula o más.',
                                )
                            ),
                            new Regex(
                                array(
                                    'pattern' => '/^(?=.*[0-9])/',
                                    'message' => 'Este valor debería tener 1 número o más.',
                                )
                            ),
                        ),
                    )
                );
        } elseif ($currentUser === $data) {
            $builder
                ->add('name')
                ->add('lastName')
                ->add('username')
                ->add('email')
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array(
                        'disabled' => true,
                        'label' => 'Status',
                        'choices' => array('Enabled' => true, 'Disabled' => false),
                    )
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'disabled' => true,
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                );

            if ('PUT' === $builder->getMethod()) {
                $builder
                    ->add(
                        'plain_password',
                        PasswordType::class,
                        array(
                            'constraints' => array(
                                new Length(array('min' => 8, 'max' => 20)),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[a-z])/',
                                        'message' => 'Este valor debería tener 1 letra minuscula o más.',
                                    )
                                ),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[A-Z])/',
                                        'message' => 'Este valor debería tener 1 letra mayuscula o más.',
                                    )
                                ),
                                new Regex(
                                    array(
                                        'pattern' => '/^(?=.*[0-9])/',
                                        'message' => 'Este valor debería tener 1 número o más.',
                                    )
                                ),
                            ),
                        )
                    )
                    ->addEventSubscriber(new UploadFileSubscriber());

            }
        } else {
            $builder
                ->add('name', null, array('disabled' => true))
                ->add('lastName', null, array('disabled' => true))
                ->add('username', null, array('disabled' => true))
                ->add('email', null, array('disabled' => true))
                ->add(
                    'enabled',
                    ChoiceType::class,
                    array('label' => 'Status', 'choices' => array('Enabled' => true, 'Disabled' => false))
                )
                ->add(
                    'rol',
                    ChoiceType::class,
                    array(
                        'mapped' => false,
                        'data' => $rol,
                        'choices' => array('User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'),
                    )
                );
        }

        $builder->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                /** @var User $data */
                $data = $event->getData();

                if ($form->isValid()) {
                    if ('ROLE_ADMIN' === $form['rol']->getData()) {
                        $data->addRole('ROLE_ADMIN');
                    } elseif ('ROLE_USER' === $form['rol']->getData()) {
                        $data->removeRole('ROLE_ADMIN');
                    }
                }
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\User',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_user';
    }
}
