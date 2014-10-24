<?php

namespace Core\ProductBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * Core\ProductBundle\Entity\ProductAmount
 *
 * @ORM\Table(indexes={
        @ORM\Index(name="stock_product_idx", columns={"product_id"})
    })
 * @ORM\Entity(repositoryClass="Core\ProductBundle\Entity\StockRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class Stock extends TranslateEntity
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
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal", nullable = true)
     */
    private $amount;

    /**
     * @var string $availability
     * @Gedmo\Translatable
     * @ORM\Column(name="availability", type="string", length=255, nullable = true )
     */
    private $availability;

    /**
     * @ORM\OneToOne(targetEntity="Core\ProductBundle\Entity\Product", inversedBy="stock")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
     protected $product;

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
     * Set amount
     *
     * @param decimal $amount
     */
    public function setAmount($amount)
    {
        $this->amount = ($amount > 0) ? $amount : 0;
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
     * Set availability
     *
     * @param string $availability
     */
    public function setAvailability($availability)
    {
        $this->availability = $availability;
    }

    /**
     * Get availability
     *
     * @return string
     */
    public function getAvailability()
    {
        return $this->availability;
    }

    /**
     * Set product
     *
     * @param \Core\ProductBundle\Entity\Product product
     */
    public function setProduct(\Core\ProductBundle\Entity\Product $product = null)
    {
        $this->product = $product;
    }

    /**
     * Get product
     *
     * @return \Core\ProductBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
