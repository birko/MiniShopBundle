<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductAmountRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getStockQueryBuilder($entities = null)
    {
        $querybuilder = $this->createQueryBuilder('s')
            ->select("s");
        if ($entities != null) {
            $expr = $querybuilder->expr()->in("s.product", ":productIds");
            $querybuilder->andWhere($expr);
            $querybuilder->setParameter("productIds", $entities);            
        }

        return $querybuilder;
    }

    public function getStockQuery($entities = null)
    {
        return $query->setHint($this->getStockQueryBuilder($entities)->getQuery());
    }
    public function getStock($entities = null)
    {
        return $this->getStockQuery($entities)->getResult();
    }

    public function getStocksArray($entities = null)
    {
        $query = $this->getStockQueryBuilder($entities)
        ->select("s, p")
        ->leftJoin("s.product", "p")
        ->getQuery();
        $query = $this->setHint($query);
        
        $result = array();
        foreach ($query->getResult() as $entity) {
            $result[$entity->getProduct()->getId()][] = $entity;
        }

        return $result;
    }
}
