<?php

namespace Site\ShopBundle\Entity;

/**
 * GrouponItem
 *
 * @author Birko
 */
class GrouponItem extends CouponItem implements \Serializable
{
    public function compareData($data)
    {
        if (!($data instanceof GrouponItem)) {
            return false;
        }

        return parent::compareData($data);
    }
}
