<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductMediaRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }
    
    public function removeProductMedia($mediaId = null, $productId = null)
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
               ->delete("CoreProductBundle:ProductMedia", "pm");
        if ($mediaId !== null) {
            $querybuilder =  $querybuilder->andWhere("pm.media = :mid")
            ->setParameter('mid', $mediaId);
        }

        if ($productId !== null) {
            $querybuilder =  $querybuilder->andWhere("pm.product = :pid")
            ->setParameter('pid', $productId);
        }
       $numUpdated = $querybuilder->getQuery()->execute();

       return $numUpdated;
    }

    public function updateDefaultCategory($productId, $exludeMediaId)
    {
        $q = $this->getEntityManager()->createQueryBuilder()
                ->update("CoreProductBundle:ProductMedia", "pm")
                ->set("pm.default", '0')
                ->where("pm.media <> :mid")
                ->andWhere("pm.product = :pid")
                ->setParameter('mid', $exludeMediaId)
                ->setParameter('pid', $productId)
                ->getQuery();
        $numUpdated = $q->execute();

        return $numUpdated;
    }

    public function updatePosition($productId, $mediaId, $position, $move)
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->update("CoreProductBundle:ProductMedia", "pm")
                ->set("pm.position", $q->expr()->sum("pm.position", ":move"))
                ->where("pm.media <> :mid")
                ->andWhere("pm.position = :position")
                ->andWhere("pm.product = :pid")
                ->setParameter('move', $move)
                ->setParameter('mid', $mediaId)
                ->setParameter('position', $position)
                ->setParameter('pid', $productId)
                ;
        $numUpdated = $q->getQuery()->execute();

        return $numUpdated;
    }

   public function getProductsMediasQueryBuilder($entities = null)
   {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
            ->select("m, pm")
            ->from("CoreProductBundle:ProductMedia", "pm")
            ->leftJoin("pm.media", "m")
            ->leftJoin("pm.product", "p")
            ->addOrderBy("pm.position", "asc")
            ->addOrderBy("m.id", "asc")
        ;
        if ($entities != null) {
            $expr = $querybuilder->expr()->in("pm.product", $entities);
            $querybuilder->andWhere($expr);
        }

       return $querybuilder;
   }

    public function getProductsMediasQuery($entities = null)
    {
        return $this->setHint($this->getProductsMediasQueryBuilder($entities)->getQuery());
    }

    public function getProductsMediasArray($entities = null)
    {
        $query = $this->getProductsMediasQuery($entities);
        $result = array();
        
        foreach ($query->getResult() as $entity) {
            $result[$entity->getProduct()->getId()][] = $entity->getMedia();
        }

        return $result;
    }
}
