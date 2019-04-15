<?php
namespace Lmap\StarTrackShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StarTrackRates extends AbstractDb
{

    /**
     * Connecting with Database table
     * This is special construct method with single underscore
     */
    protected function _construct()
    {
        $this->_init('lmap_shipping_tablerate', 'id');
    }

}