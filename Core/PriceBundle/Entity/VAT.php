<?php

namespace Core\PriceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VAT
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\PriceBundle\Entity\VATRepository")
 */
class VAT implements \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=6)
     */
    private $rate;

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
     * @param  string $name
     * @return VAT
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
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
     * Set rate
     *
     * @param  float $rate
     * @return VAT
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set ratePercentage
     *
     * @param  float $rate
     * @return VAT
     */
    public function setRatePercentage($rate)
    {
        return $this->setRate($rate / 100);
    }

    /**
     * Get ratePercentage
     *
     * @return float
     */

    public function getRatePercentage()
    {
        return $this->getRate() * 100;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->rate

        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->rate
        ) = unserialize($serialized);

    }

    public function __toString()
    {
        return $this->getName() . " (" . $this->getRatePercentage() . "%)";
    }
}
