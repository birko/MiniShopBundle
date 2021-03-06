<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ShippingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ShippingRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getShippingQueryBuilder($state = null, $enabled = false)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select("s, c")
            ->from("CoreShopBundle:Shipping", "s")
            ->leftJoin("s.currency", "c")
            ->orderBy('s.name', 'ASC');
        if ($state !== null) {
            $queryBuilder->andWhere('s.state =:state')
            ->setParameter('state', $state);
        }
        if ($enabled) {
            $queryBuilder->andWhere('s.enabled =:enabled')
            ->setParameter('enabled', $enabled);
        }

        return $queryBuilder;
    }
}
