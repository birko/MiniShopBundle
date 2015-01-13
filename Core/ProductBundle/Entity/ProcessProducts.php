<?php

namespace Core\ProductBundle\Entity;

class ProcessProducts
{
    private $products;

    public function __construct()
    {
    }

    public function getProducts()
    {
        return $this->products;
    }

    public function setProducts($products)
    {
        $this->products = $products;
        
        return $this;
    }
}
