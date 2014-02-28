<?php

namespace Core\ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Process
{
    protected $orders;
    protected $type;
    protected $order_status;
    protected $shipping_status;
    protected $export;

    public function __construct()
    {
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

     /**
     * Set shipping_status
     *
     */
    public function setShippingStatus($shipping_status)
    {
        $this->shipping_status= $shipping_status;
    }

    /**
     * Get shipping_status
     */
    public function getShippingStatus()
    {
        return $this->shipping_status;
    }

    /**
     * Set order_status
     *
     */
    public function setOrderStatus($order_status)
    {
        $this->order_status= $order_status;
    }

    /**
     * Get shipping_status
     *
     */
    public function getOrderStatus()
    {
        return $this->order_status;
    }

    /**
     * Set orders
     *
     */
    public function setProcessOrders($orders = array())
    {
        $this->orders = new ArrayCollection($orders);
    }

    /**
     * Get orders
     *
     */
    public function getProcessOrders()
    {
        return $this->orders;
    }

    /**
     * Set export
     *
     * @param string $export
     */
    public function setExport($export)
    {
        $this->export = $export;
    }

    /**
     * Get export
     *
     * @return string
     */
    public function getExport()
    {
        return $this->export;
    }
}
