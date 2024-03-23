<?php


namespace App\Repository;

use App\Entity\Shop;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ShopRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shop::class);
    }

    public function findInventoriesByShopId($shopId)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.inventories', 'i')
            ->addSelect('i')
            ->where('s.id = :shopId')
            ->setParameter('shopId', $shopId)
            ->getQuery()
            ->getResult();
    }

    public function countSKUsForShop(int $shopId): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        
        $qb->select('COUNT(pv.sku)')
            ->from('App\Entity\Shop', 'shop')
            ->leftJoin('shop.inventories', 'inventory')
            ->leftJoin('inventory.products', 'product')
            ->leftJoin('product.variations', 'pv')
            ->where('shop.id = :shopId')
            ->setParameter('shopId', $shopId);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
