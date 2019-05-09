<?php
namespace Lmap\StarTrackShipping\Model\Carrier;

/*
 * NOTICE : This file to work make sure
 * Shipping.php uses StarTrackRates resourceModel as it is and doesnt use StarTrackRatesFactory
 */
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Rate\ResultFactory;
use Lmap\StarTrackShipping\Model\Carrier\Shipping;
use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates;
use PHPUnit\Framework\TestCase;

class Shipping_getShippingPriceTest extends TestCase
{
    private $shippingMock;
    private $scopeConfigMock;
    private $rateErrorFactoryMock;
    private $loggerMock;
    private $rateResultFactoryMock;
    private $rateMethodFactoryMock;
    private $starTrackRatesMock; // This can be used as well depending upon which we are using in shipping.php model.
    private $starTrackRatesFactoryMock; // This can be used as well
    private $data = [];

    //public function setUp() // Work well with public
    protected function setUp()
    {

        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();

        $this->rateErrorFactoryMock = $this->getMockBuilder(ErrorFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        //$this->loggerMock = $this->createMock(\Psr\Log\LoggerInterface::class);
        // OR
        $this->loggerMock = $this->getMockBuilder(\Psr\Log\LoggerInterface::class)
            //->setMethods(null) // Don't use Mock Methods by using setMetods(null) use Stub Methods
            ->getMock();
        $this->loggerMock->expects($this->any())
            ->method('debug')
            ->willReturn('Stub Debug worked');


        $this->rateResultFactoryMock = $this->getMockBuilder(ResultFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateMethodFactoryMock = $this->getMockBuilder(MethodFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->starTrackRatesFactoryMock = $this->getMockBuilder(\Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRatesFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        //$this->shippingMock = $this->getMockBuilder(Lmap\StarTrackShipping\Model\Carrier\Shipping::class)
        /* Above line was giving error
            Error : Call to undefined method Mock_Shipping_45e8526f::getShippingPrice()
            /var/www/html/magento23demo/app/code/Lmap/StarTrackShipping/Test/Unit/Model/Carrier/ShippingTest.php:79
        */
        // Or below is working
        //$this->shippingMock = $this->getMockBuilder('Lmap\StarTrackShipping\Model\Carrier\Shipping')
        // or below is working
        $this->shippingMock = $this->getMockBuilder(Shipping::class)
            ->setMethods(['__construct'])// This needs to be used if disableOriginalConstructor() is used only.
            ->setConstructorArgs([$this->scopeConfigMock,$this->rateErrorFactoryMock,$this->loggerMock,$this->rateResultFactoryMock,$this->rateMethodFactoryMock,$this->starTrackRatesFactoryMock,$this->data])
            // OR
            //->disableOriginalConstructor() // This overides setConstructorArgs
            // or works with both disableOriginalConstructor() and setConstructorArgs
            ->getMock();

        /**
         * This simple code will run the test successfully. Above Mocks are created to try to run debug methods written within the code and not to interfere with the test.
         * PHP Fatal error:  Class Mock_LoggerInterface_a49cf619 contains 8 abstract methods and must therefore be declared abstract or implement the remaining methods
         * (Psr\Log\LoggerInterface::emergency, Psr\Log\LoggerInterface::alert, Psr\Log\LoggerInterface::critical, ...) in /var/www/html/magento23demo/vendor/phpunit/phpunit-mock-objects/src/Generator.php(264)
         * : eval()'d code on line 1

        $this->shippingObject = $this->getMockBuilder('Lmap\StarTrackShipping\Model\Carrier\Shipping')
            ->setMethods(null)
            ->getMock();
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
        $shipping_rate = $this->shippingMock->getShippingPrice($received_rate_array,$package_weight);
        $shipping_rate = $shipping_rate+($shipping_rate*0.147);
        //$test = $this->shippingMock->expects($this->any())
        //    ->method('getFinalPriceWithHandlingFee')
        //    ->with($shipping_rate)
        //    ->willReturn($shipping_rate+($shipping_rate*0.147));

        $this->assertEquals(floatval($expected_result),$shipping_rate);
        // $this->assertEquals(floatval($expected_result),$this->shippingMock->getShippingPrice($received_rate_array,$package_weight));
        // This approach requires that shipping.php file access getRate method through resource model and not through resource model factory.
        // If resource model factory is used then create() method (shippingMock->create()-> getShippingPrice() )will be require here which raises error that getShippingPrice(0 method doesnt exist.
        // In order to use Factory approach use Testing file ShippingTestComplete.php
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


    public function rateProvider()
    {
        return [
            // Expected Price is with 14.7% fuel adjustment
            ['15.4845',[['postcode'=>'2600','zone'=>'NC3','basic'=>'8.8','rate_per_kg'=>'0.47','minimum'=>'11.06']], 10],
            ['12.68582',[['postcode'=>'2600','zone'=>'NC3','basic'=>'8.8','rate_per_kg'=>'0.47','minimum'=>'11.06']], 1]
        ];
        // Ten bars ordered
    }


}