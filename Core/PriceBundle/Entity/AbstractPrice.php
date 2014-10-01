<?php

namespace Core\PriceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AbstractPrice
 *
 */
class AbstractPrice
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var decimal $price
     *
     * @ORM\Column(name="price", type="decimal", precision=12, scale=6)
     */
    protected $price;

    /**
     * @var decimal $priceVAT
     *
     * @ORM\Column(name="priceVAT", type="decimal", precision=12, scale=6)
     */
    protected $priceVAT;

    /**
     * @ORM\ManyToOne(targetEntity="Core\PriceBundle\Entity\VAT")
     * @ORM\JoinColumn(name="vat_id", referencedColumnName="id")
     */
    protected $vat;
    
    /**
     * @ORM\ManyToOne(targetEntity="Core\PriceBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    protected $currency;

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
     * Set priceVAT
     *
     * @param decimal $priceVAT
     */
    public function setPriceVAT($priceVAT)
    {
        $this->priceVAT = $priceVAT;
    }

    /**
     * Get priceVAT
     *
     * @return decimal
     */
    public function getPriceVAT()
    {
        return $this->priceVAT;
    }

    /**
     * Set VAT
     *
     * @param VAT $vat
     */
    public function setVAT(VAT $vat)
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Get VAT
     *
     * @return VAT
     */
    public function getVAT()
    {
        return $this->vat;
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

    public function recalculate($vat = true)
    {
        $rate = 0;
        if ($this->getVAT()) {
            $rate = $this->getVAT()->getRate();
        }

        if ($vat) {
            if ($this->getPrice() !== null) {
                $this->setPriceVAT($this->getPrice() * (1 + $rate));
            }
        } else {
            if ($this->getPriceVAT() !== null) {
                $this->setPrice($this->getPriceVAT() / (1 + $rate));
            }
        }
    }
    
    public function calculatePrice(Currency $currency) 
    {   
        return $this->getPriceVAT() /  $this->getCurrency()->getRate() * $currency->getRate(); 
    }
    
    public function calculatePriceVAT(Currency $currency) 
    {
        return $this->getPriceVAT() /  $this->getCurrency()->getRate() * $currency->getRate(); 
    }
}
