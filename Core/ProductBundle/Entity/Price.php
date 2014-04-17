<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\UserBundle\Entity\PriceGroup;
use Core\PriceBundle\Entity\AbstractPrice;
/**
 * Core\ProductBundle\Entity\Price
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\PriceRepository")
 */
class Price extends AbstractPrice
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string $type
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ProductBundle\Entity\Product", inversedBy="prices")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\PriceGroup")
     * @ORM\JoinColumn(name="pricegroup_id", referencedColumnName="id")
     */
    private $priceGroup;

    /**
     * @var decimal $priceAmount
     *
     * @ORM\Column(name="priceamount", type="decimal", precision=10, scale=6, nullable = true)
     */
    private $priceAmount;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable = true)
     */
    protected $default = false;

    public function __construct()
    {
        $this->setPriceAmount(0);
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
     * Set priceAmount
     *
     * @param decimal $priceAmount
     */
    public function setPriceAmount($priceAmount)
    {
        $this->priceAmount = $priceAmount;
    }

    /**
     * Get priceAmount
     *
     * @return decimal
     */
    public function getPriceAmount()
    {
        return $this->priceAmount;
    }

    /**
     * Set default
     *
     * @param boolean $default
     */
    public function setDefault($default)
    {
        $this->default = $default;
    }

    public function isDefault()
    {
        return $this->default;
    }
}
