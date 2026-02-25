<?php

namespace App\EventListener;

use App\Entity\DriverCar;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Doctrine\ORM\Events;

class DriverCarPostUpdateSubscriber implements EventSubscriberInterface
{
    public function getSubscribedEvents(): array
    {
        return [Events::postUpdate];
    }

    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof DriverCar) {
            return;
        }

        // Перевіряємо, чи змінено поле timeEnd (актуально для action /driver_cars/set-end)
        $uow = $args->getObjectManager()->getUnitOfWork();
        $changes = $uow->getEntityChangeSet($entity);

        if (!array_key_exists('timeEnd', $changes)) {
            return;
        }

        error_log(sprintf(
            'DriverCar #%d: timeEnd changed to %s',
            $entity->getId() ?? 0,
            $entity->getTimeEnd()?->format(\DateTimeInterface::ATOM) ?? 'null'
        ));
    }
}

