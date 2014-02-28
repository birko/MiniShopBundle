<?php
/**
 *
 *
 * @author birko
 */
namespace Core\MediaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MediaRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getMediaQueryBuilder()
    {
        return $this->createQueryBuilder("m")
            ->addOrderBy("m.id", "asc");
    }
}
