<?php
namespace Lmap\StarTrackShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Lmap\StarTrackShipping\Model\ResourceModel\Shipping\CollectionFactory;
use Lmap\StarTrackShipping\Model\ResourceModel\Shipping\Collection;
use function PHPSTORM_META\type;
use Psr\Log\LoggerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Symfony\Component\Console\Output\OutputInterface;
use Lmap\StarTrackShipping\Helper\ShippingRate;
class Shipping extends AbstractDb
{

    private $collectionFactory;
    private $shippingrate;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Connecting with Database table
     * This is special construct method with single underscore
     */
    protected function _construct()
    {
        $this->_init('lmap_shipping_tablerate', 'id');
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * This construct with double underscore is required to initialize other classes as LoggerInterface, CollectionFactory etc
     * This concept is taken from Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate.php;
     */
    public function __construct(
        LoggerInterface $logger,CollectionFactory $CollectionFactory, Context $context,ShippingRate $shippingRate
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $CollectionFactory;
        $this->shippingrate = $shippingRate;
        parent::__construct($context);

    }

    /**
     * Return table rate array or false by rate request
     * @var \Lmap\StarTrackShipping\Model\ResourceModel\Carrier\Shipping\CollectionFactory $CollectionFactory
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array|string
     */
    public function getRate(RateRequest $request)
    {
        // $connection = $this->getConnection();
        $postcode = $request->getDestPostcode();
        $weight = $request->getPackageWeight();
        //print_r("Rate Working");
        //$this->cmdoutput->writeln('<info>Postcode is:  ' . var_dump($postcode) . 'and weight is: '. var_dump($weight).'</info>');
        $this->logger->debug('Item was created');
        $this->logger->debug('Postcode is: '. var_export($postcode,true) . ' and weight is: '. var_export($weight,true));
        $this->logger->debug('Postcode type: '. gettype($postcode) . ' and weight type is: '. gettype($weight));
        $this->logger->debug('Postcode val: '. $postcode . ' and weight val: '. $weight);
        $this->logger->debug('Postcode int val: '. (int)$postcode . ' and weight float val: '. (float)$weight);
        $postcode_rate_row = $this->collectionFactory->create()->getItemsByColumnValue('postcode', '2600');

        $postcode_rate_row1 = $this->collectionFactory->create()->getConnection();
        $rates = $postcode_rate_row1->getTableName('lmap_shipping_tablerate');
        $this->logger->debug('Rates table is : '.var_dump($rates));

        //$postcode_rate_row = $this->shippingrate->fetchRate(intval($postcode));
        $this->logger->debug('Rates type is: '.gettype($postcode_rate_row));
        $this->logger->debug('Rates are empty: '. empty($postcode_rate_row));

        $this->logger->debug('Rates received: '.var_dump($postcode_rate_row));
        // var_export only works here with json_encode method otherwise gives var_export circular reference issue and hence Allowed memory size of 792723456 bytes exhausted problem.
        //echo("Rate Generated ". var_($postcode_rate_row));

        return $postcode_rate_row;
    }

}