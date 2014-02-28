<?php

namespace Core\NewsletterBundle\Entity;

/**
 * Core\NewsletterBundle\Entity\SendNewsletter

 */
class SendNewsletter
{
    private $newsletter;

    private $groups;

    private $emails;

    private $not = false;

    public function getNewsletter()
    {
        return $this->newsletter;
    }

    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    public function getEmails()
    {
        return $this->emails;
    }

    public function setEmails($emails)
    {
        $this->emails = $emails;
    }

    public function isNot()
    {
        return $this->not;
    }

    public function setNot($not)
    {
        $this->not = $not;
    }
}
