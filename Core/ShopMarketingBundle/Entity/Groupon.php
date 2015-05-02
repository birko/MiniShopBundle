<?php

namespace Core\ShopMarketingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Core\MarketingBundle\Entity\Discount;
/**
 * Core\ShopMarketingBundle\Entity\Groupon
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopMarketingBundle\Entity\GrouponRepository")
 */
class Groupon extends Discount
{
    /**
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal", precision=12, scale=6, nullable=true)
     */
    protected $amount;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ProductBundle\Entity\Product", inversedBy="prices")
     * @ORM\JoinColumn(name="price_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\Shipping")
     * @ORM\JoinColumn(name="shipping_id", referencedColumnName="id")
     */
    private $shipping;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\Payment")
     * @ORM\JoinColumn(name="payment_id", referencedColumnName="id")
     */
    private $payment;

    public function __construct()
    {
        parent::__construct();
        $this->setAmount(1);
    }

    /**
     * Set amount
     *
     * @param decimal $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return decimal
     */
    public function getAmount()
    {
        return $this->amount;
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

    /**
     * Set product
     *
     * @param Product $product
     */
    public function setProduct($product)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set payment
     *
     * @param Payment $payment
     */
    public function setPayment($payment)
    {
        $this->payment= $payment;
    }

    /**
     * Get payment
     *
     * @return Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Set shippings
     *
     * @param Shipping $shipping
     */
    public function setShipping($shipping)
    {
        $this->shipping= $shipping;
    }

    /**
     * Get shipping
     *
     * @return Shipping
     */
    public function getShipping()
    {
        return $this->shipping;
    }
}
