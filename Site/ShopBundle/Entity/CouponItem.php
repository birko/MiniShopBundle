<?php

namespace Site\ShopBundle\Entity;

/**
 * Description of CouponItem
 *
 * @author Birko
 */
class CouponItem extends CartItem implements \Serializable
{
    protected $code = null;

    public function __construct()
    {
        parent::__construct();
        $this->setChangeAmount(false);
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array[] = $this->code;

        return $array;
    }

    public function fromArray($array)
    {
        parent::fromArray($array);
        $this->code = $array[9];
    }

    public function compareData($data)
    {
        if (!($data instanceof CouponItem)) {
            return false;
        }
        if ($data->getCode() != $this->getCode()) {
            return false;
        }

        return parent::compareData($data);
    }
}
