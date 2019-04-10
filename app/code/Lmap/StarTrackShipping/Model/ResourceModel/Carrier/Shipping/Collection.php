<?php

namespace Lmap\StartrackShipping\Model\ResourceModel\Carrier\Shipping;


/**
 * Start Track Shipping table rates collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Directory/country table name
     *
     * @var string
     */
    protected $countryTable;

    /**
     * Directory/country_region table name
     *
     * @var string
     */
    protected $regionTable;

    /**
     * Define resource model and item
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Lmap\StarTrackShipping\Model\Carrier\Shipping',
            'Lmap\StarTrackShipping\Model\ResourceModel\Carrier\Shipping'
        );
        #$this->countryTable = $this->getTable('directory_country');
        #$this->regionTable = $this->getTable('directory_country_region');
    }




}