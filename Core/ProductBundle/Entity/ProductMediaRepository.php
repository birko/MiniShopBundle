<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ProductMediaRepository extends EntityRepository
{
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
}
