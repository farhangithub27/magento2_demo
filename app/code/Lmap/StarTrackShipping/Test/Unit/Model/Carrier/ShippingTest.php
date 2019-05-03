<?php
namespace Lmap\StarTrackShipping\Model\Carrier;

use Lmap\StarTrackShipping\Model\Carrier\Shipping;
use Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates;
use PHPUnit\Framework\TestCase;

class ShippingTest extends TestCase
{
    private $shippingObject;
    private $rateResourceObject;
    public function setUp()
    {
        $this->shippingObject = $this->getMockBuilder('Lmap\StarTrackShipping\Model\Carrier\Shipping')
            ->disableOriginalConstructor()
            ->getMock();


    }

    public function testShippingPrice($postcode,$received_rate_array,$package_weight)
    {


        $this->rateResourceObject = $this->getMockBuilder('Lmap\StarTrackShipping\Model\ResourceModel\StarTrackRates')
            ->disableOriginalConstructor()
            ->getMock();
        $this->rateResourceObject->expects($this->any())
            ->method('getRate')
            ->with($postcode)
            ->willReturn($received_rate_array);
    }

    public function rateProvider()
    {
        retrun [
            [
                2600,[]
            ]
        ];
    }

}