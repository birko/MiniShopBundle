<?php

namespace Core\AttributeBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AttributeNameRepository extends EntityRepository
{
    public function getNamesQueryBuilder($name = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select("an, av")
            ->from("CoreAttributeBundle:AttributeName", "an")
            ->leftJoin("an.values", "av");
            ;
        if ($name !== null) {
            $queryBuilder->andWhere("an.name = :name")
                ->setParameter("name", $name);
        }
        $queryBuilder
            ->addOrderBy("an.name");

        return $queryBuilder;
    }

    public function getNamesQuery($name = null)
    {
        $query = $this->getNamesQueryBuilder($name)->getQuery();
        $query = $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

        return $query;
    }

    public function getNames($name = null)
    {
        return $this->getNamesQuery($name)->getResult();
    }

    public function createAttributeName($name)
    {
        $attributeName = $this->getNamesQuery($name)->getOneOrNullResult();
        if (!$attributeName) {
            $attributeName = new AttributeName();
            $attributeName->setName($name);
        }

        return $attributeName;
    }
}
