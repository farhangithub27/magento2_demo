<?php
namespace Lmap\StarTrackShipping\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates\CollectionFactory;
use Lmap\StarTrackShipping\Helper\FetchShippingRate;

class Shipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'startrackshipping';

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $rateMethodFactory;

    protected $ratehelper;

    protected $_logger;

    /**
     * @var \Lmap\StarTrackShipping\Model\ResourceModel\Carrier\ShippingFactory
     */
    protected $stRateFactory;

    protected $_eventPrefix = 'lmap_shipping_event';// This even prefix will be used for coding event (observer) based logging latter on.
    // This prefix is used by AbstractModel to generate events.

    /**
     * Shipping constructor.
     *
     * @param \
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates\CollectionFactory $stRatesFactory
     * @param array                                                       $data
     */
    public function __construct(
        CollectionFactory $stRatesFactory,
        FetchShippingRate $fetchShippingRate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->stRateFactory = $stRatesFactory;
        $this->ratehelper = $fetchShippingRate;
        $this->_logger = $logger;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * get allowed methods
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return float
     */
    private function getShippingPrice(RateRequest $request)
    {
        $postcode = $request->getDestPostcode();
        $weight = $request->getPackageWeight();
        //print_r("Rate Working");
        //$this->cmdoutput->writeln('<info>Postcode is:  ' . var_dump($postcode) . 'and weight is: '. var_dump($weight).'</info>');
        $this->_logger->debug('Item was created');
        $this->_logger->debug('Postcode is: '. var_export($postcode,true) . ' and weight is: '. var_export($weight,true));
        $this->_logger->debug('Postcode type: '. gettype($postcode) . ' and weight type is: '. gettype($weight));
        $this->_logger->debug('Postcode val: '. $postcode . ' and weight val: '. $weight);
        $this->_logger->debug('Postcode int val: '. (int)$postcode . ' and weight float val: '. (float)$weight);
        $methods_avail = get_class_methods($this->stRateFactory->create());
        //print_r(get_class_methods($methods_avail));
        $this->_logger->debug('Methods are  : '.var_dump($methods_avail));
        $postcode_rate_row = $this->stRateFactory->create()->getItemByColumnValue('postcode', (int)$postcode);

        //$postcode_rate_row1 = $this->stRateFactory->create()->getConnection();
        //$rates = $postcode_rate_row1->getTableName('lmap_shipping_tablerate');
        //$this->_logger->debug('Rates table is : '.var_dump($rates));

        $postcode_rate_row1 = $this->ratehelper->fetchRate();
        $this->_logger->debug('Rates type is: '.gettype($postcode_rate_row1));
        $this->_logger->debug('Rates are empty: '. empty($postcode_rate_row1));

        $this->_logger->debug('Rates received: '.var_dump($postcode_rate_row1));
        // var_export only works here with json_encode method otherwise gives var_export circular reference issue and hence Allowed memory size of 792723456 bytes exhausted problem.
        //echo("Rate Generated ". var_($postcode_rate_row));

        $shippingPrice = $this->getFinalPriceWithHandlingFee($postcode_rate_row);

        return $shippingPrice;
    }

    /**
     * @param RateRequest $request
     * @return bool|Result
     */
    public function collectRates(RateRequest $request)
        /**
         * Function collectRates has a parameter $request which is the instance of RateRequest class.
         * This Magento\Quote\Model\Quote\Address\RateRequest class contains all information about the items
         * in the cart: quote, weight, shipping/billing address, etc
         */
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $amount = $this->getShippingPrice($request);

        $method->setPrice($amount);
        $method->setCost($amount);

        $result->append($method);

        return $result;
    }


}
