<?php

namespace Lmap\StartrackShipping\Model\ResourceModel\Shipping;

use Lmap\StarTrackShipping\Model\Carrier\Shipping;
use Lmap\StarTrackShipping\Model\ResourceModel\Shipping as LmapShippingResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Start Track Shipping table rates collection
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(Shipping ::class, LmapShippingResource::class);

    }

}