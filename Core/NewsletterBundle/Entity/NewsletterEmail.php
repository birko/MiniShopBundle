<?php

namespace Core\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Core\NewsletterBundle\Entity\NewlsetterEmail
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\NewsletterBundle\Entity\NewsletterEmailRepository")
 * @UniqueEntity("email")
 */
class NewsletterEmail
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
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(name="is_enabled", type="boolean", nullable=true)
     */
    protected $enabled = false;

    /**
     * @ORM\ManyToMany(targetEntity="NewsletterGroup", inversedBy="emails")
     * @ORM\JoinTable(name="newsletter_group_email")
     *
     */

    protected $groups;

    public function __construct()
    {
        $this->setEnabled(false);
        $this->groups = new ArrayCollection();
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
     * Add group
     *
     * @param Group
     */
    public function addGroup($group)
    {
        if (!$this->getGroups()->containsKey($group->getId())) {
            $this->getGroups()->add($group);
        }
    }

    /**
     * Remove group
     *
     * @param Group
     */
    public function removeGroup($group)
    {
         $this->getGroups()->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return ArrayCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    public function __toString()
    {
        return $this->getEmail();
    }
}
