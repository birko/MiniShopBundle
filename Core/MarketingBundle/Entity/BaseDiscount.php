<?php

namespace Core\MarketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\PriceBundle\Entity\AbstractPrice;

/**
 * @ORM\MappedSuperclass
 */
abstract class BaseDiscount extends AbstractPrice
{

    /**
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=12, scale=6, nullable=true)
     */
    protected $price;

    /**
     * @var decimal $priceVAT
     *
     * @ORM\Column(name="priceVAT", type="decimal", precision=12, scale=6, nullable=true)
     */
    protected $priceVAT;

    /**
     * @var decimal $discount
     *
     * @ORM\Column(name="discount", type="decimal", precision=12, scale=6, nullable=true)
     */
    protected $discount;

    /**
     * Set discount
     *
     * @param decimal $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Get discount
     *
     * @return decimal
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set discount
     *
     * @param decimal $discount
     */
    public function setDiscountPerc($discount)
    {
        $this->setDiscount($discount / 100);
    }

    /**
     * Get discount
     *
     * @return decimal
     */
    public function getDiscountPerc()
    {
        return $this->getDiscount() * 100;
    }

    public function isDiscount()
    {
        $discount = $this->getDiscount();

        return !empty($discount);
    }

    public function calculateDiscount(AbstractPrice $price)
    {
        $price->setPrice(($price->getPrice() - $this->getPrice()) * ( 1 - $this->getDiscount()));
        $price->recalculate(true);

        return $price;
    }
}
