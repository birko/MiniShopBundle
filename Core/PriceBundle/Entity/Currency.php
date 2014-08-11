<?php

namespace Core\PriceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Core\PriceBundle\Entity\Currency
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\PriceBundle\Entity\CurrencyRepository")
 * @UniqueEntity("name")
 * @UniqueEntity("code")
 */
class Currency implements \Serializable
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
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string $symbol
     *
     * @ORM\Column(name="symbol", type="string", length=10)
     */
    private $symbol;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=10)
     */
    private $code;

    /**
     * @var decimal $rate
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=6)
     */
    private $rate;

    /**
     * @var boolean $default
     *
     * @ORM\Column(name="is_default", type="boolean", nullable=true)
     */
    private $default;

    public function __construct()
    {
        $this->setDefault(false);
        $this->setRate(1);
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
     * Set symbol
     *
     * @param string $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * Get symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
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
     * Set rate
     *
     * @param decimal $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * Get rate
     *
     * @return decimal
     */
    public function getRate()
    {
        return $this->rate;
    }

    public function getRatePercentage()
    {
        return $this->getRate() * 100;
    }

    public function setRatePercentage($rate)
    {
        $this->setRate($rate);
    }

    /**
     * Set default
     *
     * @param boolean $boolean
     */
    public function setDefault($boolean)
    {
        $this->default = $boolean;
    }

    /**
     * Get default
     *
     * @return boolean
     */
    public function isDefault()
    {
        return $this->default;
    }

    public function equals($entity)
    {
        return $entity && $this->getId() == $entity->getId() && $entity->getCode() == $this->getCode();
    }

    public function serialize()
    {
        return serialize(array(
           $this->id,
           $this->name,
           $this->rate,
           $this->symbol,
           $this->code,
           $this->default
        ));
    }

    public function unserialize($serialized)
    {
        list(
           $this->id,
           $this->name,
           $this->rate,
           $this->symbol,
           $this->code,
           $this->default
        ) = unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getName();
    }
}
