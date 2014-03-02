<?php

namespace Core\NewsletterBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Core\CommonBundle\Entity\Filter;

/**
 * NewlsetterEmailRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NewsletterEmailRepository extends EntityRepository
{
    public function getEmailsQueryBuilder()
    {
        $querybuilder = $this->getEntityManager()->createQueryBuilder()
                ->select("ne")
                ->from("CoreNewsletterBundle:NewsletterEmail", "ne")
                ->orderBy("ne.email", "asc");

        return $querybuilder;
    }

    public function getEnabledEmailsQueryBuilder()
    {
        $querybuilder = $this->getEmailsQueryBuilder()
                ->andWhere("ne.enabled = :enabled")
                ->setParameter("enabled", true);

        return $querybuilder;
    }

    public function getEmailsInGroupsQueryBuilder($groups = array(), $not = false)
    {
        $querybuilder = $this->getEmailsQueryBuilder();

        if ($not) {
            $expr = $querybuilder->expr()->notIn("neg", $groups);
        } else {
            $expr = $querybuilder->expr()->in("neg", $groups);
        }
        $querybuilder = $querybuilder->distinct()
                ->leftJoin("ne.groups", "neg")
                //->leftJoin("neg", "ng")
                ->andWhere($expr);

        return $querybuilder;
    }

    public function getEnabledEmailsInGroupsQueryBuilder($groups = array(), $not = false)
    {
        $querybuilder = $this->getEmailsInGroupsQueryBuilder($groups, $not)
                ->andWhere("ne.enabled = :enabled")
                ->setParameter("enabled", true);

        return $querybuilder;
    }

    public function getEmailsQuery()
    {
        return $this->getEmailsQueryBuilder()->getQuery();
    }

    public function getEnabledEmailsQuery()
    {
        return $this->getEnabledEmailsQueryBuilder()->getQuery();
    }

    public function getEmailsInGroupsQuery($groups = array(), $not = false)
    {
        return $this->getEmailsInGroupsQueryBuilder($groups, $not)->getQuery();
    }

    public function getEnabledEmailsInGroupsQuery($groups = array(), $not = false)
    {
        return $this->getEnabledEmailsInGroupsQueryBuilder($groups, $not)->getQuery();
    }

    public function getEmails()
    {
        return $this->getEmailsQuery()->getResult();
    }

    public function getEnabledEmails()
    {
        return $this->getEnabledEmailsQuery()->getResult();
    }

    public function getEmailsInGroups($groups = array(), $not = false)
    {
        return $this->getEmailsInGroupsQuery($groups, $not)->getResult();
    }

    public function getEnabledEmailsInGroups($groups = array(), $not = false)
    {
        return $this->getEnabledEmailsInGroupsQuery($groups, $not)->getResult();
    }

    public function filterQueryBuilder($queryBuilder, Filter $filter = null)
    {
        if ($filter) {
            $words = $filter->getWordsArray();
            if (!empty($words)) {
                $i = 0;
                foreach ($words as $word) {
                    $where = $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("lower(ne.email)", ':word1'.$i)
                    );
                    $queryBuilder->andWhere($where);
                    $queryBuilder->setParameter('word1'.$i, '%' . strtolower($word) . '%');
                    $i ++;
                }
            }
        }

        return $queryBuilder;
    }

    public function getEmail($email)
    {
        $qb = $this->getEmailsQueryBuilder()
            ->select("ne, ng")
            ->leftJoin("ne.groups", "ng");
        $qb->andWhere($qb->expr()->like("lower(ne.email)", ':email'))
            ->setParameter('email', strtolower($email));

        return $qb->getQuery()->getOneOrNullResult();
    }
}