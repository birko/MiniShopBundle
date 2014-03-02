<?php

namespace Core\ProductBundle\Entity;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

/**
 * ProductOptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductOptionRepository extends SortableRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getOptionsByProductQueryBuilder($productId)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("po, an, av")
                ->from("CoreProductBundle:ProductOption", "po")
                ->leftJoin("po.name", "an")
                ->leftJoin("po.value", "av")
                ->andWhere("po.product = :pid")
                ->setParameter('pid', $productId)
                ->addOrderBy('an.name')
                ->addOrderBy('po.position')
                ->addOrderBy('av.value');

        return $queryBuilder;
    }

    public function getOptionsByProductQuery($productId)
    {
        return $this->setHint($this->getOptionsByProductQueryBuilder($productId)->getQuery());
    }

    public function getOptionsNamesByProduct($prouctId)
    {
        $querybuilder = $this->getOptionsByProductQueryBuilder($prouctId)
            ->select("an.name")
            ->distinct()
            ->resetDQLPart("orderBy");
        $arr = $this->setHint($querybuilder->getQuery())->getResult();
        $result = array();
        foreach ($arr as $value) {
            $result[] = $value['name'];
        }

        return $result;
    }
}