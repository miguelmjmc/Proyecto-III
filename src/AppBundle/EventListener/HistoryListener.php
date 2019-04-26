<?php

namespace AppBundle\EventListener;

use AppBundle\Entity\OperationHistory;
use AppBundle\Entity\User;
use AppBundle\Utils\HistoryResolver;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class HistoryListener implements EventSubscriber
{
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::onFlush => 'onFlush',
        );
    }

    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        if ($this->tokenStorage->getToken()) {
            foreach ($uow->getScheduledEntityInsertions() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(1);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }

            foreach ($uow->getScheduledEntityUpdates() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    if ($entity instanceof User) {
                        $uow->getEntityChangeSet($entity);

                        $changes = $uow->getEntityChangeSet($entity);

                        if (array_key_exists('lastLogin', $changes) || array_key_exists('confirmationToken', $changes) || array_key_exists('passwordRequestedAt', $changes)) {
                            continue;
                        }
                    }

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(2);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }

            foreach ($uow->getScheduledEntityDeletions() as $keyEntity => $entity) {
                if (HistoryResolver::encodeTargetEntity(get_class($entity))) {
                    $history = new OperationHistory();

                    $history->setDate(new \DateTime())
                        ->setUser($this->tokenStorage->getToken()->getUser())
                        ->setTargetEntity(HistoryResolver::encodeTargetEntity(get_class($entity)))
                        ->setOperationType(3);

                    $em->persist($history);
                    $classMetadata = $em->getClassMetadata(OperationHistory::class);
                    $uow->computeChangeSet($classMetadata, $history);
                }
            }
        }
    }
}
