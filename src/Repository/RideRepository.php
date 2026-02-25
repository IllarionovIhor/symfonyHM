<?php

namespace App\Repository;

use App\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Ride>
 */
class RideRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ride::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllRidesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('ride');
        if (isset($data['destination'])) {
            $qb->andWhere('ride.destination LIKE :destination')
                ->setParameter('destination', '%' . $data['destination'] . '%');
        }
        if (isset($data['from'])) {
            $qb->andWhere('ride.from LIKE :from')
                ->setParameter('from', '%' . $data['from'] . '%');
        }
        if (isset($data['status'])) {
            $qb->andWhere('ride.status = :status')
                ->setParameter('status', $data['status']);
        }
        if (isset($data['car_id'])) {
            $qb->andWhere('ride.car = :car')
                ->setParameter('car', $data['car_id']);
        }
        if (isset($data['driver_id'])) {
            $qb->andWhere('ride.driver = :driver')
                ->setParameter('driver', $data['driver_id']);
        }
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);
        return [
            'rides' => $qb->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
