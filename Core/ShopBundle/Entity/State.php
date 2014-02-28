<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Translatable\Translatable;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * Core\ShopBundle\Entity\State
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\StateRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class State extends TranslateEntity implements \Serializable
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
     * @Gedmo\Translatable
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
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

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->name,
            $this->locale
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->locale
        )=unserialize($serialized);
    }

    public function __toString()
    {
        return $this->getName();
    }
}
