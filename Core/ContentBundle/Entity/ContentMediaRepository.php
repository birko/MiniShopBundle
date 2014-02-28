<?php

namespace Core\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ContentMediaRepository extends EntityRepository
{
    public function removeProductCategory($mediaId = null, $contentId = null)
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
               ->delete("CoreContentBundle:ContentMedia", "cm");
        if ($mediaId !== null) {
            $querybuilder =  $querybuilder->andWhere("cm.media = :mid")
            ->setParameter('mid', $mediaId);
        }

        if ($contentId !== null) {
            $querybuilder =  $querybuilder->andWhere("cm.content = :cid")
            ->setParameter('cid', $contentId);
        }
       $numUpdated = $querybuilder->getQuery()->execute();

       return $numUpdated;
    }

    public function updateDefaultCategory($contentId, $exludeMediaId)
    {
        $q = $this->getEntityManager()->createQueryBuilder()
                ->update("CoreContentBundle:ContentMedia", "cm")
                ->set("cm.default", '0')
                ->where("cm.media <> :mid")
                ->andWhere("cm.content = :cid")
                ->setParameter('mid', $exludeMediaId)
                ->setParameter('cid', $contentId)
                ->getQuery();
        $numUpdated = $q->execute();

        return $numUpdated;
    }

    public function updatePosition($contentId, $mediaId, $position, $move)
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->update("CoreContentBundle:ContentMedia", "cm")
                ->set("cm.position", $q->expr()->sum("cm.position", ":move"))
                ->where("cm.media <> :mid")
                ->andWhere("cm.position = :position")
                ->andWhere("cm.content = :cid")
                ->setParameter('move', $move)
                ->setParameter('mid', $mediaId)
                ->setParameter('position', $position)
                ->setParameter('cid', $contentId)
                ;
        $numUpdated = $q->getQuery()->execute();

        return $numUpdated;
    }
}
