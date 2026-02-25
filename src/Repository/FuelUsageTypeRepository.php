<?php

namespace App\Repository;

use App\Entity\FuelUsageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<FuelUsageType>
 */
class FuelUsageTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FuelUsageType::class);
    }

    /**
     * @param array $filters
     * @param int $page
     * @param int $limit
     * @return array{data: array, total: int, page: int, limit: int}
     */
    public function getAllFuelUsageTypesByFilter(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $qb = $this->createQueryBuilder('f');

        // Example filter: name
        if (!empty($filters['name'])) {
            $qb->andWhere('f.name LIKE :name')
                ->setParameter('name', '%' . $filters['name'] . '%');
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
