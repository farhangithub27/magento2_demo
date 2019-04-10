<?php
namespace Lmap\StarTrackShipping\Model\ResourceModel\Carrier;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Lmap\StarTrackShipping\Model\ResourceModel\Carrier\Shipping\CollectionFactory;
use Lmap\StarTrackShipping\Model\ResourceModel\Carrier\Shipping\Collection;
use function PHPSTORM_META\type;
use Psr\Log\LoggerInterface;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Symfony\Component\Console\Output\OutputInterface;
class Shipping extends AbstractDb
{

    private $shippingCollectionFactory;
    private $cmdoutput;

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
        $this->_init('lmap_shipping_tablerate', 'postcode');
    }

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * This construct with double underscore is required to initialize other classes as LoggerInterface, CollectionFactory etc
     * This concept is taken from Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate.php;
     */
    public function __construct(
        LoggerInterface $logger,CollectionFactory $CollectionFactory, Context $context
    ) {

        $this->logger = $logger;
        $this->shippingCollectionFactory = $CollectionFactory;
        parent::__construct($context);

    }

    /**
     * Return table rate array or false by rate request
     * @var \Lmap\StarTrackShipping\Model\ResourceModel\Carrier\Shipping\CollectionFactory $CollectionFactory
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array
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
        $this->logger->debug('Postcode int val: '. (int)$postcode . ' and weight float val: '. (float)$weight);
        $this->logger->debug('Converted');

        //echo('Postcode is: '. var_export($postcode,true) . 'and weight is: '. var_export($weight,true));

        $postcode_rate_row = $this->shippingCollectionFactory->create()->getItemsByColumnValue('postcode', (int)$postcode);
        $this->logger->debug('Rates type is: '.gettype($postcode_rate_row));
        $this->logger->debug('Rates are: '.var_export($postcode_rate_row,true));
        //echo("Rate Generated ". var_($postcode_rate_row));

        return $postcode_rate_row;
    }

}