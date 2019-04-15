<?php

namespace Lmap\StarTrackShipping\Helper;

use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates\CollectionFactory;
use Lmap\StarTrackShipping\Model\StarTrackRatesFactory;
use Lmap\StarTrackShipping\Model\StarTrackRates;
use Psr\Log\LoggerInterface;

Class FetchShippingRate
{

    private $collectionFactory;
    private $starTrackRatesFactory;
    private $starTrackRates;


    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * This construct with double underscore is required to initialize other classes as LoggerInterface, CollectionFactory etc
     * This concept is taken from Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate.php;
     */
    public function __construct(LoggerInterface $logger,CollectionFactory $collectionFactory,StarTrackRatesFactory $starTrackRatesFactory,StarTrackRates $starTrackRates)
    {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->starTrackRatesFactory = $starTrackRatesFactory;
        $this->starTrackRates = $starTrackRates;

    }

    public function fetchRate()
    {
        $var =2600;
        $postcode_rate_row = $this->collectionFactory->create()->getItemByColumnValue('zone','NC3');
        $collection = $this->collectionFactory->create();
        //$postcode_rate_row->getConnection();
        $collection->addFieldToFilter('postcode',array('eq'=>2600));
        $this->logger->debug('collection query is: '.$collection->getSelect()->__toString());
        $code = $collection->getColumnValues('postcode');
        //getItemsByColumnValue('postcode', 2600);
        $this->logger->debug('column values are: '.var_dump($code). 'post code is: '. var_dump($postcode_rate_row));
        /**
        $collection = $this->stRatesFactory->create()->getCollection();
        $collection->addFieldToSelect('*')->addFieldToFilter('postcode',array('eq'=>2600));
        $this->logger->debug('query is: '. $collection->getSelect()->__toString());
        foreach ($collection as $rate){
            $this->logger->debug('Rate is:');
            $this->logger->debug(var_dump($collection));
            $this->logger->debug(var_dump($rate));
        }
        */

        //$this->logger->debug('Rates are: '.var_dump($postcode_rate_row));
        return $postcode_rate_row;

    }

}

