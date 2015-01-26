<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductCategoryRepository extends EntityRepository
{
    public function removeProductCategory($categoryId = null, $productId = null)
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
               ->delete("CoreProductBundle:ProductCategory", "pc");
        if ($categoryId !== null) {
            $querybuilder =  $querybuilder->andWhere("pc.category = :cid")
            ->setParameter('cid', $categoryId);
        }

        if ($productId !== null) {
            $querybuilder =  $querybuilder->andWhere("pc.product = :pid")
            ->setParameter('pid', $productId);
        }
       $numUpdated = $querybuilder->getQuery()->execute();

       return $numUpdated;
    }

    public function updateDefaultCategory($productId, $exludeCategoryId)
    {
        $q = $this->getEntityManager()->createQueryBuilder()
                ->update("CoreProductBundle:ProductCategory", "pc")
                ->set("pc.default", '0')
                ->where("pc.category <> :cid")
                ->andWhere("pc.product = :pid")
                ->setParameter('cid', $exludeCategoryId)
                ->setParameter('pid', $productId)
                ->getQuery();
        $numUpdated = $q->execute();

        return $numUpdated;
    }

    public function updatePosition($productId, $categoryId, $position, $move)
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->update("CoreProductBundle:ProductCategory", "pc")
                ->set("pc.position", $q->expr()->sum("pc.position", ":move"))
                ->where("pc.category = :cid")
                ->andWhere("pc.position = :position")
                ->andWhere("pc.product <> :pid")
                ->setParameter('move', $move)
                ->setParameter('cid', $categoryId)
                ->setParameter('position', $position)
                ->setParameter('pid', $productId)
                ;
        $numUpdated = $q->getQuery()->execute();

        return $numUpdated;
    }
    
    public function getCategoriesArray($entities = null)
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
            ->select("pc, c, p")
            ->from("CoreProductBundle:ProductCategory", "pc")
            ->leftJoin("pc.product", "p")
            ->leftJoin("pc.category", "c");
        if ($entities != null) {
            $expr = $querybuilder->expr()->in("pc.product", ":productIds");
            $querybuilder->andWhere($expr);
            $querybuilder->setParameter("productIds", $entities);            
        }
        $querybuilder->addOrderBy("pc.position", 'asc');
        $query = $querybuilder->getQuery();
        
        $result = array();
        foreach ($query->getResult() as $entity) {
            if ($entity->getProduct()) {
                if (!isset($result[$entity->getProduct()->getId()])) {
                    $result[$entity->getProduct()->getId()] = new Product();
                }
                $result[$entity->getProduct()->getId()]->getProductCategories()->add($entity);
            }
        }

        return $result;
    }
}
