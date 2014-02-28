<?php

namespace Core\NewsletterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Core\NewsletterBundle\Entity\NewsletterGroup
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Core\NewsletterBundle\Entity\NewsletterGroupRepository")
 * @UniqueEntity("name")
 */
class NewsletterGroup
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
     * @ORM\ManyToMany(targetEntity="NewsletterEmail", mappedBy="groups"))
     * @ORM\JoinTable(name="newsletter_group_email")
     */
    protected $emails;

    public function __construct()
    {
        $this->emails = new ArrayCollection();
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
     * Add Email
     *
     * @param Email
     */
    public function addEmail($email)
    {
        $this->getEmails()->add($email);
    }

    /**
     * Remove Email
     *
     * @param Email
     */
    public function removeEmail($email)
    {
         $this->getEmails()->removeElement($email);
    }

    /**
     * Get emails
     *
     * @return ArrayCollection
     */
    public function getEmails()
    {
        return $this->emails;
    }
}
