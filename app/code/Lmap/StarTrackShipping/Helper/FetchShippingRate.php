<?php

namespace Lmap\StarTrackShipping\Helper;

use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates\CollectionFactory;
use Psr\Log\LoggerInterface;

Class FetchShippingRate
{

    private $stRatesFactory;
    //private $stShippingFactory;


    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * This construct with double underscore is required to initialize other classes as LoggerInterface, CollectionFactory etc
     * This concept is taken from Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate.php;
     */
    public function __construct(LoggerInterface $logger,CollectionFactory $CollectionFactory)
    {
        $this->logger = $logger;
        $this->stRatesFactory = $CollectionFactory;
        //$this->stShippingFactory = $stShippiongFactory;
    }

    public function fetchRate()
    {
        $var ="2600";
        //$shippingmodel = $this->stShippingFactory->create();
        //$shippingcollection = $shippingmodel->getCollection();
        $postcode_rate_row = $this->stRatesFactory->create()->getItemsByColumnValue('postcode', $var);
        //$postcode_rate_row1 = $this->shippingCollectionFactory->create()->getConnection();

        //$this->logger->debug('Rates are: '.var_dump($postcode_rate_row));
        return $postcode_rate_row;

    }

}

