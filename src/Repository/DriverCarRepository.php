<?php

namespace App\Repository;

use App\Entity\DriverCar;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<DriverCar>
 */
class DriverCarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DriverCar::class);
    }

    /**
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return array{data: array, total: int, page: int, limit: int}
     */
    public function getAllDriverCarsByFilter(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('dc');

        // Example filter: driverId, carId
        if (!empty($filters['driverId'])) {
            $qb->andWhere('dc.driver = :driverId')
                ->setParameter('driverId', $filters['driverId']);
        }
        if (!empty($filters['carId'])) {
            $qb->andWhere('dc.car = :carId')
                ->setParameter('carId', $filters['carId']);
        }

        $qb->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $paginator = new Paginator($qb);
        $data = iterator_to_array($paginator);
        $total = count($paginator);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
        ];
    }
}
