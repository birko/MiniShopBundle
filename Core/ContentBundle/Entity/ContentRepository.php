<?php

namespace Core\ContentBundle\Entity;

use Doctrine\ORM\EntityRepository;
/**
 * CategoryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ContentRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getBySlug($slug)
    {
        $query = $this->findContentByCategoryQueryBuilder()
            ->andWhere("c.slug = :slug")
            ->setParameter("slug", $slug)
            ->getQuery();

        return $this->setHint($query)->getOneOrNullResult();
    }

    public function findContentByCategoryQueryBuilder($categoryID = null)
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("c")
                ->from("CoreContentBundle:Content", "c");
        if ($categoryID !== null) {
            $querybuilder->andWhere('c.category = :cid')
                    ->setParameter('cid', $categoryID);
        }

        return $querybuilder;
    }

    public function findMediaByContentQueryBuilder($content)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
               ->select("m")
               ->from("CoreContentBundle:Content", "c")
               ->from("CoreMediaBundle:Media", "m")
               ->leftJoin("c.media", "cm")
               ->where("c.id = :content")
               ->andWhere("cm.media = m")
               ->setParameter('content', $content)
               ->addOrderBy("cm.position", "asc")
               ->addOrderBy("m.id", "asc");

        return $queryBuilder;
    }

    public function findMediaByContent($content)
    {
        return $this->setHint($this->findMediaByContentQueryBuilder($content)->getQuery())->getResult();
    }
}
