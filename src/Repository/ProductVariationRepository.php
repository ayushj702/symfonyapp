<?php


namespace App\Repository;

use App\Entity\ProductVariation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductVariationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductVariation::class);
    }

    public function countSKUsByShop($shopId): int
    {
        return $this->createQueryBuilder('pv')
            ->join('pv.product', 'p')
            ->join('p.inventory', 'i')
            ->join('i.shop', 's')
            ->andWhere('s.id = :shopId')
            ->setParameter('shopId', $shopId)
            ->select('COUNT(pv.id) as skuCount')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
