<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\PriceBundle\Entity\Currency;
use Core\UserBundle\Entity\PriceGroup;

/**
 * Core\ShopBundle\Entity\OrderItem
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\OrderItemRepository")
 */
class OrderItem
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=6 )
     */
    protected $price;

    /**
     * @var decimal $priceVAT
     *
     * @ORM\Column(name="price_vat", type="decimal", precision=10, scale=6 )
     */
    protected $priceVAT;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="text", nullable = true)
     */
    private $description;

    /**
     * @var string $options
     *
     * @ORM\Column(name="options", type="text", nullable = true)
     */
    private $options;

    /**
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal")
     */
    private $amount;

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

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\Order", inversedBy="items")
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ProductBundle\Entity\Product", inversedBy="orderItems")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $product;
    
    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\PriceGroup")
     * @ORM\JoinColumn(name="pricegroup_id", referencedColumnName="id")
     */
    private $priceGroup;
    
     /**
     * @ORM\ManyToOne(targetEntity="Core\PriceBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    public function __construct()
    {
    }

     /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set PriceGroup
     *
     * @param priceGroup $pricegroup
     */
    public function setPriceGroup(PriceGroup $pricegroup)
    {
        $this->priceGroup = $pricegroup;
    }

    /**
     * Get priceGroup
     *
     * @return PriceGroup
     */
    public function getPriceGroup()
    {
        return $this->priceGroup;
    }
    
    /**
     * Set Currency
     *
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get Currency
     *
     * @return Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * Get price
     *
     * @return decimal
     */
    public function getPrice()
    {
        return $this->price;
    }

     /**
     * Set price
     *
     * @param decimal $price
     */
    public function setPriceVAT($price)
    {
        $this->priceVAT = $price;
    }

    /**
     * Get price
     *
     * @return decimal
     */
    public function getPriceVAT()
    {
        return $this->priceVAT;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set options
     *
     * @param string $description
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Get options
     *
     * @return string
     */
    public function getOptions()
    {
        return $this->options;
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

    /**
     * Set Order
     *
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * Get Order
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product
     *
     * @param Product product
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

    public function getPriceVATTotal()
    {
        return $this->getPriceVAT() * $this->getAmount();
    }
}
