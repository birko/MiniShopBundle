<?php

namespace Core\UserBundle\Entity;

/**
 * Description of PriceGroup
 *
 * @author birko
 */

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Core\UserBundle\Entity\PriceGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\UserBundle\Entity\PriceGroupRepository")
 */

class PriceGroup implements \Serializable
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string $name
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var decimal $rate
     *
     * @ORM\Column(name="rate", type="decimal", precision=10, scale=6 )
     */
    protected $rate;

    /**
    * @ORM\OneToMany(targetEntity="Core\UserBundle\Entity\User", mappedBy="priceGroup")
    */
    private $users;

    /**
     * @ORM\Column(name="is_default", type="boolean", nullable = true)
     */
    protected $default = false;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    public function getUsers()
    {
        return $this->users;
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

    public function __toString()
    {
        return $this->getName();
    }

    public function serialize()
    {
        return serialize(array(
        $this->id,
        $this->name,
        $this->rate,
        $this->default
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->rate,
            $this->default
        )=unserialize($serialized);
    }
}
