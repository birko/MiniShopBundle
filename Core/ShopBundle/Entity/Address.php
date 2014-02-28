<?php

namespace Core\ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Core\ShopBundle\Entity\Address
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\ShopBundle\Entity\AddressRepository")
 */
class Address implements \Serializable
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
     * @var string $surname
     *
     * @ORM\Column(name="surname", type="string", length=255)
     */
    private $surname;

    /**
     * @var string $company
     *
     * @ORM\Column(name="company", type="string", length=255, nullable = true)
     */
    private $company;

    /**
     * @var string $street
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    private $street;

    /**
     * @var string $houseNumber
     *
     * @ORM\Column(name="housenumber", type="string", length=255)
     */
    private $houseNumber;

    /**
     * @var string $city
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    private $city;

    /**
     * @var string $ZIP
     *
     * @ORM\Column(name="zip", type="string", length=10)
     */
    private $ZIP;

    /**
     * @var string $TIN
     *
     * @ORM\Column(name="tin", type="string", length=20, nullable = true)
     */
    private $TIN;

    /**
     * @var string $OIN
     *
     * @ORM\Column(name="oin", type="string", length=20, nullable = true)
     */
    private $OIN;

    /**
     * @var string $VATIN
     *
     * @ORM\Column(name="vatin", type="string", length=20, nullable = true)
     */
    private $VATIN;

    /**
     * @ORM\ManyToOne(targetEntity="Core\ShopBundle\Entity\State")
     * @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     */
    private $state;

    /**
     * @ORM\Column(name="email", type="string", nullable = true)
     */
    private $email = "";

    /**
     * @ORM\Column(name="phone", type="string", nullable = true)
     */
    private $phone = "";

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

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
     * Set surname
     *
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * Get surname
     *
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set company
     *
     * @param string $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set street
     *
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set houseNumber
     *
     * @param string $houseNumber
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * Get houseNumber
     *
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * Set city
     *
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set zip
     *
     * @param string $zip
     */
    public function setZIP($zip)
    {
        $this->ZIP = $zip;
    }

    /**
     * Get zip
     *
     * @return string
     */
    public function getZIP()
    {
        return $this->ZIP;
    }

    /**
     * Set TIN
     *
     * @param string $tIN
     */
    public function setTIN($tIN)
    {
        $this->TIN = $tIN;
    }

    /**
     * Get TIN
     *
     * @return string
     */
    public function getTIN()
    {
        return $this->TIN;
    }

    /**
     * Set OIN
     *
     * @param string $oIN
     */
    public function setOIN($oIN)
    {
        $this->OIN = $oIN;
    }

    /**
     * Get OIN
     *
     * @return string
     */
    public function getOIN()
    {
        return $this->OIN;
    }

    /**
     * Set VATIN
     *
     * @param string $vATIN
     */
    public function setVATIN($vATIN)
    {
        $this->VATIN = $vATIN;
    }

    /**
     * Get VATIN
     *
     * @return string
     */
    public function getVATIN()
    {
        return $this->VATIN;
    }

    /**
     * Set State
     *
     * @param string $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * Get State
     *
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set User
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get User
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Get phone
     *
     * @return phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    public function serialize()
    {
        return serialize(array(
            $this->getId(),
            $this->getName(),
            $this->getSurname(),
            $this->getCompany(),
            $this->getStreet(),
            $this->getHouseNumber(),
            $this->getCity(),
            $this->getState(),
            $this->getZIP(),
            $this->getEmail(),
            $this->getPhone(),
            $this->getOIN(),
            $this->getTin(),
            $this->getVATIN(),
            $this->getUser(),
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->name,
            $this->surname,
            $this->company,
            $this->street,
            $this->houseNumber,
            $this->city,
            $this->state,
            $this->ZIP,
            $this->email,
            $this->phone,
            $this->OIN,
            $this->TIN,
            $this->VATIN,
            $this->user
        ) = unserialize($serialized);
    }

    public function __toString()
    {
        $string = "";
        $string .=   $this->getName();
        $string .= " " . $this->getSurname();
        $company = $this->getCompany();
        if (!empty($company)) {
            $string .= ", " . $company;
        }
        $string .= ", " . $this->getStreet();
        $string .= " " . $this->getHouseNumber();
        $string .= ", " . $this->getCity();
        $string .= " " . $this->getZip();
        $string .= " " . $this->getState();

        return $string;
    }
}
