<?php

namespace App\Repository;

use App\Entity\Car;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Car>
 */
class CarRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Car::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array
     */
    public function getAllCarsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('car');
        if (isset($data['name'])) {
            $qb->andWhere('car.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }
        if (isset($data['plateNumber'])) {
            $qb->andWhere('car.plateNumber LIKE :plateNumber')
                ->setParameter('plateNumber', '%' . $data['plateNumber'] . '%');
        }
        if (isset($data['fuelUsageType_id'])) {
            $qb->andWhere('car.fuelUsageType = :fuelUsageType')
                ->setParameter('fuelUsageType', $data['fuelUsageType_id']);
        }
        if (isset($data['tier_id'])) {
            $qb->andWhere('car.tier = :tier')
                ->setParameter('tier', $data['tier_id']);
        }
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $pagesCount = ceil($totalItems / $itemsPerPage);
        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);
        return [
            'cars' => $qb->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems
        ];
    }
}
