<?php

namespace Core\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Process
{
    protected $products;
    protected $action;

    public function __construct()
    {
    }

    /**
     * Set action
     *
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set products
     *
     */
    public function setProcessProducts($products = array())
    {
        $this->products = new ArrayCollection($products);
    }

    /**
     * Get products
     *
     */
    public function getProcessProducts()
    {
        return $this->products;
    }
}
