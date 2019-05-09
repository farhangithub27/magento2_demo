<?php
namespace Lmap\StarTrackShipping\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StarTrackRates extends AbstractDb
{

    protected $logger;

    /**
     * StarTrackRates constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        $connectionName = null
    ){
        parent::__construct($context, $connectionName);
        $this->logger = $logger;
    }

    /**
     * Connecting with Database table
     * This is special construct method with single underscore
     */
    protected function _construct()
    {
        $this->_init('lmap_shipping_tablerate', 'id');
    }


    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getRate($postcode)
    {
        //$postcode = $request->getDestPostcode();

        $connection = $this->getConnection(); // getConnection method is method  of AbstractDb class
        $sql = $connection->select()->from($this->getMainTable())->where('postcode =?',$postcode);
        $result = $connection->fetchAll($sql);
        //$this->logger->debug('The select with condition is:');
        //$this->logger->debug(var_export($result,true));

        return $result;
    }

}