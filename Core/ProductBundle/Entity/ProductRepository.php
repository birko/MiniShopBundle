<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProductRepository extends EntityRepository
{
    public function setHint(\Doctrine\ORM\Query $query)
    {
        return $query->setHint(\Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER, 'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker');
    }

    public function getBySlug($slug)
    {
        $query = $this->findByCategoryQueryBuilder()
            ->andWhere("p.slug = :slug")
            ->setParameter("slug", $slug)
            ->getQuery();

        return $this->setHint($query)->getOneOrNullResult();
    }

    public function getProduct($id)
    {
        $query = $this->findByCategoryQueryBuilder()
            ->andWhere("p.id = :id")
            ->setParameter("id", $id)
            ->getQuery();

        return $this->setHint($query)->getOneOrNullResult();
    }

    public function findByCategoryQueryBuilder($category = null, $recursive = false, $onlyenabled = false, $join = true, $joindetail = true)
    {
        $select = "p, ps";
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->from("CoreProductBundle:Product", "p")
            ->leftJoin("p.productCategories", "pc")
            ->leftJoin("pc.category", "c")
            ->leftJoin("p.stock", "ps")
        ;
                
        if ($join) {
            $select .= ", pc, c, pp, v, pg, cur, va";
            $queryBuilder
                ->leftJoin("p.prices", "pp")
                ->leftJoin("p.vendor", "v")
                ->leftJoin("pp.priceGroup", "pg")
                ->leftJoin("pp.currency", "cur")
                ->leftJoin("pp.vat", "va")
            ;
        }

        if ($joindetail) {
            $select .= ", pm, m, pa, po";
            $queryBuilder
                ->leftJoin("p.media", "pm")
                ->leftJoin("pm.media", "m")
                ->leftJoin("p.attributes", "pa")
                ->leftJoin("p.options", "po")
            ;
        }

        $queryBuilder->select($select);
        if ($category !== null) {
            $expr = $queryBuilder->expr()->orX($queryBuilder->expr()->eq("pc.category",":category"));
            $queryBuilder->andWhere($expr)
            ->setParameter('category', $category);
            if ($recursive) {
                $categoryEntity = $this->getEntityManager()->getRepository("CoreCategoryBundle:Category")->find($category);
                if ($categoryEntity) {
                    $expr2 = $queryBuilder->expr()->andX($queryBuilder->expr()->gte("c.lft", ":cleft"), $queryBuilder->expr()->lte("c.lft", ":cright"));
                    $expr->add($expr2);
                    $queryBuilder->setParameter('cleft', $categoryEntity->getLeft());
                    $queryBuilder->setParameter('cright', $categoryEntity->getRight());
                }
            }
        }
        if ($onlyenabled) {
            $queryBuilder
                ->andWhere("p.enabled =:enabled")
                ->setParameter("enabled", $onlyenabled);
        }
        $queryBuilder
            ->addOrderBy("pc.position", 'asc')
            ->addOrderBy("p.id", 'asc')
        ;
        if ($join) {
            $queryBuilder
                ->addOrderBy("pp.priceAmount", 'asc')
                ->addOrderBy("pp.priceVAT", 'asc')
            ;
        }

        return $queryBuilder;
    }

    public function findByCategoryQuery($category = null, $recursive = false, $onlyenabled = false, $join = true, $joindetail = true)
    {
        return $this->setHint($this->findByCategoryQueryBuilder($category, $recursive, $onlyenabled, $join, $joindetail)->getQuery());
    }

    public function findByCategory($category = null, $recursive = false, $onlyenabled = false, $join = true, $joindetail = true)
    {
        return $this->findByCategoryQuery($category, $recursive, $onlyenabled, $join, $joindetail)->getResult();
    }

    public function findNotInCategoryQueryBuilder($categoryId = null, $recursive = false, $onlyenabled = false)
    {
        $queryBuilder2 = $this->findByCategoryQueryBuilder($categoryId, $recursive, $onlyenabled, false, false)
                ->select("p")
                ->andWhere("p2 = p");
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $expr = $queryBuilder->expr()->not($queryBuilder->expr()->exists($queryBuilder2));
        $queryBuilder = $queryBuilder
                ->select('p2')
                ->from("CoreProductBundle:Product", 'p2')
                ->leftJoin("p2.vendor", "v")
                ->where($expr);
        $queryBuilder->setParameters($queryBuilder2->getParameters());

        return $queryBuilder;
    }

    public function findNotInCategoryQuery($categoryId  = null, $recursive = false, $onlyenabled = false)
    {
        return $this->findNotInCategoryQueryBuilder($categoryId , $recursive, $onlyenabled)->getQuery();
    }

    public function findNotInCategory($categoryId  = null, $recursive = false, $onlyenabled = false)
    {
        return $this->findNotInCategoryQuery($categoryId , $recursive, $onlyenabled)->getResult();
    }

    public function filterQueryBuilder($queryBuilder, Filter $filter = null, $selector = "p")
    {
        if ($filter) {
            $words = $filter->getWordsArray();
            if (!empty($words)) {
                $i = 0;
                foreach ($words as $word) {
                    $where = $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like("lower({$selector}.title)", 'lower(:word1'.$i.')'),
                        $queryBuilder->expr()->like("lower({$selector}.shortDescription)", 'lower(:word2'.$i.')'),
                        $queryBuilder->expr()->like("lower({$selector}.longDescription)", 'lower(:word3'.$i.')'),
                        $queryBuilder->expr()->like("lower({$selector}.tags)", 'lower(:word4'.$i.')'),
                        $queryBuilder->expr()->like("lower(v.title)", 'lower(:word5'.$i.')')
                    );
                    $queryBuilder->andWhere($where);
                    $queryBuilder->setParameter('word1'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word2'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word3'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word4'.$i, '%' . strtolower($word) . '%');
                    $queryBuilder->setParameter('word5'.$i, '%' . strtolower($word) . '%');
                    $i ++;
                }
            }
            $vendor = $filter->getVendor();
            if ($vendor) {
                $queryBuilder->andWhere('p.vendor = :vid');
                if (is_integer($vendor)) {
                    $queryBuilder->setParameter('vid', $vendor);
                } else {
                    $queryBuilder->setParameter('vid', $vendor->getId());
                }
            }
            $order = explode(" ", $filter->getOrder());
            if (!empty($order)) {
                if (!empty($order[0])) {
                    $order[0] = str_replace("p.", $selector . "." , $order[0]);
                    $queryBuilder->orderBy($order[0], (isset($order[1]) && !empty($order[1])) ? $order[1] : "asc");
                }
            }
        }

        $tags = $filter->getTags();
        if (!empty($tags)) {
            foreach ($tags as $key=>$tag) {
                $queryBuilder->andWhere($queryBuilder->expr()->like("{$selector}.tags", ':tag'.$key));
                $queryBuilder->setParameter('tag'.$key, '%' . $tag . ', %');
            }
        }

        return $queryBuilder;
    }
}
