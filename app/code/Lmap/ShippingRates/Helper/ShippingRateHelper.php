<?php

namespace Lmap\ShippingRates\Helper;

use Lmap\ShippingRates\Model\ResourceModel\ShippingRates\CollectionFactory as ShippingRatesCollectionFactory;
use Lmap\ShippingRates\Model\ShippingRatesFactory;
use Lmap\ShippingRates\Model\ShippingRates;
//use Magento\Framework\DB\Helper\AbstractHelper;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;
//use Magento\Framework\Model\ResourceModel\Db\Context;

Class ShippingRateHelper extends AbstractHelper
{

    private $shippingRatesCollectionFactory;
    private $shippingRatesFactory;
    private $shippingRates;


    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * This construct with double underscore is required to initialize other classes as LoggerInterface, CollectionFactory etc
     * This concept is taken from Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate.php;
     */
    public function __construct(LoggerInterface $logger,ShippingRatesCollectionFactory $shippingRatesCollectionFactory,ShippingRatesFactory $shippingRatesFactory,
                                ShippingRates $shippingRates,Context $context)
    {
        $this->logger = $logger;
        $this->shippingRatesCollectionFactory = $shippingRatesCollectionFactory;
        $this->shippingRatesFactory = $shippingRatesFactory;
        $this->shippingRates = $shippingRates;
        parent::__construct($context);

    }

    public function fetchRate($var)
    {

        $postcode_rate_row = $this->shippingRatesCollectionFactory->create()->getItemsByColumnValue('postcode',2600);
        $collection = $this->shippingRatesCollectionFactory->create();
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

