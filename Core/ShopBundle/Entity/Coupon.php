<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Core\MarketingBundle\Entity\Discount;
/**
 * Core\ShopBundle\Entity\Coupon
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\CouponRepository")
 */
class Coupon extends Discount
{
    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var boolean $used
     *
     * @ORM\Column(name="used", type="boolean", nullable = true)
     */
    private $used;

    /**
     * @ORM\ManyToMany(targetEntity="Core\ProductBundle\Entity\Product")
     * @ORM\JoinTable(name="products_coupons")
     */
    private $products;

    public function __construct()
    {
        parent::__construct();
        $this->products = new ArrayCollection();
        $this->setUsed(false);
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set used
     *
     * @param boolean $used
     */
    public function setUsed($used)
    {
        $this->used = $used;
    }

    /**
     * Get used
     *
     * @return boolean
     */
    public function isUsed()
    {
        return $this->used;
    }

     /**
     * Get products
     *
     * @return ArrayCollection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
