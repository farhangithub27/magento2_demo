<?php
namespace Lmap\StarTrackShipping\Model\Carrier;


use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

//use Lmap\ShippingRates\Helper\ShippingRateHelperFactory; // Not used at all
//use Lmap\ShippingRates\Model\ResourceModel\ShippingRatesFactory; // can be used if rates are fetched using seperate module Lmap_ShippingRates
//use Lmap\ShippingRates\Model\ResourceModel\ShippingRates\CollectionFactory as SRCollectionFactory; // Not used at all

use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRatesFactory;

//use Lmap\StarTrackShipping\Helper\FetchShippingRate; // Not used



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

    //protected $ratehelper;
    //private $shippingRateHelperFactory;

    /**
     * @var \Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRatesFactory
     */
    private $starTrackRatesFactory;
    /**
     * @var \Lmap\ShippingRates\Model\ResourceModel\ShippingRatesFactory
     */
    private $shippingRateFactory;
    protected $_logger;

    /**
     * @var \Lmap\StarTrackShipping\Model\ResourceModel\Carrier\ShippingFactory
     */
    protected $stRateCollectionFactory;

    protected $_eventPrefix = 'lmap_shipping_event';// This even prefix will be used for coding event (observer) based logging latter on.
    // This prefix is used by AbstractModel to generate events.

    /**
     * Shipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param StarTrackRatesFactory $starTrackRatesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        StarTrackRatesFactory $starTrackRatesFactory,
        //ShippingRatesFactory $shippingRateFactory,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->starTrackRatesFactory = $starTrackRatesFactory;
        //$this->shippingRateFactory = $shippingRateFactory;
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

    public function getShippingPrice($rate_array,$package_weight)
    {
        $this->_logger->debug('getShippingPrice method called !');
        $this->_logger->debug('No need for var_dump, var_export  and echo methods as xdebug has been configured along with phpstorm to see the variables.');
        $basic_rate = floatval($rate_array[0]['basic']);
        $rate_per_kg = floatval($rate_array[0]['rate_per_kg']);
        $minimum_rate = floatval($rate_array[0]['minimum']);

        $weight_based_rate = $basic_rate + ($rate_per_kg * floatval($package_weight));
        $this->_logger->debug('Basic Rate is: '. var_export($basic_rate,true).
            ' and Rate Per Kg: '.$rate_per_kg.
            ' and Minimum Rate: '.$minimum_rate.
            ' and Weight Based Rate: '.$weight_based_rate.
            ' and package weight:' .floatval($package_weight));
        if ($weight_based_rate<$minimum_rate){
            return  $shipping_rate = $minimum_rate;
        }
        else{
            return $shipping_rate = $weight_based_rate;
        }
    }
    /**
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|Result|null
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


        //$amount = $this->shippingRateHelperFactory->create()->fetchRate(2600);
        //$amount = $this->stRateCollectionFactory->create()->getItemsByColumnValue('postcode',2600);

        //$received_rate_array = $this->shippingRateFactory->create()->getRate($request); // Worked successfully

        $postcode = $request->getDestPostcode();

        $received_rate_array = $this->starTrackRatesFactory->create()->getRate($postcode);

        $this->_logger->debug('Received Rates are: '.var_export($received_rate_array[0]['basic'],true));
        $packageWeight = $request->getPackageWeight();
        $shipping_rate = $this->getShippingPrice($received_rate_array,$packageWeight);
        /**
        $basic_rate = floatval($received_rate_array[0]['basic']);

        $rate_per_kg = floatval($received_rate_array[0]['rate_per_kg']);
        $minimum_rate = floatval($received_rate_array[0]['minimum']);

        $weight_based_rate = $basic_rate + ($rate_per_kg * floatval($packageWeight));
        $this->_logger->debug('Basic Rate is: '. var_export($basic_rate,true).
                                ' and Rate Per Kg: '.$rate_per_kg.
                                ' and Minimum Rate: '.$minimum_rate.
                                ' and Weight Based Rate: '.$weight_based_rate.
                                ' and package weight:' .floatval($packageWeight));
        if ($weight_based_rate<$minimum_rate){
            $shipping_rate = $minimum_rate;
        }
        else{
            $shipping_rate = $weight_based_rate;
        }
        */
        $method->setPrice($shipping_rate);
        $method->setCost($shipping_rate);

        $result->append($method);

        return $result;
    }


}
