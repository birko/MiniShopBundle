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

    public function getBySortableGroupsQueryBuilder(array $groupValues=array())
    {
        $groups = array_combine(array_values($this->config['groups']), array_keys($this->config['groups']));
        foreach ($groupValues as $name => $value) {
            if (!in_array($name, $this->config['groups'])) {
                throw new \InvalidArgumentException('Sortable group "'.$name.'" is not defined in Entity '.$this->meta->name);
            }
            unset($groups[$name]);
        }

        $qb = $this->createQueryBuilder('n')
            ->select("n, an, av");
        $qb->leftJoin("n.name", "an");
        $qb->leftJoin("n.value", "av");
        $qb->addOrderBy('n.'.$this->config['position']);
        $i = 1;
        foreach ($groupValues as $group => $value) {
            if (null === $value) {
                 $qb->andWhere('n.'.$group.' is null');
            } else {
                $qb->andWhere('n.'.$group.' = :group'.$i)
                ->setParameter('group'.$i, $value);
            }
            $i++;
        }

        return $qb;
    }

    public function getBySortableGroupsQuery(array $groupValues=array())
    {
        return $this->setHint($this->getBySortableGroupsQueryBuilder($groupValues))->getQuery();
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

    public function getOptionsNamesByProduct($prouctId, $onlyOnStock = true)
    {
        $querybuilder = $this->getOptionsByProductQueryBuilder($prouctId)
            ->select("an.name")
            ->distinct()
            ->resetDQLPart("orderBy");
        if ($onlyOnStock) {
            $querybuilder->andWhere($querybuilder->expr()->orX(
                $querybuilder->expr()->isNull("po.amount"),
                $querybuilder->expr()->gt("po.amount", 0)
            ));
        }
        $arr = $this->setHint($querybuilder->getQuery())->getResult();
        $result = array();
        foreach ($arr as $value) {
            $result[] = $value['name'];
        }

        return $result;
    }
    
    public function getOptionsByProductsQueryBuilder($productIds = array(), $groupValues = array())
    {
        $querybuilder = $this->getBySortableGroupsQueryBuilder($groupValues);
        if (!empty($productIds)) {
            $expr = $querybuilder->expr()->in('n.product', ":productIds");
            $querybuilder->andWhere($expr);
            $querybuilder->setParameter("productIds", $productIds);
        }

        return $querybuilder;
    }
    
    public function getGroupedOptionsByProducts($productIds = array(), $groupValues = array(), $locale = null)
    {
        $query = $this->getOptionsByProductsQueryBuilder($productIds, $groupValues)
            ->select("an.name, av.value, n.position, p.id as product")
            ->leftJoin("n.product", 'p')
            ->getQuery();
        $result = array();
        $query = $this->setHint($query);
        if ($locale) {
            $query->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                $locale // take locale from session or request etc.
            );
        }
        $iterator = $query->iterate(array(), \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
        foreach ($iterator as $key => $row) {
           $entity = $row[$key];
           $result[$entity['product']][$entity['name']][] = $entity;
        }

        return $result;
    }
}
