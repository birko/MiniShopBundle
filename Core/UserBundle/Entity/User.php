<?php
/**
 * Description of User
 *
 * @author birko
 */
namespace Core\UserBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Table(name="CoreUser")
 * @ORM\Entity()
 * @UniqueEntity("email")
 * @UniqueEntity("login")
 * @ORM\Entity(repositoryClass="Core\UserBundle\Entity\UserRepository")
 */
class User implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", unique = true)
     */
    protected $login = "";

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(min=6)
     */
    protected $password = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $salt = "";

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = false;

    /**
     * @ORM\Column(type="string", unique = true)
     */
    protected $email = "";

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created_at = null;

    /**
     * @ORM\Column(type="string")
     */
    private $roles = "";

    /**
     * @ORM\ManyToOne(targetEntity="Core\UserBundle\Entity\PriceGroup", inversedBy="users")
     * @ORM\JoinColumn(name="pricegroup_id", referencedColumnName="id")
     */
    private $priceGroup;

    public function __construct()
    {
        $this->setEnabled(false);
        $this->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        $this->setCreatedAt(new \DateTime());
        $this->setRoles(array("ROLE_USER"));
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
     * Set login
     *
     * @param string $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * Get login
     *
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled()
    {
        return $this->enabled;
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
     * Set createdAt
     *
     * @param datetime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    /**
     * Get createdAt
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set createdAt
     *
     * @param array $roles
     */
    public function setRoles($roles)
    {
        $this->roles = implode(', ', $roles);
    }

    /**
     * Get Roles
     *
     * @return Array
     */
    public function getRoles()
    {
        return explode(', ', $this->roles);
    }

    public function isEqualTo(UserInterface $user)
    {
        return $this->getLogin() == $user->getUsername();
    }

    public function eraseCredentials()
    {
    }

    public function getUsername()
    {
        return $this->getLogin();
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function __toString()
    {
        return $this->getLogin();
    }

    /**
     * Set PriceGroup
     *
     * @param priceGroup $pricegroup
     */
    public function setPriceGroup(PriceGroup $pricegroup)
    {
        $this->priceGroup = $pricegroup;
    }

    /**
     * Get priceGrpup
     *
     * @return PriceGroup
     */
    public function getPriceGroup()
    {
        return $this->priceGroup;
    }

    public function serialize()
    {
        return serialize(array(
        $this->id,
        $this->login,
        $this->roles,
        $this->priceGroup
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->login,
            $this->roles,
            $this->priceGroup
        )=unserialize($serialized);
    }
}
