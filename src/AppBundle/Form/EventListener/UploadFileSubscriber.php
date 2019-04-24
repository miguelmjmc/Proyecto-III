<?php

namespace AppBundle\Form\EventListener;


use AppBundle\Entity\CompanyProfile;
use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadFileSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $form->add(
            'file',
            FileType::class,
            array(
                'mapped' => false,
                'constraints' => array(
                    new File(array('maxSize' => '2048k', 'mimeTypes' => array('image/jpeg', 'image/png'))),
                ),
            )
        );
    }

    public function onPostSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if ($data instanceof CompanyProfile) {
            if ($form->isValid() && $form['file']->getData()) {
                /** @var UploadedFile $file */
                $file = $form['file']->getData();

                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $file->move('img', $fileName);

                if (file_exists($data->getLogo())) {
                    unlink($data->getLogo());
                }

                $data->setLogo('img/'.$fileName);
            }
        } elseif ($data instanceof User) {
            if ($form->isValid() && $form['file']->getData()) {
                /** @var UploadedFile $file */
                $file = $form['file']->getData();

                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                $file->move('img', $fileName);

                if (file_exists($data->getImg())) {
                    unlink($data->getImg());
                }

                $data->setImg('img/'.$fileName);
            }
        }
    }
}
