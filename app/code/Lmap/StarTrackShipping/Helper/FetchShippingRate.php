<?php

namespace Lmap\StarTrackShipping\Helper;

use Lmap\StarTrackShipping\Model\ResourceModel\Carrier\StarTrackRates\CollectionFactory;
use Lmap\StarTrackShipping\Model\Carrier\StarTrackRatesFactory;
use Lmap\StarTrackShipping\Model\Carrier\StarTrackRates;
//use Magento\Framework\DB\Helper\AbstractHelper;
//use Magento\Framework\App\Helper\AbstractHelper;
use Psr\Log\LoggerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;

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
    public function __construct(LoggerInterface $logger,CollectionFactory $collectionFactory,StarTrackRatesFactory $starTrackRatesFactory,
                                StarTrackRates $starTrackRates)
    {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
        $this->starTrackRatesFactory = $starTrackRatesFactory;
        $this->starTrackRates = $starTrackRates;

    }

    public function fetchRate($var)
    {

        $postcode_rate_row = $this->collectionFactory->create()->getItemsByColumnValue('postcode',2600);
        $collection = $this->collectionFactory->create();
        //$postcode_rate_row->getConnection();
        $collection->addFieldToFilter('postcode',array('eq'=>2600));
        $this->logger->debug('collection query is: '.$collection->getSelect()->__toString());
        $code = $collection->getColumnValues('postcode');
        //getItemsByColumnValue('postcode', 2600);
        $this->logger->debug('column values are: '.var_dump($code). 'post code is: '. var_dump($postcode_rate_row));
        /**
        $collection1 = $this->starTrackRatesFactory->create()->getCollection();
        $collection1->addFieldToSelect('*')->addFieldToFilter('postcode',array('eq'=>2600));
        $this->logger->debug('collection1 query is: '. $collection1->getSelect()->__toString());
        foreach ($collection1 as $rate){
            $this->logger->debug('Forloop Rate are:');
            $this->logger->debug(var_dump($rate->getData));
        }
        */
        //$this->logger->debug('Rates are $postcode_rate_row with getData(): '.var_dump($postcode_rate_row->getData()));
        return $postcode_rate_row;

    }

}

