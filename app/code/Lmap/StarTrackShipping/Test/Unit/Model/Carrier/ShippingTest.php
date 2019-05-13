<?php
namespace Lmap\StarTrackShipping\Model\Carrier;

use Lmap\StarTrackShipping\Model\Carrier\Shipping;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;

use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates;
use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRatesFactory;

use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;

use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ShippingTest extends TestCase
{
    /**
     *
     */
    private $shippingMock;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfigMock;
    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $rateErrorFactoryMock;

    private $loggerMock;

    private $rateResultFactoryMock;

    private $rateMethodFactoryMock;

    private $starTrackRatesMock; // This can be used as well depending upon which we are using in shipping.php model.
    private $starTrackRatesFactoryMock; // This can be used as well

    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    private $helper;

    private $data = [];


    protected function setUp()
    {

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['create', 'isSetFlag', 'getValue'])
            ->getMock();

        $this->rateErrorFactoryMock = $this->getMockBuilder(ErrorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        //OR
        //$this->loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);

        $this->rateResultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $this->rateMethodFactoryMock = $this->getMockBuilder(MethodFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();


        $this->starTrackRatesFactoryMock = $this->getMockBuilder(StarTrackRatesFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create','getRate'])
            ->getMock();


        //  This fixture is mved to the test methods because its used differently in both.
        //$this->shippingMock = $this->getMockBuilder(Shipping::class)
        //    ->setMethods(['__construct','getRate','getShippingPrice']) // Stubbing the constructor, getRate and getShippingPrice methods
                // Method collectRates will be mocked
        //    ->setConstructorArgs([$this->scopeConfigMock,$this->rateErrorFactoryMock,$this->loggerMock,$this->rateResultFactoryMock,$this->rateMethodFactoryMock,$this->starTrackRatesFactoryMock])
        //    ->getMock();
            //->disableOriginalConstructor() // This overides setConstructorArgs and if used then following error comes
            /*
             * Error : Call to a member function isSetFlag() on null
             * /var/www/html/magento23demo/vendor/magento/module-shipping/Model/Carrier/AbstractCarrier.php:155
             * /var/www/html/magento23demo/app/code/Lmap/StarTrackShipping/Model/Carrier/Shipping.php:150
             * /var/www/html/magento23demo/app/code/Lmap/StarTrackShipping/Test/Unit/Model/Carrier/ShippingTestComplete.php:183
             *
             */
        // OR
        /* // This approach of getting Object of Shipping Class using ObjectManager requires to Mock the class in test function testcollectRates but that mock is giving null with serCarrier method.
        $this->helper = new \Magento\Framework\TestFramework\Unit\Helper\ObjectManager($this);
        $this->shippingMock = $this->helper->getObject(
            Shipping::class,
            [
                'scopeConfig' => $this->scopeConfigMock,
                'rateErrorFactory' => $this->rateErrorFactoryMock,
                'logger' => $this->loggerMock,
                'rateResultFactory' => $this->rateResultFactoryMock,
                'resultMethodFactory' => $this->rateMethodFactoryMock,
                'starTrackRatesFactory' => $this->starTrackRatesFactoryMock
            ]
        );
        */

    }

    /**
     * @param $postcode
     * @param $received_rate_array
     * @param $package_weight
     * @dataProvider rateProvider
     * Each element of array in dataprovider corresponds to its respective input argument in testgetShippingPrice
     */
    public function testgetShippingPrice($expected_result,$received_rate_array,$package_weight)
    {
        // Since getShippingPrice is used differently in both test methods hence shipping fixture is moved inside the tests methods.
        $this->shippingMock = $this->getMockBuilder(Shipping::class)
            ->setMethods(['__construct'])// This needs to be used if disableOriginalConstructor() is used only.
            ->setConstructorArgs([$this->scopeConfigMock,$this->rateErrorFactoryMock,$this->loggerMock,$this->rateResultFactoryMock,$this->rateMethodFactoryMock,$this->starTrackRatesFactoryMock])
            // OR
            //->disableOriginalConstructor() // This overides setConstructorArgs
            // or works with both disableOriginalConstructor() and setConstructorArgs
            ->getMock();


        $shipping_rate = $this->shippingMock->getShippingPrice($received_rate_array,$package_weight);
        $shipping_rate = $shipping_rate+($shipping_rate*0.147);

        $this->assertEquals(floatval($expected_result),$shipping_rate);
        // $this->assertEquals(floatval($expected_result),$this->shippingMock->getShippingPrice($received_rate_array,$package_weight));
        // This approach requires that shipping.php file access getRate method through resource model and not through resource model factory.
        // If resource model factory is used then create() method (shippingMock->create()-> getShippingPrice() )will be require here which raises error that getShippingPrice(0 method doesnt exist.
        // In order to use Factory approach use Testing file ShippingTestComplete.php
    }

    public function rateProvider()
    {
        return [
            // Expected Price is with 14.7% fuel adjustment
            ['15.4845',[['postcode'=>'2600','zone'=>'NC3','basic'=>'8.8','rate_per_kg'=>'0.47','minimum'=>'11.06']], 10],
            ['12.68582',[['postcode'=>'2600','zone'=>'NC3','basic'=>'8.8','rate_per_kg'=>'0.47','minimum'=>'11.06']], 1]
        ];
        // Ten bars ordered
    }

    /*
     * @param $shippingPrice
     * @dataProvider priceProvider
     */
    public function testCollectRates()
    {

        $shippingPrice = 15; // shippingprice
        $received_rate = [['postcode'=>'2600','zone'=>'NC3','basic'=>'8.8','rate_per_kg'=>'0.47','minimum'=>'11.06']];

        $this->shippingMock = $this->getMockBuilder(Shipping::class)
            ->setMethods(['__construct','getRate','getShippingPrice']) // Stubbing the constructor, getRate and getShippingPrice methods
            // Method collectRates will be mocked
            ->setConstructorArgs([$this->scopeConfigMock,$this->rateErrorFactoryMock,$this->loggerMock,$this->rateResultFactoryMock,$this->rateMethodFactoryMock,$this->starTrackRatesFactoryMock])
            ->getMock();

        $request = $this->getMockBuilder(\Magento\Quote\Model\Quote\Address\RateRequest::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAllItems', 'getPackageQty','getPackageWeight'])
            ->getMock();

        $item = $this->getMockBuilder(\Magento\Sales\Model\Order\Item::class)
            ->disableOriginalConstructor()
            ->setMethods(['getProduct', 'getParentItem', 'getHasChildren', 'isShipSeparately', 'getChildren',
                    'getQty', 'getFreeShipping', 'getBaseRowTotal']
            )
            ->getMock();

        $product = $this->getMockBuilder(\Magento\Catalog\Model\Product::class)
            ->disableOriginalConstructor()
            ->setMethods(['isVirtual'])
            ->getMock();
        $product->expects($this->any())->method('isVirtual')->willReturn(false);

        /*
         * // This section is required for the Shipping Object created via objectManager in setUp method.
        //These lines were created as per logic from tableratetest file but these lines were causing return values to be null.
        $shipping = $this->getMockBuilder(Shipping::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShippingPrice','getRate'])
            ->getMock();

        $shipping->expects($this->any())->method('getRate')->willReturn($received_rate);
        $this->starTrackRatesFactoryMock->expects($this->any())->method('create')->willReturn($shipping);
        $shipping->expects($this->any())->method('getShippingPrice')->willReturn($rate);
        */
        $this->shippingMock->expects($this->any())->method('getRate')->willReturn($received_rate);
        $this->starTrackRatesFactoryMock->expects($this->any())->method('create')->willReturn($this->shippingMock);
        $this->shippingMock->expects($this->any())->method('getShippingPrice')->willReturn($shippingPrice);

        $this->scopeConfigMock->expects($this->any())->method('isSetFlag')->willReturn(true);

        $method = $this->getMockBuilder(Method::class)
            ->disableOriginalConstructor()
            ->setMethods(['setCarrier', 'setCarrierTitle', 'setMethod', 'setMethodTitle', 'setPrice', 'setCost'])
            ->getMock();
        $this->rateMethodFactoryMock->expects($this->once())->method('create')->willReturn($method);

        $result = $this->getMockBuilder(Result::class)
            ->disableOriginalConstructor()
            ->setMethods(['append'])
            ->getMock();
        $this->rateResultFactoryMock->expects($this->once())->method('create')->willReturn($result);

        $item->expects($this->any())->method('getProduct')->willReturn($product);
        $item->expects($this->any())->method('getFreeShipping')->willReturn(1);
        $item->expects($this->any())->method('getQty')->willReturn(1);

        $request->expects($this->any())->method('getAllItems')->willReturn([$item]);
        $request->expects($this->any())->method('getPackageQty')->willReturn(1);
        $request->expects($this->any())->method('getPackageWeight')->willReturn(1);

        $returnPrice = null;
        $method->expects($this->once())->method('setPrice')->with($this->captureArg($returnPrice));

        $returnCost = null;
        $method->expects($this->once())->method('setCost')->with($this->captureArg($returnCost));

        $returnMethod = null;
        $result->expects($this->once())->method('append')->with($this->captureArg($returnMethod));

        //echo is_null($request) ? 'yes':'No';
        $returnResult = $this->shippingMock->collectRates($request);
        //print_r('Return Price: '.$returnPrice . ' and Return Cost: '. $returnCost.' ');
        $this->assertEquals($shippingPrice, $returnPrice);
        $this->assertEquals($shippingPrice, $returnCost);
        $this->assertEquals($method, $returnMethod);
        $this->assertEquals($result, $returnResult);

    }

    /**
     * Method borrowed from vendor/magento/module-offline-shipping/Test/Unit/Model/Carrier/TablerateTest.php
     * Captures the argument and saves it in the given variable
     *
     * @param $captureVar
     * @return \PHPUnit\Framework\Constraint\Callback
     */
    private function captureArg(&$captureVar)
    {
        return $this->callback(
            function ($argToMock) use (&$captureVar) {
                $captureVar = $argToMock;
                //print_r("This is running". var_dump($captureVar));
                return true;
            }
        );
    }

}