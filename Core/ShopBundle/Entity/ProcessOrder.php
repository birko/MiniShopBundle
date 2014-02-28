<?php

namespace Core\ShopBundle\Entity;

class ProcessOrder
{
    private $orderId;
    private $include;
    private $order;

    public function __construct()
    {
    }

    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function isInclude()
    {
        return $this->include;
    }

    public function setInclude($include)
    {
        $this->include = $include;

        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(Order $order)
    {
        $this->order = $order;
        if ($this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
        }

        return $this;
    }
}
