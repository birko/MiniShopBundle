<?php

namespace Core\AttributeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * Core\AttributeBundle\Entity\AttributeName
 *
 * @ORM\Table(indexes={
        @ORM\Index(name="attributename_name_idx", columns={"name"})
   })
 * @UniqueEntity("name")
 * @ORM\Entity(repositoryClass="Core\AttributeBundle\Entity\AttributeNameRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class AttributeName extends TranslateEntity implements \Serializable
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
     * @var string name
     * @Gedmo\Translatable
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="Core\AttributeBundle\Entity\AttributeValue", mappedBy="name")
     * @ORM\OrderBy({ "value" = "ASC", "id" = "ASC"})
     */
    protected $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    /**
     * Get name
     *
     * @return int
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
     * Set values
     *
     * @param  $values
     */
    public function setValues($values = array())
    {
        $this->values = ($values instanceof ArrayCollection) ? $values : new ArrayCollection($values);
    }

    /**
     * Get values
     *
     * @return ArrayCollection
     */
    public function getValues()
    {
        return $this->values;
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function toArray()
    {
        $array =  array();
        $array[] = $this->id;
        $array[] = $this->name;

        return $array;
    }

    public function fromArray($array)
    {
        $this->id = $array[0];
        $this->name = $array[1];
    }

    public function serialize()
    {
        return serialize($this->toArray());
    }

    public function unserialize($serialized)
    {
        $array = unserialize($serialized);
        $this->fromArray($array);
    }
}
