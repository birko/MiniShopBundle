<?php

namespace Core\CommonBundle\Entity;

/**
 * Description of ProductFilter
 *
 * @author Birko
 */
class Filter implements \Serializable
{
    protected $words = null;
    protected $page = 1;

    public function __construct()
    {
    }

    public function getWords()
    {
        return $this->words;
    }

    public function setWords($words)
    {

        $this->words = trim($words);
    }

    public function getWordsArray()
    {
        return preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $this->getWords(), null, PREG_SPLIT_NO_EMPTY);
    }

    public function setPage($page)
    {
        $this->page = $page;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function serialize()
    {
        return serialize(array(
            $this->words,
            $this->page
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->words,
            $this->page
        ) = unserialize($serialized);
    }
}
