<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OrderRepository extends EntityRepository
{
    public function getOrdersByIdQueryBuilder($orderIds)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $expr = $queryBuilder->expr()->in('o.id', ":orderIds");
        $queryBuilder = $queryBuilder->select('o')
                ->from('CoreShopBundle:Order', 'o')
                ->leftJoin("o.items", 'oi')
                ->leftJoin("o.shipping", 's')
                ->leftJoin("o.shipping_status", 'ss')
                ->leftJoin("o.order_status", 'os')
                ->leftJoin("o.payment", 'p')
                ->leftJoin("o.delivery_state", 'ds')
                ->leftJoin("o.invoice_state", 'is')
                ->andWhere($expr)
                ->setParameter("orderIds", $orderIds);
        
        return $queryBuilder ;
    }

    public function getOrdersByIdQuery($orderIds)
    {
        return  $this->getOrdersByIdQueryBuilder($orderIds)->getQuery();
    }

    public function updateOrderStatus($orderIds, $statusId)
    {
        if (!empty($orderIds)) {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();
            $expr = $queryBuilder->expr()->in('o.id', ":orderIds");
            $queryBuilder
                ->update('CoreShopBundle:Order', 'o')
                ->set("o.order_status", ":order_status")
                ->andWhere($expr)
                ->setParameter('order_status', $statusId)
                ->setParameter("orderIds", $orderIds);
                ;
            $numUpdated = $queryBuilder->getQuery()->execute();

            return $numUpdated;
        }

        return 0;
    }

    public function updateShippingStatus($orderIds, $statusId)
    {
        if (!empty($orderIds)) {
            $queryBuilder = $this->getEntityManager()->createQueryBuilder();
            $expr = $queryBuilder->expr()->in('o.id', ":orderIds");
            $queryBuilder
                ->update('CoreShopBundle:Order', 'o')
                ->set("o.shipping_status", ":shipping_status")
                ->andWhere($expr)
                ->setParameter('shipping_status', $statusId)
                ->setParameter("orderIds", $orderIds);
                ;
            $numUpdated = $queryBuilder->getQuery()->execute();

            return $numUpdated;
        }

        return 0;
    }

    public function filterOrderQuieryBuilder(QueryBuilder $queryBuilder, OrderFilter $filter = null)
    {
        if (!empty($filter)) {
            $words = $filter->getWordsArray();
            if (!empty($words)) {
                $i = 0;
                foreach ($words as $word) {
                    $where = $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("lower(o.order_number)",     ':word01i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_number)",   ':word02i'.$i),
                        $queryBuilder->expr()->like("lower(o.variable_number)",  ':word03i'.$i),
                        $queryBuilder->expr()->like("lower(o.tracking_id)",      ':word04i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_name)",    ':word05i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_surname)", ':word06i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_company)", ':word07i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_street)",  ':word08i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_city)",    ':word09i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_zip)",     ':word10i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_email)",   ':word11i'.$i),
                        $queryBuilder->expr()->like("lower(o.delivery_phone)",   ':word12i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_name)",     ':word13i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_surname)",  ':word14i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_company)",  ':word15i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_street)",   ':word16i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_city)",     ':word17i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_zip)",      ':word18i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_email)",    ':word19i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_phone)",    ':word20i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_TIN)",      ':word21i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_OIN)",      ':word22i'.$i),
                        $queryBuilder->expr()->like("lower(o.invoice_VATIN)",    ':word23i'.$i)
                    );
                    $queryBuilder->andWhere($where);
                    $queryBuilder->setParameter('word01i'.$i, strtolower($word));
                    $queryBuilder->setParameter('word02i'.$i, strtolower($word));
                    $queryBuilder->setParameter('word03i'.$i, strtolower($word));
                    $queryBuilder->setParameter('word04i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word05i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word06i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word07i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word08i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word09i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word10i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word11i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word12i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word13i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word14i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word15i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word16i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word17i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word18i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word19i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word20i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word21i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word22i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word23i'.$i, '%' . strtolower($word) . '%');
                    $i ++;
                }
            }
            if ($filter->getOrderStatus() != null) {
                $queryBuilder->andWhere('o.order_status =:ostatus')
                        ->setParameter('ostatus', $filter->getOrderStatus()->getId());
            }
            if ($filter->getShippingStatus() != null) {
                $queryBuilder->andWhere('o.shipping_status =:sstatus')
                        ->setParameter('sstatus', $filter->getShippingStatus()->getId());
            }
            if ($filter->getShippingState() != null) {
                $queryBuilder->andWhere('o.delivery_state =:sstate')
                        ->setParameter('sstate', $filter->getShippingState()->getId());
            }
            
            $itemWords = $filter->getItemWordsArray();
            if (!empty($itemWords)) {
                $queryBuilder2 = $this->getEntityManager()->createQueryBuilder()
                 ->select("foi.id")
                 ->from('CoreShopBundle:OrderItem', 'foi')
                 ->andWhere("foi.order = o.id");
                $queryBuilder2->andWhere($queryBuilder2->expr()->andX(
                    $queryBuilder2->expr()->isNull("foi.payment"),
                    $queryBuilder2->expr()->isNull("foi.shipping")
                ));
                $i = 0;
                foreach ($itemWords as $word) {
                    $itemswhere = $queryBuilder2->expr()->orX(
                        $queryBuilder2->expr()->like("lower(foi.name)",          ':itemsword01i'.$i),
                        $queryBuilder2->expr()->like("lower(foi.description)",   ':itemsword02i'.$i)  
                    );
                    $queryBuilder2->andWhere($itemswhere);
                    $queryBuilder->setParameter('itemsword01i'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('itemsword02i'.$i, '%' . strtolower($word) . '%');
                    $i ++;
                }
                $queryBuilder->andWhere($queryBuilder->expr()->exists($queryBuilder2));
            }
            
        }

        return $queryBuilder;
    }
}
