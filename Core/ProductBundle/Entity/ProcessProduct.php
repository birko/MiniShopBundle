<?php

namespace Core\ProductBundle\Entity;

class ProcessProduct
{
    private $productId;
    private $include;
    private $product;

    public function __construct()
    {
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;

        return $this;
    }

    public function isInclude()
    {
        return $this->include;
    }

    public function setInclude($include)
    {
        $this->include = $include;

        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        if ($this->getProduct()) {
            $this->setProductId($this->getProduct()->getId());
        }

        return $this;
    }
}
