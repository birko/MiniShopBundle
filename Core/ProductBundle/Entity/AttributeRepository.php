<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Query\Expr;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

class AttributeRepository extends SortableRepository
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
        $qb->addOrderBy("n.group");
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

    public function getAllAttributesByProductQueryBuilder($productId, array $groupValues=array())
    {
        return $this->getBySortableGroupsQueryBuilder($groupValues)
                ->andWhere("n.product = :pid")
                ->setParameter('pid', $productId);
    }

    public function getAllAttributesByProductQuery($productId, array $groupValues=array())
    {
        $query = $this->getAllAttributesByProductQueryBuilder($productId, $groupValues)->getQuery();

        return $this->setHint($query);
    }

    public function getAllAttributesByProduct($productId)
    {
        return $this->getAllAttributesByProductQuery($productId)->getResult();
    }

    public function getVisibleAttributesByProductQuery($productId,array $groupValues=array())
    {
        $querybuilder = $this->getAllAttributesByProductQueryBuilder($productId, $groupValues);
        $query = $this->setHint($querybuilder->getQuery());

        return $query;
    }

    public function getVisibleAttributesByProduct($productId, array $groupValues=array())
    {
        return $this->getVisibleAttributesByProductQuery($productId, $groupValues)->getResult();
    }

    public function getAttributesByProductQuery($productId, array $groupValues=array())
    {
        $querybuilder = $this->getAllAttributesByProductQueryBuilder($productId, $groupValues);
        $query = $querybuilder->getQuery();

        return $this->setHint($query);
    }

    public function getAttributesByProduct($productId)
    {
        return $this->getAttributesByProductQuery($productId)->getResult();
    }

    public function getAttribute($productId, $name)
    {
       $querybuilder = $this->getAllAttributesByProductQueryBuilder($productId)
               ->andWhere('n.name = :name')
               ->setParameter('name', $name);

       return $this->setHint($querybuilder->getQuery())->getOneOrNullResult();
    }

    public function getAttributesByProductsQueryBuilder($productIds = array(), $groupValues = array())
    {
        $querybuilder = $this->getBySortableGroupsQueryBuilder($groupValues);
        if (!empty($productIds)) {
            $expr = $querybuilder->expr()->in('n.product', $productIds );
            $querybuilder->andWhere($expr);
        }

        return $querybuilder;
    }

    public function getAttributesByProductsQuery($productIds = array(), $groupValues = array())
    {
        return $this->setHint($this->getAttributesByProductsQueryBuilder($productIds, $groupValues)->getQuery());
    }

    public function getAttributesByProducts($productIds = array(), $groupValues = array())
    {
        return $this->getAttributesByProductsQuery($productIds, $groupValues)->getResult();
    }

    public function getGroupedAttributesByProducts($productIds = array(), $groupValues = array(), $locale = null)
    {
        $query = $this->getAttributesByProductsQueryBuilder($productIds, $groupValues)
            ->select("an.name, av.value, n.group, n.position, p.id as product")
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
