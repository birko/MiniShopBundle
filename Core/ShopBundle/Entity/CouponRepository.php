<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CouponRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CouponRepository extends EntityRepository
{
    public function getCouponsProductsQueryBuilder($coupon)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("p2")
                ->from("CoreShopBundle:Coupon", "c")
                ->join("c.products", "p2")
                ->where("c = :cid")
                ->setParameter('cid', $coupon);

        return $queryBuilder;
    }

    public function getNotCouponsProductsQueryBuilder($coupon)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("p")
                ->distinct()
                ->from("CoreProductBundle:Product", "p");
        $qb2 = $this->getCouponsProductsQueryBuilder($coupon)
                ->andWhere('p = p2');
        $expr = $queryBuilder->expr()->exists($qb2);
        $expr2 = $queryBuilder->expr()->not($expr);
        $queryBuilder->andWhere($expr2)
                ->setParameter('cid', $coupon);

        return $queryBuilder;
    }

    public function getNotCouponsProductsQuery($coupon)
    {
        return $this->getNotCouponsProductsQueryBuilder($coupon)->getQuery();
    }
}
