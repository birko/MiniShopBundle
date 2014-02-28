<?php

namespace Core\AttributeBundle\Entity;

use Doctrine\ORM\EntityRepository;

class AttributeValueRepository extends EntityRepository
{
    public function getValuesByNameQueryBuilder($name = null, $all = false, $value = null)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select("av, an")
            ->from("CoreAttributeBundle:AttributeValue", "av")
            ->leftJoin("av.name", "an");
            ;
        if ($name != null) {
            $queryBuilder->andWhere("av.name = :name")
                ->setParameter("name", $name);
        }

        if (!$all) {
            $queryBuilder->andWhere("av.serialized = :serialized")
                ->setParameter("serialized", false);
        }

        if ($value !== null) {
            $queryBuilder->andWhere("av.value = :value")
                ->setParameter("value", $value);
        }
        $queryBuilder
            ->addOrderBy("an.name");

        return $queryBuilder;
    }

    public function getValuesByNameQuery($name = null, $all = false, $value = null)
    {
        $query = $this->getValuesByNameQueryBuilder($name, $all, $value)->getQuery();
        $query = $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');

        return $query;
    }

    public function getValuesByName($name = null, $all = false, $value = null)
    {
        return $this->getValuesByNameQuery($name, $all, $value)->getResult();
    }

    public function createAttributeValue(AttributeName $name, $value)
    {

        $attributeValue = ($name->getId()) ? $this->getValuesByNameQuery($name->getId(), true, $value)->getOneOrNullResult() : null;
        if (!$attributeValue) {
            $attributeValue = new AttributeValue();
            $attributeValue->setName($name);
            $attributeValue->setValue($value);
        }

        return $attributeValue;
    }
}
