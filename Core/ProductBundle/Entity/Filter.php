<?php

namespace Core\ProductBundle\Entity;

use Core\CommonBundle\Entity\Filter as BaseFilter;
/**
 * Description of ProductFilter
 *
 * @author Birko
 */
class Filter extends BaseFilter implements \Serializable
{
    protected $vendor = null;
    protected $order = null;
    protected $category = null;
    protected $tags  = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder($order)
    {
        $this->order = $order;
    }

    public function serialize()
    {
        $serialized =  unserialize(parent::serialize());
        $serialized[] = $this->vendor;
        $serialized[] = $this->order;
        $serialized[] = $this->category;
        $serialized[] = $this->tags;

        return serialize($serialized);
    }

    public function unserialize($serialized)
    {
        $unserialized = unserialize($serialized);
        $this->tags = array_pop($unserialized);
        $this->category = array_pop($unserialized);
        $this->order = array_pop($unserialized);
        $this->vendor = array_pop($unserialized);
        parent::unserialize(serialize($unserialized));
    }
}
