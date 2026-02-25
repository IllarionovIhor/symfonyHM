<?php

namespace App\EventListener;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

class RidePostUpdateSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [Events::postUpdate];
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Ride) {
            return;
        }

        // Перевіряємо, чи змінено поле status (актуально для action /rides/complete)
        $uow = $args->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        if (!array_key_exists('status', $changes)) {
            return;
        }

        // Реагуємо тільки коли статус став "completed"
        if ($entity->getStatus() !== 'completed') {
            return;
        }

        error_log(sprintf(
            'Ride #%d marked as completed',
            $entity->getId() ?? 0
        ));
    }
}

