<?php

namespace Core\AttributeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Core\CommonBundle\Entity\TranslateEntity;

/**
 * Neonus\Nws\AttributeBundle\Entity\AttributeName
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\AttributeBundle\Entity\AttributeValueRepository")
 * @Gedmo\TranslationEntity(class="Core\CommonBundle\Entity\Translation")
 */
class AttributeValue extends TranslateEntity implements \Serializable
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
     *
     * @ORM\ManyToOne(targetEntity="Core\AttributeBundle\Entity\AttributeName", inversedBy="values", cascade={"persist"})
     * @ORM\JoinColumn(name="attributename_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $name;

    /**
     * @var string name
     * @Gedmo\Translatable
     * @ORM\Column(type="text", nullable=true)
     */
    protected $value;

    /**
     * @var boolean $serialized
     *
     * @ORM\Column(name="serialized", type="boolean", nullable=true)
     */
    protected $serialized;

    public function __construct()
    {
        $this->setSerialized(false);
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
     * @param AttributeName $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return AttributeName
     */
    public function getName()
    {
        return $this->name;
    }

    public function getAttributeName()
    {
        return ($this->getName()) ? $this->getName()->getName() : null;
    }

    /**
     * Set value
     *
     * @param  $value
     */
    public function setValue($value = null)
    {
        $this->value = $value;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set serialized
     *
     * @param boolean $serialized
     */
    public function setSerialized($serialized)
    {
        $this->serialized = $serialized;
    }

    /**
     * Is serialized
     *
     * @return boolean
     */
    public function isSerialized()
    {
        return $this->serialized;
    }

    /**
     * Get serializedValue
     *
     * @return mixed
     */

    public function getSerializedValue()
    {
        $value = $this->getValue();
        if (!empty($value)) {
            return unserialize($value);
        } else {
            return null;
        }
    }

    /**
     * Set serializedValue
     *
     * @param mixed $value
     */
    public function setSerializedValue($value = null)
    {
        if (!empty($value)) {
            $this->setValue(serialize($value));
            $this->setSerialized(true);
        } else {
            $this->setSerialized(false);
            $this->setValue();
        }
    }

    public function __toString()
    {
        return $this->getValue();
    }

    public function toArray()
    {
        $array =  array();
        $array[] = $this->id;
        $array[] = $this->value;
        $array[] = $this->serialized;
        $array[] = $this->name;

        return $array;
    }

    public function fromArray($array)
    {
        $this->id = $array[0];
        $this->value = $array[1];
        $this->serialized = $array[2];
        $this->name = $array[2];
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
