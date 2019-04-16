<?php

namespace Lmap\StartrackShipping\Model\ResourceModel\Carrier\StarTrackRates;

use Lmap\StarTrackShipping\Model\Carrier\StarTrackRates;
use Lmap\StarTrackShipping\Model\ResourceModel\Carrier\StarTrackRates as StarTrackRatesResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Start Track Shipping table rates collection
 */
class Collection extends AbstractCollection
{

    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init(StarTrackRates ::class, StarTrackRatesResource::class);

    }

}