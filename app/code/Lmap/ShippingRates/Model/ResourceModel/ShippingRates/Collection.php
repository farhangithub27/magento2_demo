<?php

namespace Lmap\ShippingRates\Model\ResourceModel\ShippingRates;

use Lmap\ShippingRates\Model\ShippingRates;
use Lmap\ShippingRates\Model\ResourceModel\ShippingRates as ShippingRatesResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Start Track Shipping table rates collection
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(ShippingRates ::class, ShippingRatesResource::class);

    }

}