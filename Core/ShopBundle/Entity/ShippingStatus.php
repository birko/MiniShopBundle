<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Core\ShopBundle\Entity\ShippingStatus
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\ShippingStatusRepository")
 */
class ShippingStatus implements \Serializable
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

    public function __toString()
    {
        return $this->getName();
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
        ) = unserialize($serialized);
    }
}
