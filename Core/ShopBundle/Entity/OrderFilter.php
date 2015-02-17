<?php
namespace Core\ShopBundle\Entity;

use Core\CommonBundle\Entity\Filter as BaseFilter;

class OrderFilter  extends BaseFilter
{
    private $shipping_status = null;
    private $order_status = null;
    private $shipping_state = null;
    protected $item_words = null;

    public function setShippingStatus($shipping_status)
    {
        $this->shipping_status= $shipping_status;
    }

    public function getShippingStatus()
    {
        return $this->shipping_status;
    }

    public function setOrderStatus($order_status)
    {
        $this->order_status= $order_status;
    }

    public function getOrderStatus()
    {
        return $this->order_status;
    }

    public function setShippingState($shipping_state)
    {
        $this->shipping_state= $shipping_state;
    }

    public function getShippingState()
    {
        return $this->shipping_state;
    }
    
    public function getItemWords()
    {
        return $this->item_words;
    }

    public function setItemWords($words)
    {

        $this->item_words = trim($words);
    }

    public function getItemWordsArray()
    {
        return preg_split('/([\s\-_,:;?!\/\(\)\[\]{}<>\r\n"]|(?<!\d)\.(?!\d))/', $this->getItemWords(), null, PREG_SPLIT_NO_EMPTY);
    }

}
