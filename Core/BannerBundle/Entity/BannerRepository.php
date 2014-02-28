<?php

namespace Core\BannerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * BannerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BannerRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getBannersQueryBuilder($categoryId = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("b, m")
                ->from("CoreBannerBundle:Banner", "b")
                ->leftJoin("b.media", "m")
                ->addOrderBy("b.position")
                ->addOrderBy("b.id");
        if ($categoryId !== null) {
            $queryBuilder->andWhere('b.category = :cid')
                    ->setParameter('cid', $categoryId);
        }

        return $queryBuilder;
    }

    public function getBannersQuery($categoryId = null)
    {
        return $this->setHint($this->getBannersQueryBuilder($categoryId)->getQuery());
    }

    public function getBanners($categoryId = null)
    {
        return $this->getBannersQuery($categoryId)->getResult();
    }

    public function updatePosition($bannerId, $categoryId, $position, $move)
    {
        $q = $this->getEntityManager()->createQueryBuilder();
        $q->update("CoreBannerBundle:Banner", "b")
                ->set("b.position", $q->expr()->sum("b.position", ":move"))
                ->where("b <> :id")
                ->andWhere("b.position = :position")
                ->andWhere("b.category = :cid")
                ->setParameter('move', $move)
                ->setParameter('id', $bannerId)
                ->setParameter('position', $position)
                ->setParameter('cid', $categoryId)
                ;
        $numUpdated = $q->getQuery()->execute();

        return $numUpdated;
    }
}
