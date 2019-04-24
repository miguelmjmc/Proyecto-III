<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\AccessHistory;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginListener implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            SecurityEvents::INTERACTIVE_LOGIN => 'onLoginSuccess',
        );
    }

    public function onLoginSuccess(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();
        /** @var User $user */
        $user = $event->getAuthenticationToken()->getUser();



        $history = new AccessHistory();
        $history->setDate(new \DateTime())
            ->setIp($request->getClientIp())
            ->setUser($user);

        $this->entityManager->persist($history);

        $this->entityManager->flush();
    }
}
